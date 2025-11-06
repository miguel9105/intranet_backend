<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertamos registros en la tabla pivot 'role_users'
        DB::table('role_users')->insert([
            // 1. Administrador (user_id 1 -> role_id 1)
            ['user_id' => 1, 'role_id' => 1], 
            
            // 2. Gestor (user_id 2 -> role_id 2)
            ['user_id' => 2, 'role_id' => 2], 
            
            // 3. Administrativo (user_id 3 -> role_id 3)
            ['user_id' => 3, 'role_id' => 3], 
            
            // 4. Asesor (user_id 4 -> role_id 4)
            ['user_id' => 4, 'role_id' => 4],
            
            // Opcional: Asignar múltiples roles a un usuario (ej: El Administrador también es Gestor)
            // ['user_id' => 1, 'role_id' => 2], 
        ]);
    }
}