<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public const EMAIL_ORGANIZADOR = 'organizador@socef.test';

    public const EMAIL_PARTICIPANTE = 'participante@socef.test';

    public const EMAIL_PARTICIPANTE_2 = 'participante2@socef.test';

    public const EMAIL_PARTICIPANTE_3 = 'participante3@socef.test';

    public function run(): void
    {
        $roles = Role::pluck('id', 'nombre');

        $admin = User::factory()->create([
            'name' => 'Ana Administradora',
            'email' => 'admin@socef.test',
        ]);
        $admin->roles()->attach($roles['admin']);

        $organizador = User::factory()->create([
            'name' => 'Oscar Organizador',
            'email' => self::EMAIL_ORGANIZADOR,
        ]);
        $organizador->roles()->attach($roles['organizador']);

        $participante = User::factory()->create([
            'name' => 'Paula Participante',
            'email' => self::EMAIL_PARTICIPANTE,
        ]);
        $participante->roles()->attach($roles['participante']);

        $baja = User::factory()->desactivado()->create([
            'name' => 'Bruno Baja',
            'email' => 'baja@socef.test',
        ]);
        $baja->roles()->attach($roles['participante']);

        $participante2 = User::factory()->create(['email' => self::EMAIL_PARTICIPANTE_2]);
        $participante2->roles()->attach($roles['participante']);

        $participante3 = User::factory()->create(['email' => self::EMAIL_PARTICIPANTE_3]);
        $participante3->roles()->attach($roles['participante']);

        $admin->perfil()->create([
            'telefono' => '600 000 001',
            'institucion' => 'SOCEF',
            'bio' => 'Administra las categorias, los usuarios y sus roles.',
        ]);

        $organizador->perfil()->create([
            'telefono' => '600 000 002',
            'institucion' => 'Escuela de Formacion Continua',
            'bio' => 'Organiza los cursos y talleres del catalogo.',
        ]);
    }
}
