<?php

namespace Database\Seeders;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        //metodo creacion de registros en la tabla company 
        Company::create([
            'name_company' => 'Finansueños S.A.S.',
            'ubication' => 'bogota.dc',
        ]);

        Company::create([
            'name_company' => 'electrocreditos del cauca',
            'ubication' => 'carrera 7',
        ]);
    }
}
