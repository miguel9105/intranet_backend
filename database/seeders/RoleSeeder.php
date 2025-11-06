<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🚨 IMPORTANTE: Los nombres deben coincidir exactamente con los usados en web.php y api.php
        DB::table('roles')->insert([
            [
                'name_role' => 'Administrador',
                'description_role' => 'Acceso y control total sobre la plataforma y la gestión de usuarios.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_role' => 'Gestor',
                'description_role' => 'Acceso a módulos de Cartera, Mesa de Ayuda e Inventario.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_role' => 'Administrativo',
                'description_role' => 'Acceso a módulos de Inventario y Mesa de Ayuda.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_role' => 'Asesor',
                'description_role' => 'Acceso limitado solo al módulo de Inventario.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}