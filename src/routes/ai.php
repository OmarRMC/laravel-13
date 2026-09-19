<?php

use App\Mcp\Servers\EventosServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('eventos', EventosServer::class);

Mcp::web('/mcp/eventos', EventosServer::class)
    ->middleware('auth:sanctum');
    