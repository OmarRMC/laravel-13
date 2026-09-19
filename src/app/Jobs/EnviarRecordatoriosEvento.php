<?php

namespace App\Jobs;

use App\Mail\RecordatorioEvento;
use App\Models\Evento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatoriosEvento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Evento $evento) {}

    public function handle(): void
    {
        $this->evento->inscritos()
            ->wherePivot('estado', 'confirmada')
            ->get()
            ->each(fn ($inscrito) => Mail::to($inscrito)
                ->queue(new RecordatorioEvento($this->evento, $inscrito->pivot->codigo)));
    }
}
