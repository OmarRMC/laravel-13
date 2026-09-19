<?php

namespace App\Mcp\Tools;

use App\Http\Resources\InscripcionResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

/**
 * Mismo criterio que Api\InscripcionController::index() (S10): siempre las inscripciones
 * del usuario autenticado por el token de la request, nunca las de otro usuario a pedido
 * (no acepta user_id/email como parametro). $request->user() resuelve al usuario que
 * autentico el middleware `auth:sanctum` en el transporte web; por stdio (Mcp::local, sin
 * HTTP) no hay usuario autenticado y se responde con un error explicito.
 */
#[Description('Lista las inscripciones del usuario autenticado por el token de la request. Requiere autenticacion.')]
class MisInscripcionesTool extends Tool
{
    public function handle(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            return Response::error('Esta herramienta requiere autenticacion. Conectate al servidor web con un token Sanctum valido.');
        }

        $inscripciones = $user->inscripciones()
            ->with('categoria')
            ->orderByDesc('inicia_el')
            ->limit(20)
            ->get();

        return Response::json(
            InscripcionResource::collection($inscripciones)->resolve()
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
