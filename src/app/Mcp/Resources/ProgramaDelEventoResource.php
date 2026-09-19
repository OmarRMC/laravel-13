<?php

namespace App\Mcp\Resources;

use App\Models\Evento;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Contracts\HasUriTemplate;
use Laravel\Mcp\Server\Resource;
use Laravel\Mcp\Support\UriTemplate;

#[Description('Ficha del programa de un evento publicado: horario, lugar, modalidad, categoria y cupo.')]
#[MimeType('text/markdown')]
class ProgramaDelEventoResource extends Resource implements HasUriTemplate
{
    public function uriTemplate(): UriTemplate
    {
        return new UriTemplate('evento://{slug}/programa');
    }

    public function handle(Request $request): Response
    {
        $evento = Evento::publicado()
            ->with('categoria')
            ->where('slug', $request->get('slug'))
            ->first();

        if (! $evento) {
            return Response::text('No hay ningun evento publicado con ese slug.');
        }

        return Response::text(
            "# {$evento->titulo}\n\n".
            "- Fecha y hora: {$evento->inicia_el->format('d/m/Y H:i')}\n".
            "- Lugar: {$evento->lugar} ({$evento->modalidad})\n".
            "- Categoria: {$evento->categoria->nombre}\n".
            "- Cupo disponible: {$evento->cuposDisponibles()} de {$evento->cupo}\n\n".
            "{$evento->descripcion}"
        );
    }
}
