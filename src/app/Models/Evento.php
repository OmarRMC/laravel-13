<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Evento extends Model
{
    use HasFactory;
    
    protected $table = 'eventos';

    protected $fillable = [
        'titulo', 'slug', 'descripcion', 'categoria_id', 'user_id',
        'inicia_el', 'termina_el', 'lugar', 'modalidad',
        'cupo', 'es_gratuito', 'precio', 'afiche', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'inicia_el'   => 'datetime',
            'termina_el'  => 'datetime',
            'es_gratuito' => 'boolean',
            'precio'      => 'decimal:2',
        ];
    }

    // Relaciones

    /** Inversa del 1:N la categoria a la que pertenece. */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /** Inversa del 1:N el usuario que lo organiza (FK explicita). */
    public function organizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** N:M los usuarios inscritos (pivote `evento_user`, con datos propios). */
    public function inscritos(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['codigo', 'estado', 'asistio'])
            ->withTimestamps();
    }
}
