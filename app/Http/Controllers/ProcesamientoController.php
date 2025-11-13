<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataCreditoApiService;

class ProcesamientoController extends Controller
{
    protected $apiService;

    public function __construct(DataCreditoApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Maneja la subida local, la subida a S3 y el inicio del procesamiento.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function procesar(Request $request) 
    {
        // 1. Validar la solicitud
        $request->validate([
            'plano_file' => 'required|file|mimes:txt', // Asumo que el plano es .txt
            'correcciones_file' => 'required|file|mimes:xlsx', // Asumo que correcciones es .xlsx
            'empresa' => 'required|string',
        ]);
        
        $planoFile = $request->file('plano_file');
        $correccionesFile = $request->file('correcciones_file');
        $empresa = $request->input('empresa');
        
        try {
            // --- PASO 1: Generar URLs de subida ---
            $urlsResponse = $this->apiService->generarUrlsSubida(
                $planoFile->getClientOriginalName(),
                $correccionesFile->getClientOriginalName()
            );
            
            $planoUpload = $urlsResponse['plano'];
            $correccionesUpload = $urlsResponse['correcciones'];

            // --- PASO 2: Subir archivos a S3 directamente ---
            
            // Subida del archivo plano (getMimeType() debe ser 'text/plain')
            $this->apiService->subirArchivoAS3(
                $planoUpload['upload_url'], 
                $planoFile->getPathname(),
                $planoFile->getMimeType() 
            );
            
            // Subida del archivo de correcciones (getMimeType() debe ser 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            $this->apiService->subirArchivoAS3(
                $correccionesUpload['upload_url'], 
                $correccionesFile->getPathname(),
                $correccionesFile->getMimeType() 
            );
            
            // --- PASO 3: Iniciar procesamiento en la API de App Runner ---
            $inicioResponse = $this->apiService->iniciarProcesamiento(
                $planoUpload['key'], 
                $correccionesUpload['key'], 
                $empresa
            );

            // Respuesta exitosa al cliente (código 202 Accepted)
            return response()->json([
                'success' => true,
                'message' => 'Archivos subidos y procesamiento iniciado.',
                'api_response' => $inicioResponse,
                'status_code' => 202
            ], 202);

        } catch (\Exception $e) {
            // Manejar errores
            return response()->json([
                'success' => false,
                'message' => 'Fallo en el proceso de carga o inicialización.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
}