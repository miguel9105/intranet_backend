<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'name_company' => 'Finansueños S.A.S.',
                'ubication' => 'Bogotá, Colombia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_company' => 'Electrocreditos del cauca',
                'ubication' => 'Medellín, Colombia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
        ]);
    }
}