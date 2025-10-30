<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //metodos los cuales nos permiten relacionar los datos
        $positions = Position::query()
        ->included() // Maneja la inclusión de users y posts
        ->filter()   // Aplica los filtros
        ->sort()     // Aplica el ordenamiento
        ->getOrPaginate(); // Devuelve get() o paginate()

    return response()->json($positions);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name_position'=> 'require|max:255',
            'description_position'=> 'require|max:255',
        ]);

        $position = Position::create([
          'name_position'=> $request->name_position,
          'description_position' => $request->description_position,
        ]);
        return response()->json($position);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $position=Position::findOrFail($id);
        return response()->json($position);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name_position'=>  'require|max::255',
            'description_position'=>  'require|max::255',
        ]);

        $position = Position::find($id);
        if (!$position) {
            return response()->json(['message'=> 'puesto no encontrada'], 404);
        }
         // ingreso del nuevo dato a la tabla
        $position->update([
            'name_position' => $request->name_position,
            'description_position'  => $request->description_position,
            
        ]);
        //respuesta de la actualizacion de dato 
        return response()->json($position, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //busqueda del dato por id
         $position = Position::find($id);
        // validacion del dato si existe el registro
        if (!$position) {
            return response()->json(['message' => 'cargo no encontrado'], 404);
        }
        // metodo para elimincacion del dato
        $position->delete();
        // repsuesta de la eliminacion del dato
        return response()->json(['message' => 'cargo eliminado'], 204);
    }
}
