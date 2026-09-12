<?php

namespace Database\Factories;

use App\Models\Evento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evento>
 */
class EventoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fechaInicio = fake()->dateTimeBetween('+1 week', '+2 weeks');
        $fechaTermino = (clone $fechaInicio)->modify('+2 days');

        return [
            'titulo' => fake()->sentence(4),
            'slug' => fake()->unique()->slug(),
            'descripcion' => fake()->paragraph(),
            'inicia_el' => $fechaInicio,
            'termina_el' => $fechaTermino,
            'lugar' => fake()->address(),
            'modalidad' => 'presencial',
            'cupo' => 15,
            'precio' => null,
            'es_gratuito' => true,
            'estado' => 'publicado',
        ];
    }
}
