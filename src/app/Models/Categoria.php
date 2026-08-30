<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;
    
    protected $table = 'categorias';

    protected $fillable = ['nombre', 'slug', 'color'];

    // Relaciones

    /** 1:N los eventos de esta categoria. */
    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class);
    }
}
