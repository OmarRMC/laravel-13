<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;


class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'admin',        'descripcion' => 'Administra categorias, usuarios y roles'],
            ['nombre' => 'organizador',  'descripcion' => 'Crea y gestiona sus propios eventos'],
            ['nombre' => 'participante', 'descripcion' => 'Se inscribe en eventos'],
        ];

        foreach ($roles as $rol) {
            Role::updateOrCreate(['nombre' => $rol['nombre']], $rol);
        }
    }
}
