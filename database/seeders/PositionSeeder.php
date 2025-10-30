<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Position::create([
            'name_position' => 'auxiliar contable',
            'description_position' => 'encargado de generar informes contables ',
        ]);

        Position::create([
            'name_position' => 'coordinador financiero',
            'description_position' => 'responsable del area financiera',
        ]);
    }
}
