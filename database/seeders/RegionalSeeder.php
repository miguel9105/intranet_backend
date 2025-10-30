<?php

namespace Database\Seeders;

use App\Models\Regional;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Regional::create([
            'name_regional'=> 'nacional',
            'ubication_regional'=> 'cra 7 -67-45',
        ]);
        Regional::create([
            'name_regional'=> 'popayan sur',
            'ubication_regional'=> 'cra 5 a',
        ]);
    }
}
