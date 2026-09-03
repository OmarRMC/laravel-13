<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Symfony\Component\HttpFoundation\Response;

class ReporteController extends Controller
{
    /** `/panel/eventos/{evento}/reporte/pdf` */
    public function pdf(Evento $evento): Response
    {
        abort(501, 'Reporte PDF pendiente (Sesion 9).');
    }

    /** `/panel/eventos/{evento}/reporte/excel` */
    public function excel(Evento $evento): Response
    {
        abort(501, 'Reporte Excel pendiente (Sesion 9).');
    }
}
