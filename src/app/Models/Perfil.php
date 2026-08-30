<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perfil extends Model
{
    /** Laravel pluralizaria "Perfil" como "perfils": hay que decirselo. */
    protected $table = 'perfiles';

    protected $fillable = ['user_id', 'telefono', 'institucion', 'avatar', 'bio'];

    // Relaciones

    /** Inversa del 1:1 — el usuario dueño de este perfil. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
