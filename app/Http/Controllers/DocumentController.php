<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::query()
            ->included() 
            ->filter()   
            ->sort()     
            ->getOrPaginate();

        return response()->json($documents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_document' => 'required|string|max:255',
            'file_path' => 'required|string|max:255', // Deberías validar que sea una URL o path válido
            'type_document' => 'required|string|max:100',
            'version' => 'nullable|string|max:10',
        ]);

        $document = Document::create($request->all());
        
        return response()->json($document, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $document = Document::query()
            ->included()
            ->findOrFail($id);
            
        return response()->json($document);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $document = Document::find($id);
        
        if (!$document) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        $request->validate([
            'name_document' => 'required|string|max:255',
            'file_path' => 'required|string|max:255', 
            'type_document' => 'required|string|max:100',
            'version' => 'nullable|string|max:10',
        ]);
        
        $document->update($request->all());
        
        return response()->json($document, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        $document->delete();
        
        return response()->json(null, 204);
    }
}
