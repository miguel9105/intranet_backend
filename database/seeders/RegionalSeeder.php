<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('regionals')->insert([
            [
                'name_regional' => 'nacional',
                'ubication_regional' => 'popayan cra7 ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_regional' => 'carrera septima',
                'ubication_regional' => 'calle 7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
           
        ]);
    }
}