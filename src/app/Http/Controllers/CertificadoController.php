<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


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

        abort(501, 'Generacion de certificados pendiente .');
    }
}
