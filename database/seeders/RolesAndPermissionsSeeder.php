<?php

namespace Database\Seeders;

// use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Resetear la caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- 2. CREAR PERMISOS ---
        // (Nombra los permisos basado en la "acción", no en el rol)
        Permission::create(['name' => 'gestionar documentos']);
        Permission::create(['name' => 'usar mesa de ayuda']);
        Permission::create(['name' => 'ver inventario']);
        Permission::create(['name' => 'gestionar usuarios']);
        Permission::create(['name' => 'gestionar roles']);


        // --- 3. CREAR ROLES y ASIGNAR PERMISOS ---
        
        // Rol Asesor (solo ve inventario)
        $asesorRole = Role::create(['name' => 'Asesor']);
        $asesorRole->givePermissionTo('ver inventario');

        // Rol Administrativo (Inventario y Mesa de Ayuda)
        $adminiRole = Role::create(['name' => 'Administrativo']);
        $adminiRole->givePermissionTo([
            'ver inventario',
            'usar mesa de ayuda'
        ]);

        // Rol Gestor (Documentos, Mesa de Ayuda, Inventario)
        $gestorRole = Role::create(['name' => 'Gestor']);
        $gestorRole->givePermissionTo([
            'gestionar documentos',
            'usar mesa de ayuda',
            'ver inventario'
        ]);

        // Rol Administrador (Tiene todos los permisos)
        $adminRole = Role::create(['name' => 'Administrador']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
