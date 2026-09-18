<?php

namespace App\Console\Commands;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Uso en terminal:
 *
 *   php artisan app:generar-eventos admin@socef.test
 *   php artisan app:generar-eventos admin@socef.test 20
 *   php artisan app:generar-eventos 1 5
 *   php artisan app:generar-eventos admin@socef.test 3 --manana
 *   php artisan app:generar-eventos admin@socef.test 3 --manana --inscritos=5
 *   php artisan app:generar-eventos admin@socef.test 3 --vencido
 */
class GenerarEventos extends Command
{
    protected $signature = 'app:generar-eventos
        {usuario : ID o email del organizador}
        {cantidad=10 : Cantidad de eventos a generar}
        {--manana : Genera eventos que inician manana con inscritos, para probar app:recordar-eventos y el correo de recordatorio}
        {--vencido : Genera eventos ya finalizados pero publicados, para probar app:cerrar-eventos-pasados}
        {--inscritos=3 : Inscritos confirmados por evento a agregar con --manana}';

    protected $description = 'Genera eventos de prueba (via factory), incluyendo escenarios para probar el cierre por cron y los recordatorios por correo';

    public function handle(): int
    {
        $usuario = $this->argument('usuario');
        $cantidad = (int) $this->argument('cantidad');

        if ($cantidad < 1) {
            $this->error('La cantidad debe ser un numero entero mayor a 0.');

            return self::FAILURE;
        }

        $user = is_numeric($usuario)
            ? User::find($usuario)
            : User::where('email', $usuario)->first();

        if (! $user) {
            $this->error("No se encontro un usuario con el identificador '{$usuario}'.");

            return self::FAILURE;
        }

        if ($this->option('manana')) {
            $this->generarParaRecordatorio($user, $cantidad, (int) $this->option('inscritos'));
        } elseif ($this->option('vencido')) {
            $this->generarVencidos($user, $cantidad);
        } else {
            Evento::factory()->count($cantidad)->create(['user_id' => $user->id]);
            $this->info("{$cantidad} evento(s) generado(s) para {$user->name} ({$user->email}).");
        }

        return self::SUCCESS;
    }

    /**
     * `app:recordar-eventos` busca publicados cuyo inicia_el caiga exactamente
     * manana (whereDate). Cada evento lleva inscritos confirmados (a quienes
     * SI les toca el correo), mas uno pendiente y uno cancelada para verificar
     * que el job los excluye (solo filtra estado = 'confirmada').
     */
    private function generarParaRecordatorio(User $user, int $cantidad, int $inscritos): void
    {
        $manana = now()->addDay();

        $eventos = Evento::factory()->count($cantidad)->create([
            'user_id' => $user->id,
            'estado' => 'publicado',
            'inicia_el' => $manana->clone()->setTime(10, 0),
            'termina_el' => $manana->clone()->setTime(13, 0),
        ]);

        foreach ($eventos as $evento) {
            $confirmados = User::factory()->count($inscritos)->create();
            $pendiente = User::factory()->create();
            $cancelado = User::factory()->create();

            // OJO: nada de spread (...) aqui: con claves enteras (user_id) reindexa
            // el array y attach() termina insertando user_id 0, 1, 2... en vez del real.
            $inscripciones = $confirmados->mapWithKeys(fn (User $u) => [
                $u->id => ['codigo' => Str::upper(Str::random(12)), 'estado' => 'confirmada', 'asistio' => false],
            ])->all();
            $inscripciones[$pendiente->id] = ['codigo' => Str::upper(Str::random(12)), 'estado' => 'pendiente', 'asistio' => false];
            $inscripciones[$cancelado->id] = ['codigo' => Str::upper(Str::random(12)), 'estado' => 'cancelada', 'asistio' => false];

            $evento->inscritos()->attach($inscripciones);

            $this->info("Evento '{$evento->titulo}' listo para manana con {$inscritos} confirmado(s) (+1 pendiente, +1 cancelada).");
        }
    }

    /**
     * `app:cerrar-eventos-pasados` cubre dos ramas: termina_el < now(), o
     * termina_el null con inicia_el < now(). Se reserva un evento para la
     * segunda rama (estado() virtual) cuando se piden 2 o mas.
     */
    private function generarVencidos(User $user, int $cantidad): void
    {
        $sinFechaFin = $cantidad > 1 ? 1 : 0;
        $conFechaFin = $cantidad - $sinFechaFin;

        Evento::factory()->count($conFechaFin)->pasado()->create([
            'user_id' => $user->id,
        ]);

        if ($sinFechaFin > 0) {
            Evento::factory()->count($sinFechaFin)->pasado()->virtual()->create([
                'user_id' => $user->id,
            ]);
        }

        $this->info("{$cantidad} evento(s) vencido(s) y publicado(s) generado(s) para {$user->name} ({$user->email}).");
    }
}
