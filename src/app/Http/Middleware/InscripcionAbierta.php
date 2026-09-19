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

        // Publicado / no vencido / con cupo: misma regla que usa InscribirseEnEventoTool (MCP),
        // que no pasa por este middleware al no tener una ruta HTTP real detras.
        if ($mensaje = $evento->errorDeInscripcion()) {
            return $this->rechazar($request, $mensaje);
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
