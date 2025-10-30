<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name_role' => 'auxiliar',
            'description_role' => 'encargado de generar informes contables ',
        ]);

        Role::create([
            'name_role' => 'coordinador ',
            'description_role' => 'responsable del area financiera',
        ]);
    }
}
