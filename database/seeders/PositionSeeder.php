<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('positions')->insert([
            [
                'name_position' => 'Gerente de Sucursal',
                'description_position' => 'Supervisión y gestión general de la sucursal.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_position' => 'Asesor Comercial Senior',
                'description_position' => 'Venta de productos financieros y atención al cliente.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_position' => 'Analista de Cartera',
                'description_position' => 'Análisis y seguimiento de la cartera de clientes.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_position' => 'Soporte Técnico Nivel 1',
                'description_position' => 'Primer contacto y solución de problemas técnicos básicos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}