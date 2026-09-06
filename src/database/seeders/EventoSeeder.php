<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    /**
     * Slug fijo expuesto como constante porque InscripcionSeeder lo necesita
     * para saber a que evento inscribir gente.
     */
    public const SLUG_LARAVEL = 'laravel-13-desde-cero';

    /**
     * Seis eventos, todos del mismo organizador: tres publicados y futuros (los
     * unicos del catalogo), uno ya celebrado, uno en borrador y uno cancelado.
     *
     * El slug de cada uno es fijo porque los casos de prueba abren esas URLs.
     */
    public function run(): void
    {
        $organizador = User::where('email', UserSeeder::EMAIL_ORGANIZADOR)->firstOrFail();
        $categorias = Categoria::pluck('id', 'slug');

        /*
         * La FK se pasa a mano: ->for($organizador) buscaria una relacion `user()`
         * en Evento y la relacion se llama `organizador()`.
         */
        $base = Evento::factory()->state(['user_id' => $organizador->id]);

        $base->create([
            'titulo' => 'Laravel 13 desde cero',
            'slug' => self::SLUG_LARAVEL,
            'categoria_id' => $categorias[CategoriaSeeder::SLUG_TECNOLOGIA],
            'lugar' => 'Aula 3, Edificio Central',
        ]);

        $base->dePago(149.99)->create([
            'titulo' => 'Curso de arte contemporaneo',
            'slug' => 'curso-de-arte',
            'categoria_id' => $categorias[CategoriaSeeder::SLUG_ARTE],
            'lugar' => 'Sala de exposiciones',
        ]);

        $base->virtual()->create([
            'titulo' => 'Webinar: primeros pasos con Docker',
            'slug' => 'webinar-virtual',
            'categoria_id' => $categorias[CategoriaSeeder::SLUG_TECNOLOGIA],
        ]);

        $base->pasado()->create([
            'titulo' => 'Jornada de puertas abiertas',
            'slug' => 'jornada-pasada',
            'categoria_id' => $categorias[CategoriaSeeder::SLUG_ARTE],
            'lugar' => 'Patio principal',
        ]);

        $base->borrador()->create([
            'titulo' => 'Taller aun sin publicar',
            'slug' => 'borrador-secreto',
            'categoria_id' => $categorias[CategoriaSeeder::SLUG_TECNOLOGIA],
            'lugar' => 'Por confirmar',
        ]);

        $base->cancelado()->create([
            'titulo' => 'Ruta guiada por el casco antiguo',
            'slug' => 'cancelado-por-lluvia',
            'categoria_id' => $categorias[CategoriaSeeder::SLUG_ARTE],
            'lugar' => 'Plaza Mayor',
        ]);
    }
}
