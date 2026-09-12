<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;


class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['nombre' => 'admin', 'descripcion' => 'Administrador del sistema']);
        Role::create(['nombre' => 'organizador', 'descripcion' => 'Organizador de eventos']);
        Role::create(['nombre' => 'participante', 'descripcion' => 'Participante regular']);
    }
}
