<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventoResource;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventoController extends Controller
{
    /** `GET /api/v1/eventos` */
    public function index(Request $request): AnonymousResourceCollection
    {
        $eventos = Evento::query()
            ->publicado()
            ->proximos()
            ->when($request->query('categoria'), function ($query, $slug) {
                $query->whereHas('categoria', fn ($q) => $q->where('slug', $slug));
            })
            ->with(['categoria', 'organizador'])
            ->paginate(12)
            ->withQueryString();

        return EventoResource::collection($eventos);
    }

    /** `GET /api/v1/eventos/{evento}` — resuelto por slug (Evento::getRouteKeyName). */
    public function show(Evento $evento): EventoResource
    {
        abort_unless($evento->estado === 'publicado', 404);

        return new EventoResource($evento->load(['categoria', 'organizador']));
    }
}
