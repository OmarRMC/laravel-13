<?php

namespace App\Mcp\Prompts;

use App\Models\Evento;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Description('Arma una invitacion breve y amigable para compartir un evento con alguien.')]
class InvitarAmigoPrompt extends Prompt
{
    public function handle(Request $request): Response
    {
        $evento = Evento::find($request->get('evento_id'));

        if (! $evento) {
            return Response::text('No encontre un evento con ese ID. Usa BuscarEventosTool para confirmar el ID correcto.');
        }

        return Response::text(
            'Escribi una invitacion breve y amigable, lista para mandar por WhatsApp, invitando a '.
            "alguien a inscribirse al evento \"{$evento->titulo}\", que se realiza el ".
            "{$evento->inicia_el->format('d/m/Y H:i')} en {$evento->lugar}. Mencion".
            'a que todavia hay cupo disponible.'
        );
    }

    /**
     * @return array<int, Argument>
     */
    public function arguments(): array
    {
        return [
            new Argument(
                name: 'evento_id',
                description: 'ID del evento a invitar (lo devuelve BuscarEventosTool).',
                required: true,
            ),
        ];
    }
}
