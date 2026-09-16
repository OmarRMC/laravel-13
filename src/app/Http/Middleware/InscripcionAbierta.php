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
            return $this->rechazar($request, __('This event is not open for registration.'));
        }

        // 2 · No se puede entrar a algo que ya empezo.
        if ($evento->inicia_el->isPast()) {
            return $this->rechazar($request, __('The registration deadline has passed.'));
        }

        // 3 · Cupo. Las canceladas liberan plaza, por eso no cuentan.
        $ocupadas = $evento->inscritos()
            ->wherePivot('estado', '!=', 'cancelada')
            ->count();

        if ($ocupadas >= $evento->cupo) {
            return $this->rechazar($request, __('No spots available.'));
        }

        return $next($request);
    }

    /**
     * Un cliente de API (Postman, la app movil) espera un error JSON, no una redireccion con
     * flash de sesion: `back()` no significa nada para quien no tiene un "atras" de navegador.
     */
    private function rechazar(Request $request, string $mensaje): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $mensaje], 422);
        }

        return back()->with('error', $mensaje);
    }
}
