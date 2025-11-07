<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallets = Wallet::query()
            ->included() 
            ->filter()   
            ->sort()     
            ->getOrPaginate();

        return response()->json($wallets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ATENCIÓN: Las claves 'debt amount' y 'payment status' tienen espacios
        // y se usan tal cual en la validación, lo cual es inusual.
        $request->validate([
            'type_document' => 'required|string|max:50',
            'reference' => 'required|string|max:255|unique:wallets,reference',
            'debt amount' => 'required|numeric|min:0', 
            'expiration_date' => 'required|date',
            'payment status' => 'required|string|max:50',
        ]);

        $wallet = Wallet::create($request->all());
        
        return response()->json($wallet, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $wallet = Wallet::query()
            ->included()
            ->findOrFail($id);
            
        return response()->json($wallet);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $wallet = Wallet::find($id);
        
        if (!$wallet) {
            return response()->json(['message' => 'Registro de Cartera no encontrado'], 404);
        }

        // ATENCIÓN: Las claves 'debt amount' y 'payment status' tienen espacios
        $request->validate([
            'type_document' => 'required|string|max:50',
            'reference' => 'required|string|max:255|unique:wallets,reference,'.$id,
            'debt amount' => 'required|numeric|min:0', 
            'expiration_date' => 'required|date',
            'payment status' => 'required|string|max:50',
        ]);
        
        $wallet->update($request->all());
        
        return response()->json($wallet, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['message' => 'Registro de Cartera no encontrado'], 404);
        }

        $wallet->delete();
        
        return response()->json(null, 204);
    }
}
