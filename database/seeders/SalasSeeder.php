<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sala;

class SalasSeeder extends Seeder
{
    public function run(): void
    {
        $salas = [
            ['nombre' => 'Sala A', 'capacidad' => 10, 'disponible' => true],
            ['nombre' => 'Sala B', 'capacidad' => 20, 'disponible' => false],
            ['nombre' => 'Sala C', 'capacidad' => 15, 'disponible' => true]
        ];

        foreach ($salas as $sala) {
            Sala::create($sala);
        }
    }
}
