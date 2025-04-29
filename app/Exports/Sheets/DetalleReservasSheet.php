<?php

namespace App\Exports\Sheets;

use App\Models\Reserva;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DetalleReservasSheet implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        // Trae todas las reservas con relaciones
        return Reserva::with(['user','sala'])->get();
    }

    public function map($reserva): array
    {
        return [
            $reserva->user->name,           // Cliente
            $reserva->sala->nombre,         // Sala
            $reserva->fecha,
            $reserva->hora_inicio,          // Hora de Reserva
        ];
    }

    public function headings(): array
    {
        return [
            'Cliente',
            'Sala',
            'Fecha',
            'Hora de Reserva',
        ];
    }
}
