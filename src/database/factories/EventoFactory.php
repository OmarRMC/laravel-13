<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<Evento>
 */
class EventoFactory extends Factory
{
    /**
     * El evento tipico: publicado, futuro, presencial y gratuito.
     *
     * Todo lo que se sale de ahi (borrador, cancelado, pasado, de pago, virtual)
     * es un state, no otro definition().
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titulo = fake()->unique()->sentence(4);

        return [
            'titulo' => $titulo,
            'slug' => Str::slug($titulo),
            'descripcion' => fake()->paragraph(),
            'categoria_id' => Categoria::factory(),
            'user_id' => User::factory(),
            'inicia_el' => fake()->dateTimeBetween('+1 week', '+3 months'),

            /*
             * Derivada, no generada aparte: el cierre se resuelve cuando ya se han
             * aplicado los states, asi que si `pasado()` o el seeder cambian
             * `inicia_el`, la hora de fin le sigue y nunca queda antes del inicio.
             */
            'termina_el' => fn (array $attributes) => Carbon::parse($attributes['inicia_el'])->addHours(3),

            'lugar' => fake()->streetAddress(),
            'modalidad' => 'presencial',
            'cupo' => 30,
            'es_gratuito' => true,
            'precio' => null,
            'estado' => 'publicado',
        ];
    }

    /** No es publico: abrir su URL debe dar 404. */
    public function borrador(): static
    {
        return $this->state(fn (array $attributes) => ['estado' => 'borrador']);
    }

    /** Anulado: sigue siendo visible pero no admite inscripciones. */
    public function cancelado(): static
    {
        return $this->state(fn (array $attributes) => ['estado' => 'cancelado']);
    }

    /** Ya se celebro: el scope `proximos()` lo deja fuera del catalogo. */
    public function pasado(): static
    {
        return $this->state(fn (array $attributes) => [
            'inicia_el' => fake()->dateTimeBetween('-3 months', '-1 week'),
        ]);
    }

    /** De pago: la ficha muestra el precio en vez de "Gratuito". */
    public function dePago(float $precio = 149.99): static
    {
        return $this->state(fn (array $attributes) => [
            'es_gratuito' => false,
            'precio' => $precio,
        ]);
    }

    /** En linea y sin hora de fin: la ficha tiene que aguantar `termina_el` null. */
    public function virtual(): static
    {
        return $this->state(fn (array $attributes) => [
            'modalidad' => 'virtual',
            'termina_el' => null,
            'lugar' => 'https://meet.socef.test/'.Str::random(10),
        ]);
    }
}
