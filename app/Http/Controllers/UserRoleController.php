<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    /**
     * Asigna un rol a un usuario.
     * POST /users/{id}/roles
     * Body: { "role_name": "Gestor" }
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_name' => 'required|string|exists:roles,name',
        ]);

        $user->assignRole($request->role_name); // ¡Magia de Spatie!

        return response()->json([
            'message' => 'Rol asignado exitosamente.',
            'roles' => $user->getRoleNames() // Devuelve la lista actualizada
        ]);
    }

    /**
     * Quita un rol a un usuario.
     * DELETE /users/{id}/roles/{role_id}
     */
    public function removeRole(User $user, Role $role)
    {
        if (!$user->hasRole($role)) {
            return response()->json(['message' => 'El usuario no tiene este rol.'], 400);
        }

        $user->removeRole($role); // ¡Magia de Spatie!

        return response()->json([
            'message' => 'Rol quitado exitosamente.',
            'roles' => $user->getRoleNames() // Devuelve la lista actualizada
        ]);
    }
}