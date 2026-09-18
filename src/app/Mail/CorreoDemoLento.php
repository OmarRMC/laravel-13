<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

/**
 * SOLO PARA DEMOSTRACION (borrar cuando ya no se use).
 *
 * Simula un envio "pesado" de 1 minuto (podria ser una API externa lenta,
 * un PDF grande, etc.) para comparar Mail::send() (bloquea la request)
 * contra Mail::queue() (no bloquea; el trabajo pesado lo hace el worker despues).
 *
 * Uso:
 *   Mail::to($user)->queue(new CorreoDemoLento());               // caso feliz, tarda 1 min
 *   Mail::to($user)->queue(new CorreoDemoLento(fallar: true));    // lanza excepcion, prueba reintentos
 *
 * Con `fallar: true`, cada intento la tira de una vez (sin esperar el sleep) para
 * ver rapido los 3 intentos + el backoff de 10s entre cada uno. Necesita
 * `php artisan queue:work` corriendo (sin --once, para que reintente solo) y
 * despues revisar `php artisan queue:failed`.
 *
 * Usada por: php artisan app:demo-correo-async
 */
class CorreoDemoLento extends Mailable implements ShouldQueue
// implements ShouldQueue
{
    use SerializesModels , Queueable;
    // Queueable

    /** Intentos antes de darse por vencido y caer a la tabla failed_jobs. */
    public int $tries = 3;

    /** Segundos de espera entre cada reintento. */
    public int $backoff = 10;

    public function __construct(public bool $fallar = false) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Demo: correo lento');
    }

    public function content(): Content
    {
        if ($this->fallar) {
            throw new RuntimeException('Fallo simulado para probar los reintentos.');
        }

        // sleep(5); // el "trabajo pesado" que se quiere demostrar (segundos)

        return new Content(htmlString: '<p>Listo: esto tardo 1 minuto en generarse.</p>');
    }
}
