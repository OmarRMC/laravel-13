<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Evento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        $datos['user_id'] = $request->user()->id;
        $datos['slug']    = $this->slugUnico($datos['titulo']);

        Evento::create($datos);

        return redirect()
            ->route('panel.eventos.index')
            ->with('status', 'Evento creado.');
    }

    /** formulario de edicion. */
    public function edit(Evento $evento): View
    {
        return view('panel.eventos.edit', [
            'evento'     => $evento,
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    /** guarda cambios, incluido `estado` (borrador -> publicado). */
    public function update(Request $request, Evento $evento): RedirectResponse
    {
        $datos = $this->validar($request);

        if ($datos['titulo'] !== $evento->titulo) {
            $datos['slug'] = $this->slugUnico($datos['titulo'], $evento->id);
        }

        $evento->update($datos);

        return redirect()
            ->route('panel.eventos.index')
            ->with('status', 'Evento actualizado.');
    }

    /** elimina el evento; el pivote cae por cascadeOnDelete. */
    public function destroy(Evento $evento): RedirectResponse
    {
        $evento->delete();

        return redirect()
            ->route('panel.eventos.index')
            ->with('status', 'Evento eliminado.');
    }

    /** Reglas compartidas por store y update. Se iran a un FormRequest en la S7. */
    private function validar(Request $request): array
    {
        return $request->validate([
            'titulo'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['required', 'string'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'inicia_el'    => ['required', 'date'],
            'termina_el'   => ['nullable', 'date', 'after:inicia_el'],
            'lugar'        => ['required', 'string', 'max:150'],
            'modalidad'    => ['required', 'in:presencial,virtual,hibrido'],
            'cupo'         => ['required', 'integer', 'min:1'],
            'es_gratuito'  => ['required', 'boolean'],
            'precio'       => ['nullable', 'required_if:es_gratuito,0', 'numeric', 'min:0'],
            'estado'       => ['required', 'in:borrador,publicado,cerrado,cancelado'],
        ]);
    }

    /** `eventos.slug` es unique: dos eventos con el mismo titulo chocarian. */
    private function slugUnico(string $titulo, ?int $ignorarId = null): string
    {
        $base = Str::slug($titulo);
        $slug = $base;
        $n    = 2;

        // El when() es necesario: whereKeyNot(null) genera `id != NULL`, que en SQL
        // no es falso sino NULL, y dejaria pasar cualquier slug repetido en el alta.
        while (Evento::where('slug', $slug)
            ->when($ignorarId, fn ($q) => $q->whereKeyNot($ignorarId))
            ->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }
}
