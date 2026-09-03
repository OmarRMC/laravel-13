<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InscripcionController extends Controller
{
    /** `/mis-inscripciones` */
    public function index(Request $request): View
    {
        return view('inscripciones.index', [
            'inscripciones' => $request->user()
                ->inscripciones()
                ->with('categoria')
                ->orderByDesc('inicia_el')
                ->paginate(10),
        ]);
    }

    /**
     * `POST /eventos/{evento}/inscribirse`
     * Middleware extra: `inscripcion.abierta` (estado, fecha y cupo ya validados).
     */
    public function store(Request $request, Evento $evento): RedirectResponse
    {
        $user = $request->user();

        if ($evento->inscritos()->whereKey($user->id)->exists()) {
            return back()->with('error', 'Ya estabas inscrito en este evento.');
        }

        $evento->inscritos()->attach($user->id, [
            'codigo' => Str::upper(Str::random(12)),
            'estado' => 'confirmada',
        ]);

        return back()->with('status', 'Inscripcion confirmada.');
    }

    /**
     * `PATCH /eventos/{evento}/inscripcion`
     *
     * Cancelar es un UPDATE, no un DELETE: el enum del pivote tiene 'cancelada'.
     * Borrar la fila perderia el `codigo` y liberaria el unique, permitiendo
     * reinscribirse con un codigo distinto.    
     */
    public function cancelar(Request $request, Evento $evento): RedirectResponse
    {
        $request->user()
            ->inscripciones()
            ->updateExistingPivot($evento->id, ['estado' => 'cancelada']);

        return back()->with('status', 'Inscripcion cancelada.');
    }
}
