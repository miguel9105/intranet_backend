<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            // 1. Usuario Administrador (Password: 'password')
            [
                'name_user' => 'Juan',
                'last_name_user' => 'Admin',
                'email' => 'admin@test.com',
                'number_document' => '1000000000',
                'company_id' => 1,
                'regional_id' => 1,
                'position_id' => 1,
                'password' => Hash::make('password'), 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 2. Usuario Gestor (Password: 'password')
            [
                'name_user' => 'Maria',
                'last_name_user' => 'Gestora',
                'email' => 'gestor@test.com',
                'number_document' => '1000000001',
                'company_id' => 2, 
                'regional_id' => 2, 
                'position_id' => 4,
                'password' => Hash::make('password'), 
                'created_at' => now(), 
                'updated_at' => now(),
            ],
            // 3. Usuario Administrativo (Password: 'password')
            [
                'name_user' => 'Carlos', 'last_name_user' => 'Contable', 'email' => 'admini@test.com', 'number_document' => '1000000002',
                'company_id' => 1, 'regional_id' => 1, 'position_id' => 3,
                'password' => Hash::make('password'), 'created_at' => now(), 'updated_at' => now(),
            ],
            // 4. Usuario Asesor (Password: 'password')
            [
                'name_user' => 'Laura', 'last_name_user' => 'Ventas', 'email' => 'asesor@test.com', 'number_document' => '1000000003',
                'company_id' => 1, 'regional_id' => 2, 'position_id' => 2,
                'password' => Hash::make('password'), 'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}