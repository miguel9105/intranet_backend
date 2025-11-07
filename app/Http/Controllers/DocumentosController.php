<?php

namespace App\Http\Controllers;

use App\Models\Documentos;
use Illuminate\Http\Request;

class DocumentosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $registros = Documentos::query()
            ->included()
            ->filter()
            ->sort()
            ->getOrPaginate();

        return response()->json($registros);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ Debe definir las reglas de validación específicas
        $request->validate([
            // 'nombre_documento' => 'required|max:255',
        ]);

        $registro = Documentos::create($request->all());
        
        return response()->json($registro, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $registro = Documentos::query()
            ->included()
            ->findOrFail($id);
            
        return response()->json($registro);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $registro = Documentos::find($id);
        
        if (!$registro) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        // ✅ Debe definir las reglas de validación específicas
        $request->validate([
            // 'nombre_documento' => 'required|max:255',
        ]);
        
        $registro->update($request->all());
        
        return response()->json($registro, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $registro = Documentos::find($id);

        if (!$registro) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        $registro->delete();
        
        return response()->json(null, 204);
    }
}
