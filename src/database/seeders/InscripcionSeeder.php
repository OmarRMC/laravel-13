<?php

namespace Database\Seeders;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class InscripcionSeeder extends Seeder
{
    public function run(): void
    {
        $evento = Evento::where('slug', EventoSeeder::SLUG_LARAVEL)->firstOrFail();

        $emails = [
            UserSeeder::EMAIL_PARTICIPANTE,
            UserSeeder::EMAIL_PARTICIPANTE_2,
            UserSeeder::EMAIL_PARTICIPANTE_3,
        ];

        $participantes = User::whereIn('email', $emails)
            ->where('activo', true)
            ->get()
            ->keyBy('email');

    
        throw_if(
            $participantes->count() < 3,
            RuntimeException::class,
            'Faltan participantes fijos; revisa UserSeeder.'
        );

        /*
         * Una situacion distinta por fila:
         *   - confirmada sin asistencia -> el certificado devuelve 403
         *   - confirmada con asistencia -> el organizador ve la lista con datos
         *   - cancelada                 -> libera plaza, no cuenta para el cupo
         *
         * `codigo` es varchar(12) unique: por eso codigos cortos y fijos.
         */
        $evento->inscritos()->attach([
            $participantes[UserSeeder::EMAIL_PARTICIPANTE]->id   => ['codigo' => 'LAR13-0001', 'estado' => 'confirmada', 'asistio' => false],
            $participantes[UserSeeder::EMAIL_PARTICIPANTE_2]->id => ['codigo' => 'LAR13-0002', 'estado' => 'confirmada', 'asistio' => true],
            $participantes[UserSeeder::EMAIL_PARTICIPANTE_3]->id => ['codigo' => 'LAR13-0003', 'estado' => 'cancelada',  'asistio' => false],
        ]);
    }
}
