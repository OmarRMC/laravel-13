<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'titulo'      => $this->titulo,
            'slug'        => $this->slug,
            'descripcion' => $this->descripcion,
            'inicia_el'   => $this->inicia_el->toIso8601String(),
            'termina_el'  => $this->termina_el?->toIso8601String(),
            'lugar'       => $this->lugar,
            'modalidad'   => $this->modalidad,
            'gratuito'    => $this->es_gratuito,
            'precio'      => $this->when(! $this->es_gratuito, $this->precio),
            'afiche'      => $this->afiche ? asset('storage/'.$this->afiche) : null,
            'cupo'        => [
                'total'       => $this->cupo,
                'disponibles' => max($this->cuposDisponibles(), 0),
            ],
            'categoria'   => new CategoriaResource($this->whenLoaded('categoria')),
            'organizador' => new UserResource($this->whenLoaded('organizador')),
        ];
    }
}
