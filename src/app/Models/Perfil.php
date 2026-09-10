<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Perfil extends Model
{
    /** Laravel pluralizaria "Perfil" como "perfils": hay que decirselo. */
    protected $table = 'perfiles';

    protected $fillable = ['user_id', 'telefono', 'institucion', 'avatar', 'bio'];

    /**
     * URL para mostrar el avatar, sin importar en que disco este guardado.
     * En discos que soportan URL firmada (s3) usa una temporal; si el disco
     * no lo soporta (ej. "public" local), cae a la URL publica normal.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->avatar) {
                    return null;
                }

                try {
                    // return Storage::temporaryUrl($this->avatar, now()->addMinutes(30));
                    return Storage::url($this->avatar);
                } catch (\RuntimeException) {
                    return Storage::url($this->avatar);
                }
            },
        );
    }

    // Relaciones

    /** Inversa del 1:1 — el usuario dueño de este perfil. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
