<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Estado;

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            1 => 'pendiente',
            2 => 'aceptada',
            3 => 'rechazada',
        ];

        foreach ($estados as $id => $nombre) {
            Estado::updateOrCreate(
                ['id' => $id],
                ['nombre' => $nombre]
            );
        }
    }
}
