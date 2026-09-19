<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerfilUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PerfilController extends Controller
{
    /**
     * Formulario del perfil ampliado (`App\Models\Perfil`), distinto del
     * `/perfil` de Breeze que edita nombre/email/contrasena del `User`.
     */
    public function edit(Request $request): View
    {
        return view('perfil.edit', [
            'perfil' => $request->user()->perfil,
            'tieneMcpToken' => $request->user()
                ->tokens()
                ->where('name', McpTokenController::NOMBRE_TOKEN)
                ->exists(),
        ]);
    }

    public function update(PerfilUpdateRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except('avatar');

        if ($request->hasFile('avatar')) {
            // Reemplaza el archivo viejo: si no se borra, queda huerfano en el disco.
            // Sin indicar disco: usa el disco por defecto (FILESYSTEM_DISK en .env),
            // el mismo que usan las vistas al llamar Storage::url() sin disco.
            if ($request->user()->perfil?->avatar) {
                Storage::delete($request->user()->perfil->avatar);
            }

            $datos['avatar'] = $request->file('avatar')->store('avatares');
        }

        // updateOrCreate cubre tanto al usuario que ya tiene fila en `perfiles`
        // como al que tiene `perfil` null: la crea la primera vez que guarda.
        $request->user()->perfil()->updateOrCreate([], $datos);

        return redirect()->route('perfil.edit')->with('status', 'perfil-actualizado');
    }
}
