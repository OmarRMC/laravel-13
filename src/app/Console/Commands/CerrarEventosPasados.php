<?php

namespace App\Console\Commands;

use App\Models\Evento;
use Illuminate\Console\Command;

class CerrarEventosPasados extends Command
{
    protected $signature = 'app:cerrar-eventos-pasados';

    protected $description = 'Marca como cerrado todo evento publicado cuya fecha ya paso';

    public function handle(): int
    {    
        // termina_el es nullable: si no hay fecha de fin, se usa inicia_el como referencia.
        $afectados = Evento::publicado()
            ->where(function ($query) {
                $query->where('termina_el', '<', now())
                    ->orWhere(function ($query) {
                        $query->whereNull('termina_el')->where('inicia_el', '<', now());
                    });
            })
            ->update(['estado' => 'cerrado']);

        $this->info("{$afectados} evento(s) cerrado(s).");
    
        return self::SUCCESS;
    }
}
