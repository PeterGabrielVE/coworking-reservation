<?php

namespace App\Exports\Sheets;

use App\Models\Reserva;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResumenPorDiaSheet implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Agrupa por fecha y suma reservas (1h cada una)
        return Reserva::select('fecha', DB::raw('COUNT(*) as total_reservas'))
            ->groupBy('fecha')
            ->get()
            ->map(function($row) {
                return [
                    $row->fecha,
                    $row->total_reservas,     // total horas en ese día
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Total Horas Reservadas',
        ];
    }
}
