<?php

namespace App\Console\Commands;

use App\Jobs\EnviarRecordatoriosEvento;
use App\Models\Evento;
use Illuminate\Console\Command;

class RecordarEventos extends Command
{
    protected $signature = 'app:recordar-eventos {--dias=1}';

    protected $description = 'Encola recordatorios para los eventos que inician pronto';

    public function handle(): int
    {
        $dias = (int) $this->option('dias');
        $eventos = Evento::publicado()
            ->whereDate('inicia_el', now()->addDays($dias)->toDateString())
            ->get();

        foreach ($eventos as $evento) {
            EnviarRecordatoriosEvento::dispatch($evento);
            $this->info("Recordatorios encolados para: {$evento->titulo}");
        }

        $this->info($eventos->count().' evento(s) procesado(s).');

        return self::SUCCESS;
    }
}
