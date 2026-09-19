<?php

namespace App\Mcp\Servers;

use App\Mcp\Prompts\InvitarAmigoPrompt;
use App\Mcp\Resources\ProgramaDelEventoResource;
use App\Mcp\Tools\BuscarEventosTool;
use App\Mcp\Tools\InscribirseEnEventoTool;
use App\Mcp\Tools\ListarCategoriasTool;
use App\Mcp\Tools\MisInscripcionesTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Agenda de Eventos')]
#[Version('1.0.0')]
#[Instructions('Consulta la agenda de eventos publicados. No expone datos de usuarios ajenos: "mis inscripciones" solo devuelve las del usuario autenticado por el token de la request.')]
class EventosServer extends Server
{
    protected array $tools = [
        BuscarEventosTool::class,
        ListarCategoriasTool::class,
        MisInscripcionesTool::class,
        InscribirseEnEventoTool::class,
    ];

    protected array $resources = [
        ProgramaDelEventoResource::class,
    ];

    protected array $prompts = [
        InvitarAmigoPrompt::class,
    ];
}
