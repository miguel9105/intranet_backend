<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

     class RoleController extends Controller
{
    /**
     * Muestra una lista de todos los roles con sus permisos.
     * GET /api/roles
     */
    public function index()
    {
        // Usamos with('permissions') para cargar los permisos de cada rol
        // y evitar el problema de N+1 consultas.
        $roles = Role::with('permissions')->get();
        
        return response()->json($roles);
    }

    /**
     * Crea un nuevo rol y le asigna permisos.
     * POST /api/roles
     * Body: { "name": "Auditor", "permissions": ["ver inventario"] }
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name' // Valida que cada permiso exista
        ]);

        // 1. Crear el rol
        $role = Role::create(['name' => $request->name]);

        // 2. Asignar permisos si se enviaron
        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        // Devolvemos el rol cargando la relación de permisos
        return response()->json($role->load('permissions'), 201);
    }

    /**
     * Muestra un rol específico con sus permisos.
     * GET /api/roles/{role}
     */
    public function show(Role $role) // Usamos Route Model Binding
    {
        // Cargamos los permisos del rol específico
        return response()->json($role->load('permissions'));
    }

    /**
     * Actualiza el nombre de un rol y/o su lista de permisos.
     * PUT /api/roles/{role}
     * Body: { "name": "Auditor Senior", "permissions": ["ver inventario", "gestionar documentos"] }
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            // 'sometimes' significa que solo valida si el campo está presente
            'name' => [
                'sometimes',
                'string',
                Rule::unique('roles')->ignore($role->id) // Ignora su propio nombre
            ],
            'permissions' => 'sometimes|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        // 1. Actualizar el nombre si se proporcionó
        if ($request->has('name')) {
            $role->update(['name' => $request->name]);
        }

        // 2. Sincronizar permisos si se proporcionaron
        if ($request->has('permissions')) {
            // syncPermissions es la magia de Spatie:
            // Quita los permisos antiguos y añade solo los nuevos de la lista.
            $role->syncPermissions($request->permissions);
        }

        // Devolvemos el rol actualizado con sus permisos
        return response()->json($role->load('permissions'));
    }

    /**
     * Elimina un rol.
     * DELETE /api/roles/{role}
     */
    public function destroy(Role $role)
    {
        $role->delete();

        // 204 No Content: Éxito, pero no hay nada que devolver
        return response()->json(null, 204);
    }
}