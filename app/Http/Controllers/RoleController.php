<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

// class RoleController extends Controller
// {
//       /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//         //metodos los cuales nos permiten relacionar los datos
//         $roles = Role::query()
//         ->included() // Maneja la inclusión de users y posts
//         ->filter()   // Aplica los filtros
//         ->sort()     // Aplica el ordenamiento
//         ->getOrPaginate(); // Devuelve get() o paginate()

//     return response()->json($roles);
//     }
//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         //  'require' cambiado a 'required'
//         $request->validate([
//             'name_role'=> 'required|max:255',
//             'description_role'=> 'required|max:255',
//         ]);

//         $role = Role::create([
//           'name_role'=> $request->name_role,
//           'description_role' => $request->description_role,
//         ]);
//         return response()->json($role);
//     }

//     /**
//      * Display the specified resource.
//      */
//     public function show($id)
//     {
//         //
//         $role = Role ::findOrFail($id);
//         return response()->json($role);
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, $id)
//     {
//         //'require' cambiado a 'required' y se eliminó el doble ':' en 'max:255'
//         $request->validate([
//             'name_role'=>  'required|max:255',
//             'description_role'=>  'required|max:255',
//         ]);

//         $role = Role::find($id);
//         if (!$role) {
//             return response()->json(['message'=> 'rol no encontrado'], 404);
//         }
//          // ingreso del nuevo dato a la tabla
//         $role->update([
//             'name_role' => $request->name_role,
//             'description_role'  => $request->description_role,
            
//         ]);
//         //respuesta de la actualizacion de dato 
//         return response()->json($role, 200);
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy($id)
//     {
//         //busqueda del dato por id
//          $role = Role::find($id);
//         // validacion del dato si existe el registro
//         if (!$role) {
//             return response()->json(['message' => 'rol no encontrado'], 404);
//         }
//         // metodo para elimincacion del dato
//         $role->delete();
//         // repsuesta de la eliminacion del dato
//         return response()->json(null, 204);
//     }


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