<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = fake()->unique()->words(2, true);

        return [
            'nombre' => $nombre,
            'slug' => Str::slug($nombre),
            'color' => fake()->hexColor(), // '#a1b2c3': 7 caracteres, justo lo que admite la columna
        ];
    }
}
