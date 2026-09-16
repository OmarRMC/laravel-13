<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Se usa en dos lugares: la respuesta de login (ahi el usuario ve su propio email) y el
 * `organizador` anidado en EventoResource (ahi NO se expone el email a un visitante anonimo).
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->when($request->routeIs('login'), $this->email),
        ];
    }
}
