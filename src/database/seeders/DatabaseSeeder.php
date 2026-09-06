<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * El orden importa: cada seeder busca lo que sembro el anterior y las claves
     * foraneas no admiten adelantos.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,      // UserSeeder necesita los ids de los roles
            UserSeeder::class,
            CategoriaSeeder::class,
            EventoSeeder::class,    // depende del organizador y de las categorias
            InscripcionSeeder::class,
        ]);
    }
}
