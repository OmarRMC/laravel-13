<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('dashboard', [
            'inscripciones' => $request->user()
                ->inscripciones()
                ->wherePivot('estado', '!=', 'cancelada')
                ->latest('evento_user.created_at')
                ->take(5)
                ->get(),
        ]);
    }
}
