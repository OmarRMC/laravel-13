<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InscripcionResource;
use App\Models\Evento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class InscripcionController extends Controller
{
    /** `GET /api/v1/mis-inscripciones` — solo las del dueno del token. */
    public function index(Request $request): AnonymousResourceCollection
    {
        $inscripciones = $request->user()
            ->inscripciones()
            ->with('categoria')
            ->orderByDesc('inicia_el')
            ->paginate(10);

        return InscripcionResource::collection($inscripciones);
    }

    /**
     * `POST /api/v1/eventos/{evento}/inscribirse`
     *
     * Misma regla de negocio que InscripcionController::store (web): un codigo aleatorio,
     * sin permitir doble inscripcion. El cupo/fecha/estado ya los valida el middleware
     * `inscripcion.abierta`, reutilizado tal cual en la ruta.
     */
    public function store(Request $request, Evento $evento): InscripcionResource|JsonResponse
    {
        $user = $request->user();

        if ($evento->inscritos()->whereKey($user->id)->exists()) {
            return response()->json([
                'message' => __('You are already registered for this event.'),
            ], 409);
        }

        $evento->inscritos()->attach($user->id, [
            'codigo' => Str::upper(Str::random(12)),
            'estado' => 'confirmada',
        ]);

        $inscripcion = $user->inscripciones()->with('categoria')->findOrFail($evento->id);

        return new InscripcionResource($inscripcion);
    }
}
