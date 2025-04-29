<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Sala;
use Carbon\Carbon;

class ReservaSeeder extends Seeder
{
    public function run()
    {
        // Asegúrate de que existan al menos 1 usuario y 1 sala
        $user = User::first();
        $salas = Sala::all();

        if (! $user || $salas->isEmpty()) {
            $this->command->info('Faltan usuarios o salas para crear reservas.');
            return;
        }

        // Crear 5 reservas de ejemplo
        foreach ($salas->take(5) as $index => $sala) {
            Reserva::create([
                'user_id'      => $user->id,
                'sala_id'      => $sala->id,
                'estado_id'    => 1, // pendiente
                'fecha'        => Carbon::today()->addDays($index),
                'hora_inicio'  => Carbon::now()->setTime(9 + $index, 0)->toTimeString(),
            ]);
        }

        $this->command->info('Seeder de reservas ejecutado correctamente.');
    }
}
