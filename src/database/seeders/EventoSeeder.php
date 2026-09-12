<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarioOrganizador = User::where('email', 'organizador@socef.test')->first();

        $categoriaTecnologia = Categoria::where('slug', 'tecnologia')->first();
        $categoriaArte = Categoria::where('slug', 'arte')->first();

        Evento::factory()->create([
            'titulo' => 'Laravel 13 desde cero',
            'slug' => 'laravel-13-desde-cero',
            'categoria_id' => $categoriaTecnologia->id,
            'user_id' => $usuarioOrganizador->id,
            'estado' => 'publicado',
            'modalidad' => 'presencial',
            'es_gratuito' => true,
        ]);

        Evento::factory()->create([
            'titulo' => 'Curso de arte',
            'slug' => 'curso-de-arte',
            'categoria_id' => $categoriaArte->id,
            'user_id' => $usuarioOrganizador->id,
            'estado' => 'publicado',
            'es_gratuito' => false,
            'precio' => 149.99,
        ]);

        Evento::factory()->create([
            'titulo' => 'Webinar virtual',
            'slug' => 'webinar-virtual',
            'categoria_id' => $categoriaTecnologia->id,
            'user_id' => $usuarioOrganizador->id,
            'estado' => 'publicado',
            'modalidad' => 'virtual',
            'termina_el' => null,
        ]);

        $fechaPasadaInicio = now()->subDays(5);
        $fechaPasadaTermino = (clone $fechaPasadaInicio)->modify('+2 hours');

        Evento::factory()->create([
            'titulo' => 'Jornada pasada',
            'slug' => 'jornada-pasada',
            'categoria_id' => $categoriaTecnologia->id,
            'user_id' => $usuarioOrganizador->id,
            'estado' => 'publicado',
            'inicia_el' => $fechaPasadaInicio,
            'termina_el' => $fechaPasadaTermino,
        ]);

        Evento::factory()->create([
            'titulo' => 'Borrador secreto',
            'slug' => 'borrador-secreto',
            'categoria_id' => $categoriaArte->id,
            'user_id' => $usuarioOrganizador->id,
            'estado' => 'borrador',
        ]);

        Evento::factory()->create([
            'titulo' => 'Cancelado por lluvia',
            'slug' => 'cancelado-por-lluvia',
            'categoria_id' => $categoriaArte->id,
            'user_id' => $usuarioOrganizador->id,
            'estado' => 'cancelado',
        ]);
    }
}
