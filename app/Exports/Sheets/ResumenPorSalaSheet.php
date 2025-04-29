<?php

namespace App\Exports\Sheets;

use App\Models\Reserva;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResumenPorSalaSheet implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Agrupa por sala y suma horas (asumiendo duración fija de 1h por reserva)
        return Reserva::select('sala_id', DB::raw('COUNT(*) as total_reservas'))
            ->groupBy('sala_id')
            ->with('sala')
            ->get()
            ->map(function($row) {
                return [
                    $row->sala->nombre,
                    $row->total_reservas,     // num de reservas de 1h => total horas
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Sala',
            'Total Horas Reservadas',
        ];
    }
}
