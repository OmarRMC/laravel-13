<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
        $datos = $this->validar($request);
        $datos['slug'] = Str::slug($datos['nombre']);

        Categoria::create($datos);

        return redirect()
            ->route('admin.categorias.index')
            ->with('status', 'Categoria creada.');
    }

    /** */
    public function edit(Categoria $categoria): View
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    /** */
    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        $datos = $this->validar($request, $categoria);
        $datos['slug'] = Str::slug($datos['nombre']);

        $categoria->update($datos);

        return redirect()
            ->route('admin.categorias.index')
            ->with('status', 'Categoria actualizada.');
    }

    /** - falla si tiene eventos: `categoria_id` es restrictOnDelete. */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        // Se comprueba aqui para dar un mensaje util en vez de dejar que la
        // restriccion de la base de datos lance un 500.
        if ($categoria->eventos()->exists()) {
            return back()->with('error', 'No se puede eliminar: la categoria tiene eventos.');
        }

        $categoria->delete();

        return redirect()
            ->route('admin.categorias.index')
            ->with('status', 'Categoria eliminada.');
    }

    /** Reglas compartidas. `nombre` y `slug` son unique en la tabla. */
    private function validar(Request $request, ?Categoria $categoria = null): array
    {
        return $request->validate([
            'nombre' => [
                'required', 'string', 'max:255',
                Rule::unique('categorias', 'nombre')->ignore($categoria),
            ],
            'color' => ['nullable', 'string', 'max:20'],
        ]);
    }
}
