<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * $this es un Evento con datos del pivote adjuntos (->pivot), porque sale de
 * $user->inscripciones(), que es un belongsToMany(Evento::class) desde User.
 */
class InscripcionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'evento'      => new EventoResource($this->resource),
            'codigo'      => $this->pivot->codigo,
            'estado'      => $this->pivot->estado,
            'asistio'     => $this->pivot->asistio,
            'inscrito_el' => $this->pivot->created_at?->toIso8601String(),
        ];
    }
}
