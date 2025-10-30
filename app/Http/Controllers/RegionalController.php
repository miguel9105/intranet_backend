<?php

namespace App\Http\Controllers;

use App\Models\Regional;
use Illuminate\Http\Request;


class RegionalController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regionals = Regional::query()
        ->included() // Maneja la inclusión de users y posts
        ->filter()   // Aplica los filtros
        ->sort()     // Aplica el ordenamiento
        ->getOrPaginate(); // Devuelve get() o paginate()

    return response()->json($regionals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validacion de campos de la tabla
        $request->validate([
            'name_regional' => 'required|max:255',
            'ubication_regional'  => 'required|max:255',
        ]);
        //funcion para ingresar datos en la tabla
        $regional = Regional::create([
            'name_regional' => $request->name_regional,
            'ubication_regional'  => $request->ubication_regional,
        ]);
        // respuesta del almacenamiento de los datos 
        return response()->json($regional, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //funciones para buscar los datos por id
        $regional = Regional::FindOrFail($id);
        return response()-> json($regional);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, $id)
    {
        $request->validate([
            'name_regional' => 'required|max:255',
            'ubication_regional'  => 'required|max:255',
        ]);
        //busqueda de los datos de la tabla
        $regional = Regional::find($id);
        if (!$regional) {
            return response()->json(['message' => 'empresa no encontrado'], 404);
        }
        // ingreso del nuevo dato a la tabla
        $regional->update([
            'name_regional' => $request->name_regional,
            'ubication_regional'  => $request->ubication_regional,
            
        ]);
        //respuesta de la actualizacion de dato 
        return response()->json($regional, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //busqueda del dato por id
         $regional = Regional::find($id);
        // validacion del dato si existe el registro
        if (!$regional) {
            return response()->json(['message' => 'empresa no encontrado'], 404);
        }
        // metodo para elimincacion del dato
        $regional->delete();
        // repsuesta de la eliminacion del dato
        return response()->json(['message' => 'empresa eliminado'], 204);
    }
}
