<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InscritoController extends Controller
{
    /** `/panel/eventos/{evento}/inscritos` */
    public function index(Evento $evento): View
    {
        // $this->authorize('update', $evento);
        return view('panel.eventos.inscritos', [
            'evento'    => $evento,
            'inscritos' => $evento->inscritos()->orderBy('name')->paginate(25),
        ]);
    }

    /**
     * `PATCH /panel/eventos/{evento}/asistencia/{inscrito}`
     */
    public function asistencia(Evento $evento, User $inscrito): RedirectResponse
    {
        $asistio = !$inscrito->pivot->asistio;

        $evento->inscritos()->updateExistingPivot($inscrito->id, ['asistio' => $asistio]);

        return back()->with('status', $asistio ? 'Asistencia marcada.' : 'Asistencia retirada.');
    }
}
