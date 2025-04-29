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
        Estado::create(['nombre' => 'pendiente']);
        Estado::create(['nombre' => 'aceptada']);
        Estado::create(['nombre' => 'rechazada']);
    }
}
