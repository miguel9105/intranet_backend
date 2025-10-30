<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //metodos los cuales nos permiten relacionar los datos
        $roles = Role::query()
        ->included() // Maneja la inclusión de users y posts
        ->filter()   // Aplica los filtros
        ->sort()     // Aplica el ordenamiento
        ->getOrPaginate(); // Devuelve get() o paginate()

    return response()->json($roles);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name_role'=> 'require|max:255',
            'description_role'=> 'require|max:255',
        ]);

        $role = Role::create([
          'name_role'=> $request->name_role,
          'description_role' => $request->description_role,
        ]);
        return response()->json($role);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $role = Role ::findOrFail($id);
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name_role'=>  'require|max::255',
            'description_role'=>  'require|max::255',
        ]);

        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message'=> 'rol no encontrada'], 404);
        }
         // ingreso del nuevo dato a la tabla
        $role->update([
            'name_role' => $request->name_role,
            'description_role'  => $request->description_role,
            
        ]);
        //respuesta de la actualizacion de dato 
        return response()->json($role, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //busqueda del dato por id
         $role = Role::find($id);
        // validacion del dato si existe el registro
        if (!$role) {
            return response()->json(['message' => 'rol no encontrado'], 404);
        }
        // metodo para elimincacion del dato
        $role->delete();
        // repsuesta de la eliminacion del dato
        return response()->json(['message' => 'rol eliminado'], 204);
    }
}
