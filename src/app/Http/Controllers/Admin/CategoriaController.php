<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * `/eventos/categoria/{categoria}`
 */
class CategoriaController extends Controller
{
    /** */
    public function index(): View
    {
        return view('admin.categorias.index', [
            'categorias' => Categoria::withCount('eventos')->orderBy('nombre')->paginate(20),
        ]);
    }

    /** */
    public function create(): View
    {
        return view('admin.categorias.create');
    }

    /** - el `slug` sale del `nombre`. TODO S7: StoreCategoriaRequest. */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /** */
    public function edit(Categoria $categoria): View
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    /** */
    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        //
    }

    /** - falla si tiene eventos: `categoria_id` es restrictOnDelete. */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        //
    }
}
