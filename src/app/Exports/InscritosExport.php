<?php

namespace App\Exports;

use App\Models\Evento;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InscritosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(public Evento $evento) {}

    public function collection(): Collection
    {
        return $this->evento->inscritos()->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            __('Participant'),
            __('Email'),
            __('Code'),
            __('Status'),
            __('Attendance'),
            __('Registered on'),
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->pivot->codigo,
            $this->etiquetaEstado($user->pivot->estado),
            $user->pivot->asistio ? __('Yes') : __('No'),
            $user->pivot->created_at->format('d/m/Y H:i'),
        ];
    }

    /** Fila 1 (encabezados): fondo indigo y texto blanco en negrita. */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4338CA'],
                ],
            ],
        ];
    }

    /** Mismas claves __() que usa components/estado-badge.blade.php para el estado de inscripcion. */
    private function etiquetaEstado(string $estado): string
    {
        return match ($estado) {
            'pendiente'  => __('Pending'),
            'confirmada' => __('Confirmed'),
            'cancelada'  => __('Cancelled'),
            default      => ucfirst($estado),
        };
    }
}
