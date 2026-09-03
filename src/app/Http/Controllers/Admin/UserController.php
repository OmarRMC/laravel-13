<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /** `/admin/usuarios` */
    public function index(Request $request): View
    {
        return view('admin.usuarios.index', [
            'usuarios' => User::with('roles')
                ->when($request->query('q'), fn ($q, $texto) => $q->where('name', 'ilike', '%'.$texto.'%'))
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    /** */
    public function edit(User $usuario): View
    {
        return view('admin.usuarios.edit', [
            'usuario' => $usuario->load('roles'),
            'roles'   => Role::orderBy('nombre')->get(),
        ]);
    }

    /** sync() de `role_user` mas la baja logica `activo`. */
    public function update(Request $request, User $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'roles'   => ['array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'activo'  => ['required', 'boolean'],
        ]);

        // `activo` no esta en el #[Fillable] del modelo a proposito: solo se
        // toca desde aqui, nunca por asignacion masiva de un formulario publico.
        $usuario->activo = $datos['activo'];
        $usuario->save();

        // sync() deja exactamente los roles marcados: anade los nuevos y quita
        // los que se desmarcaron. Sin roles enviados, el usuario se queda sin ninguno.
        $usuario->roles()->sync($datos['roles'] ?? []);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Usuario actualizado.');
    }
}
