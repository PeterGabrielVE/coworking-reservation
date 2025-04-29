<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReservasExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new \App\Exports\Sheets\DetalleReservasSheet(),
            new \App\Exports\Sheets\ResumenPorSalaSheet(),
            new \App\Exports\Sheets\ResumenPorDiaSheet(),
        ];
    }
}
