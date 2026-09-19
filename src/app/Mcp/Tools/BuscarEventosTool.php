<?php

namespace App\Mcp\Tools;

use App\Http\Resources\EventoResource;
use App\Models\Evento;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

/**
 * Reutiliza la misma consulta base de Api\EventoController::index() (S10), agregando el
 * filtro de fechas que la API REST no tiene. La respuesta usa EventoResource -mismo shape
 * publico que /api/v1/eventos, incluida la ocultacion del email del organizador.
 */
#[Description('Busca eventos publicados y proximos, opcionalmente filtrando por categoria (slug) y rango de fechas.')]
class BuscarEventosTool extends Tool
{
    public function handle(Request $request): Response
    {
        $eventos = Evento::query()
            ->publicado()
            ->proximos()
            ->when($request->get('categoria'), function ($query, $slug) {
                $query->whereHas('categoria', fn ($q) => $q->where('slug', $slug));
            })
            ->when($request->get('desde'), fn ($query, $desde) => $query->where('inicia_el', '>=', $desde))
            ->when($request->get('hasta'), fn ($query, $hasta) => $query->where('inicia_el', '<=', $hasta))
            ->with(['categoria', 'organizador'])
            ->limit(20)
            ->get();

        return Response::json(
            EventoResource::collection($eventos)->resolve()
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'categoria' => $schema->string()
                ->description('Slug de la categoria a filtrar, ej. "tecnologia" (opcional).'),
            'desde' => $schema->string()
                ->description('Fecha inicial en formato YYYY-MM-DD (opcional).'),
            'hasta' => $schema->string()
                ->description('Fecha final en formato YYYY-MM-DD (opcional).'),
        ];
    }
}
