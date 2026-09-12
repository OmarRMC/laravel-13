<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolAdministrador = Role::where('nombre', 'admin')->first();
        $rolOrganizador = Role::where('nombre', 'organizador')->first();
        $rolParticipante = Role::where('nombre', 'participante')->first();

        $usuarioAdministrador = User::factory()->create([
            'email' => 'admin@socef.test'
        ]);
        $usuarioAdministrador->roles()->attach($rolAdministrador);
        $usuarioAdministrador->perfil()->create([
            'telefono' => '123456789',
            'institucion' => 'Directiva Central'
        ]);

        $usuarioOrganizador = User::factory()->create([
            'email' => 'organizador@socef.test'
        ]);
        $usuarioOrganizador->roles()->attach($rolOrganizador);
        $usuarioOrganizador->perfil()->create([
            'telefono' => '987654321',
            'institucion' => 'Comité Organizador'
        ]);

        $usuarioParticipante = User::factory()->create([
            'email' => 'participante@socef.test'
        ]);
        $usuarioParticipante->roles()->attach($rolParticipante);

        $usuarioBaja = User::factory()->create([
            'email' => 'baja@socef.test',
            'activo' => false
        ]);
        $usuarioBaja->roles()->attach($rolParticipante);

        $usuariosDeRelleno = User::factory(2)->create();

        foreach ($usuariosDeRelleno as $usuarioActual) {
            $usuarioActual->roles()->attach($rolParticipante);
        }
    }
}
