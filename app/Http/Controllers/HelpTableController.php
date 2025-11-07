<?php

namespace App\Http\Controllers;

use App\Models\Help_Table;
use Illuminate\Http\Request;

class HelpTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $helptables = Help_Table::query()
            ->included() 
            ->filter()   
            ->sort()     
            ->getOrPaginate();

        return response()->json($helptables);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title_help_table' => 'required|string|max:255',
            'description_help_table' => 'required|string',
            // Nota: 'state-help_table' no es un buen nombre de campo, usar snake_case es mejor.
            'state-help_table' => 'required|string|max:50', 
            'priority' => 'required|string|max:50',
            'asignado_a_user_id' => 'nullable|exists:users,id',
        ]);

        $helptable = Help_Table::create($request->all());
        
        return response()->json($helptable, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $helptable = Help_Table::query()
            ->included()
            ->findOrFail($id);
            
        return response()->json($helptable);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $helptable = Help_Table::find($id);
        
        if (!$helptable) {
            return response()->json(['message' => 'Registro de Mesa de Ayuda no encontrado'], 404);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title_help_table' => 'required|string|max:255',
            'description_help_table' => 'required|string',
            'state-help_table' => 'required|string|max:50',
            'priority' => 'required|string|max:50',
            'asignado_a_user_id' => 'nullable|exists:users,id',
        ]);
        
        $helptable->update($request->all());
        
        return response()->json($helptable, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $helptable = Help_Table::find($id);

        if (!$helptable) {
            return response()->json(['message' => 'Registro de Mesa de Ayuda no encontrado'], 404);
        }

        $helptable->delete();
        
        return response()->json(null, 204);
    }
}
