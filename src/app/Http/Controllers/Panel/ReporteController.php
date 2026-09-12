<?php

namespace App\Http\Controllers\Panel;

use App\Exports\InscritosExport;
use App\Http\Controllers\Controller;
use App\Models\Evento;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ReporteController extends Controller
{
    /** `/panel/eventos/{evento}/reporte/pdf` */
    public function pdf(Evento $evento): Response
    {
        $this->authorize('update', $evento);

        return Pdf::loadView('reportes.inscritos', [
            'evento'    => $evento,
            'inscritos' => $evento->inscritos()->orderBy('name')->get(),
        ])->download("inscritos-{$evento->slug}.pdf");
    }

    /** `/panel/eventos/{evento}/reporte/excel` */
    public function excel(Evento $evento): Response
    {
        $this->authorize('update', $evento);

        return Excel::download(new InscritosExport($evento), "inscritos-{$evento->slug}.xlsx");
    }
}
