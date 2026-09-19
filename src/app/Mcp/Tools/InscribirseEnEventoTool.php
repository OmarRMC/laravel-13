<?php

namespace App\Mcp\Tools;

use App\Http\Resources\InscripcionResource;
use App\Models\Evento;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Inscribe al usuario autenticado en un evento publicado, si hay cupo y no paso la fecha limite. Requiere autenticacion.')]
class InscribirseEnEventoTool extends Tool
{
    public function handle(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            return Response::error('Esta herramienta requiere autenticacion. Conectate al servidor web con un token Sanctum valido.');
        }

        $evento = Evento::find($request->integer('evento_id'));

        if (! $evento) {
            return Response::error('No existe un evento con ese ID. Usa BuscarEventosTool para encontrar el ID correcto.');
        }

        if ($mensaje = $evento->errorDeInscripcion()) {
            return Response::error($mensaje);
        }

        if ($evento->inscritos()->whereKey($user->id)->exists()) {
            return Response::error(__('You are already registered for this event.'));
        }

        $evento->inscritos()->attach($user->id, [
            'codigo' => Str::upper(Str::random(12)),
            'estado' => 'confirmada',
        ]);

        $inscripcion = $user->inscripciones()->with('categoria')->findOrFail($evento->id);

        return Response::json((new InscripcionResource($inscripcion))->resolve());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'evento_id' => $schema->integer()
                ->required()
                ->description('ID del evento a inscribirse (lo devuelve BuscarEventosTool).'),
        ];
    }
}
