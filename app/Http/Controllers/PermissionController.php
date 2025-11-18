<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Muestra una lista de todos los permisos.
     * GET /api/permissions
     */
    public function index()
    {
        return response()->json(Permission::all());
    }

    /**
     * Crea un nuevo permiso.
     * POST /api/permissions
     * Body: { "name": "gestionar contabilidad" }
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $request->name]);

        return response()->json($permission, 201);
    }

    /**
     * Muestra un permiso específico.
     * GET /api/permissions/{permission}
     */
    public function show(Permission $permission) // Usamos Route Model Binding
    {
        return response()->json($permission);
    }

    /**
     * Actualiza el nombre de un permiso.
     * PUT /api/permissions/{permission}
     * Body: { "name": "gestionar contabilidad avanzada" }
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('permissions')->ignore($permission->id) // Ignora su propio nombre
            ],
        ]);

        $permission->update(['name' => $request->name]);

        return response()->json($permission);
    }

    /**
     * Elimina un permiso.
     * DELETE /api/permissions/{permission}
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        
        return response()->json(null, 204);
    }
}