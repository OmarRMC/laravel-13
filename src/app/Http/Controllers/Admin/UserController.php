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
        //
    }
}
