<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Slugs fijos expuestos como constante porque EventoSeeder los necesita
     * para asignar categoria a sus eventos.
     */
    public const SLUG_TECNOLOGIA = 'tecnologia';

    public const SLUG_ARTE = 'arte';

    /**
     * Nombre y slug fijos: el slug va en la URL del filtro publico
     * (/eventos/categoria/tecnologia), asi que no puede ser inventado.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Tecnologia', 'slug' => self::SLUG_TECNOLOGIA, 'color' => '#2563eb'],
            ['nombre' => 'Negocios', 'slug' => 'negocios', 'color' => '#16a34a'],
            ['nombre' => 'Arte', 'slug' => self::SLUG_ARTE, 'color' => '#db2777'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::factory()->create($categoria);
        }
    }
}
