<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        //metodos los cuales nos permiten relacionar los datos
        $users = User::query()
        ->included() // Maneja la inclusión de users y posts
        ->filter()   // Aplica los filtros
        ->sort()     // Aplica el ordenamiento
        ->getOrPaginate(); // Devuelve get() o paginate()

    return response()->json($users);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name_user'=> 'require|max:255',
            'last_name_user' => 'require|max:255',
            'email'=> 'require|max:255',
            'number_document'=> 'require|max:255',
        ]);

        $user = User::create([
          'name_user'=> $request->name_user,
          'last_name_user' => $request->last_name_user,
          'email'=> $request->email,
          'number_document'=> $request->number_document,
        ]);
        return response()->json($user);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $user = User ::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
           'name_user'=> 'require|max:255',
            'last_name_user' => 'require|max:255',
            'email'=> 'require|max:255',
            'number_document'=> 'require|integer:255',
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message'=> 'usuario no encontrada'], 404);
        }
         // ingreso del nuevo dato a la tabla
        $user->update([
            'name_user'=> $request->name_user,
            'last_name_user' => $request->last_name_user,
            'email'=> $request->email,
            'number_document'=> $request->number_document,
            
        ]);
        //respuesta de la actualizacion de dato 
        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //busqueda del dato por id
         $user = User::find($id);
        // validacion del dato si existe el registro
        if (!$user) {
            return response()->json(['message' => 'usuario no encontrado'], 404);
        }
        // metodo para elimincacion del dato
        $user->delete();
        // repsuesta de la eliminacion del dato
        return response()->json(['message' => 'usuario eliminado'], 204);
    }
}
