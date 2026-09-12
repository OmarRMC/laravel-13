<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::create([
            'nombre' => 'Tecnología',
            'slug' => 'tecnologia',
            'color' => '#10b981'
        ]);

        Categoria::create([
            'nombre' => 'Negocios',
            'slug' => 'negocios',
            'color' => '#3b82f6'
        ]);

        Categoria::create([
            'nombre' => 'Arte',
            'slug' => 'arte',
            'color' => '#f43f5e'
        ]);
    }
}
