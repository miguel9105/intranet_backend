<?php

namespace App\Http\Controllers;
use App\Models\Company;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::query()
        ->included() // Maneja la inclusión de users y posts
        ->filter()   // Aplica los filtros
        ->sort()     // Aplica el ordenamiento
        ->getOrPaginate(); // Devuelve get() o paginate()

    return response()->json($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validacion de campos de la tabla
        $request->validate([
            'name_company' => 'required|max:255',
            'ubication'  => 'required|max:255',
        ]);
        //funcion para ingresar datos en la tabla
        $company = Company::create([
            'name_company' => $request->name_company,
            'ubication'  => $request->ubication,
        ]);
        // respuesta del almacenamiento de los datos 
        return response()->json($company, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //funciones para buscar los datos por id
        $company = Company::FindOrFail($id);
        return response()-> json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, $id)
    {
        $request->validate([
            'name_company' => 'required|max:255',
            'ubication'  => 'required|max:255',
        ]);
        //busqueda de los datos de la tabla
        $company = Company::find($id);
        if (!$company) {
            return response()->json(['message' => 'empresa no encontrado'], 404);
        }
        // ingreso del nuevo dato a la tabla
        $company->update([
            'name_company' => $request->name_company,
            'ubication'  => $request->ubication,
            
        ]);
        //respuesta de la actualizacion de dato 
        return response()->json($company, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //busqueda del dato por id
         $company = Company::find($id);
        // validacion del dato si existe el registro
        if (!$company) {
            return response()->json(['message' => 'empresa no encontrado'], 404);
        }
        // metodo para elimincacion del dato
        $company->delete();
        // repsuesta de la eliminacion del dato
        return response()->json(['message' => 'empresa eliminado'], 204);
    }
}
