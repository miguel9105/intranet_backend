<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventories = Inventory::query()
            ->included() 
            ->filter()   
            ->sort()     
            ->getOrPaginate();

        return response()->json($inventories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_product' => 'required|string|max:255',
            'code_sku' => 'required|string|max:100|unique:inventories,code_sku',
            'amount' => 'required|integer|min:0',
            'pursache_price' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $inventory = Inventory::create($request->all());
        
        return response()->json($inventory, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $inventory = Inventory::query()
            ->included()
            ->findOrFail($id);
            
        return response()->json($inventory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inventory = Inventory::find($id);
        
        if (!$inventory) {
            return response()->json(['message' => 'Registro de Inventario no encontrado'], 404);
        }

        $request->validate([
            'name_product' => 'required|string|max:255',
            // El 'code_sku' debe ser único excluyendo el registro actual
            'code_sku' => 'required|string|max:100|unique:inventories,code_sku,'.$id,
            'amount' => 'required|integer|min:0',
            'pursache_price' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        
        $inventory->update($request->all());
        
        return response()->json($inventory, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(['message' => 'Registro de Inventario no encontrado'], 404);
        }

        $inventory->delete();
        
        return response()->json(null, 204);
    }
}
