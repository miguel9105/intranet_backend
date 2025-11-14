<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataCreditoApiService
{
    protected $baseUrl;

    public function __construct()
    {
        // Obtiene la URL base desde el .env
        $this->baseUrl = env('APP_RUNNER_API_URL');
        // Asegúrate de que APP_RUNNER_API_URL en tu .env tenga el valor:
        // APP_RUNNER_API_URL=https://bs73iwqiqd.us-east-2.awsapprunner.com/api/v1
    }

    /**
     * PASO 1: Llama al endpoint de tu API para obtener URLs pre-firmadas de S3.
     */
    public function generarUrlsSubida(string $planoFilename, string $correccionesFilename): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/generar_urls_subida", [ 
                'plano_filename' => $planoFilename,
                'correcciones_filename' => $correccionesFilename,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Error al generar URLs de subida: ' . $response->body());
            throw new \Exception('API Error: No se pudo generar las URLs para subir archivos.');

        } catch (\Exception $e) {
            Log::error('Fallo de conexión al generar URLs: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * PASO 2: Sube un archivo a la URL pre-firmada de S3.
     * Es CRUCIAL que el Content-Type coincida con lo que espera S3/la URL pre-firmada.
     */
    public function subirArchivoAS3(string $uploadUrl, string $filePath, string $mimeType): bool
    {
        try {
            // Lee el contenido binario del archivo local
            $fileContent = file_get_contents($filePath);
            
            if ($fileContent === false) {
                 throw new \Exception("No se pudo leer el archivo local: {$filePath}");
            }

            // Integración de timeout(60) para evitar el cURL error 28 en subidas largas.
            // Se envía el contenido con el Content-Type correcto.
           $response = Http::withBody($fileContent, $mimeType)
               ->timeout(300) // Cambiar de 180 a 300 segundos
               ->put($uploadUrl);
               
            if ($response->successful()) {
                return true;
            }

            Log::error("Error al subir archivo a S3 ({$mimeType}): " . $response->body());
            throw new \Exception('Fallo al subir el archivo a S3 pre-firmado. Respuesta S3: ' . $response->body());

        } catch (\Exception $e) {
            // El mensaje de error ahora incluye la URL de S3 que falló para el diagnóstico.
            Log::error("Fallo de conexión o lectura al subir a S3 ({$uploadUrl}): " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * PASO 3: Llama al endpoint de tu API para iniciar el procesamiento en segundo plano.
     */
    public function iniciarProcesamiento(string $planoKey, string $correccionesKey, string $empresa): array
    {
        try {
            // Se asume que el segundo endpoint es {{baseURL}}/api/v1/iniciar_procesamiento_datacredito
            $response = Http::post("{$this->baseUrl}/iniciar_procesamiento_datacredito", [
                'plano_key' => $planoKey,
                'correcciones_key' => $correccionesKey,
                'empresa' => $empresa,
            ]);

            // Se espera un 202 'Accepted' para una operación asíncrona.
            if ($response->status() === 202 || $response->successful()) { 
                return $response->json();
            }

            Log::error('Error al iniciar procesamiento: ' . $response->body());
            throw new \Exception('API Error: No se pudo iniciar el procesamiento de los archivos.');

        } catch (\Exception $e) {
            Log::error('Fallo de conexión al iniciar procesamiento: ' . $e->getMessage());
            throw $e;
        }
    }
}