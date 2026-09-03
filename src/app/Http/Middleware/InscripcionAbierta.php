<?php

namespace App\Http\Middleware;

use App\Models\Evento;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InscripcionAbierta
{
    public function handle(Request $request, Closure $next): Response
    {
        $evento = $request->route('evento');

        if (! $evento instanceof Evento) {
            abort(404);
        }

        // 1 · Solo se admiten inscripciones en eventos publicados.
        if ($evento->estado !== 'publicado') {
            return back()->with('error', 'Este evento no admite inscripciones.');
        }

        // 2 · No se puede entrar a algo que ya empezo.
        if ($evento->inicia_el->isPast()) {
            return back()->with('error', 'El plazo de inscripcion ya se cerro.');
        }

        // 3 · Cupo. Las canceladas liberan plaza, por eso no cuentan.
        $ocupadas = $evento->inscritos()
            ->wherePivot('estado', '!=', 'cancelada')
            ->count();

        if ($ocupadas >= $evento->cupo) {
            return back()->with('error', 'No quedan plazas disponibles.');
        }

        return $next($request);
    }
}
