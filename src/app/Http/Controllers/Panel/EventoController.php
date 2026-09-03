<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Evento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventoController extends Controller
{
    /** `/panel/eventos` - solo los mios. */
    public function index(Request $request): View
    {
        return view('panel.eventos.index', [
            'eventos' => $request->user()
                ->eventosOrganizados()
                ->with('categoria')
                ->withCount('inscritos')
                ->when($request->query('estado'), fn ($q, $estado) => $q->where('estado', $estado))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    /** formulario de alta. */
    public function create(): View
    {
        return view('panel.eventos.create', [
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    /** alta. TODO S7: StoreEventoRequest + slug + afiche. */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /** formulario de edicion. */
    public function edit(Evento $evento): View
    {
        // TODO S8: $this->authorize('update', $evento);
        return view('panel.eventos.edit', [
            'evento'     => $evento,
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    /** guarda cambios, incluido `estado` (borrador -> publicado). */
    public function update(Request $request, Evento $evento): RedirectResponse
    {
        //
    }

    /** elimina el evento; el pivote cae por cascadeOnDelete. */
    public function destroy(Evento $evento): RedirectResponse
    {
        //
    }
}
