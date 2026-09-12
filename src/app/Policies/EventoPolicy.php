<?php

namespace App\Policies;

use App\Models\Evento;
use App\Models\User;

class EventoPolicy
{
    /** El admin puede todo*/
    public function before(User $user, string $ability): ?bool
    {
        return $user->tieneRol('admin') ? true : null;
    }

    /**
     * Gestionar el evento: editarlo, borrarlo, ver sus inscritos, marcar asistencia
     * y descargar sus reportes son, en el fondo, la misma pregunta: "es tu evento?".
     */
    public function update(User $user, Evento $evento): bool
    {
        return $user->id === $evento->user_id;
    }
}
