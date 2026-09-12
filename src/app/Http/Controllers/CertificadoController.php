<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CertificadoController extends Controller
{
    public function __invoke(Request $request, Evento $evento): Response
    {
        $inscripcion = $request->user()
            ->inscripciones()
            ->whereKey($evento->id)
            ->firstOrFail()
            ->pivot;

        abort_unless($inscripcion->asistio, 403, 'No hay asistencia registrada en este evento.');

        return Pdf::loadView('certificados.certificado', [
            'evento'       => $evento->load('organizador'),
            'participante' => $request->user(),
            'codigo'       => $inscripcion->codigo,
        ])->setPaper('a4', 'landscape')
          ->download("certificado-{$evento->slug}.pdf");
    }
}
