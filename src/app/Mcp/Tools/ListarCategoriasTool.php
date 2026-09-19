<?php

namespace App\Mcp\Tools;

use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

/**
 * Mismo criterio que Api\CategoriaController::index() (S10): sin filtros, sin paginacion
 * (son pocas categorias). Sirve para que el agente sepa que "slug" pasarle a
 * BuscarEventosTool en vez de adivinarlo.
 */
#[Description('Lista todas las categorias disponibles, para usar su slug como filtro en BuscarEventosTool.')]
class ListarCategoriasTool extends Tool
{
    public function handle(Request $request): Response
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return Response::json(
            CategoriaResource::collection($categorias)->resolve()
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
