<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Evento;
use Illuminate\View\View;

class EventoController extends Controller
{
    /**
     *`/eventos` y `/eventos/categoria/{categoria?}`.
     *
     * Un solo metodo para las tres. La diferencia la marca el parametro opcional,
     * no el codigo: si llega una categoria, filtra; si no, lista todo.
     */
    public function index(?Categoria $categoria = null): View
    {
        $eventos = Evento::query()
            ->publicado()
            ->proximos()
            ->when($categoria, fn ($q) => $q->where('categoria_id', $categoria->id))
            ->with(['categoria', 'organizador'])
            ->paginate(12)
            ->withQueryString();

        return view('eventos.index', [
            'eventos'    => $eventos,
            'categorias' => Categoria::orderBy('nombre')->get(),
            'categoria'  => $categoria,
        ]);
    }

    /**
     * Ruta 19: `/eventos/{evento}` — ficha publica, resuelta por `slug`.
     */
    public function show(Evento $evento): View
    {
        abort_unless($evento->estado === 'publicado', 404);

        $evento->load(['categoria', 'organizador'])->loadCount('inscritos');

        return view('eventos.show', compact('evento'));
    }
}
