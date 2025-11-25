<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client; // Importamos Guzzle
use GuzzleHttp\Exception\RequestException;

class ProcesamientoDatacreditoController extends Controller
{
    private $client;
    private $apiUrl;

    public function __construct()
    {
        // 1. Creamos un cliente de Guzzle que apunta a nuestra API de Python
        $this->apiUrl = config('app.datacredito_api_url'); // Lee la URL del .env
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout'  => 30.0, // Timeout de 30 segundos
        ]);
    }

    /**
     * PASO 1: Pedir las URLs de S3 a la API de Python.
     * POST /api/procesamiento/generar-urls
     */
    public function generarUrls(Request $request)
    {
        // 2. Reenviamos la petición del frontend a la API de Python
        try {
            $response = $this->client->post('generar_urls_subida', [
                'json' => $request->all() // Reenvía el JSON exacto del frontend
            ]);

            // 3. Devolvemos la respuesta de Python al frontend
            return response()->json(
                json_decode($response->getBody()->getContents()), 
                $response->getStatusCode()
            );
        } catch (RequestException $e) {
            return $this->handleGuzzleError($e);
        }
    }

    /**
     * PASO 3: Iniciar el procesamiento en la API de Python.
     * POST /api/procesamiento/iniciar
     */
   public function iniciarProceso(Request $request)
{
    // 1. Validamos que tengamos las claves de S3
    $request->validate([
        'plano_key' => 'required|string',
        'correcciones_key' => 'required|string',
        'empresa' => 'required|string',
    ]);

    // 2. Reenviamos la petición al endpoint de Python
    try {
        // El endpoint en Python es 'iniciar_procesamiento_datacredito'
        $response = $this->client->post('iniciar_procesamiento_datacredito', [
            'json' => $request->all()
        ]);

        // 3. Devolvemos la respuesta de Python (debe incluir el 'output_key' para el polling)
        // Python debería retornar 200/202.
        return response()->json(
            json_decode($response->getBody()->getContents()),
            $response->getStatusCode()
        );
    } catch (RequestException $e) {
        // Utilizamos la función de manejo de errores de Guzzle que ya tiene definida
        return $this->handleGuzzleError($e);
    }
}

    /**
     * PASO 4: Verificar el estado del trabajo en la API de Python.
     * GET /api/procesamiento/estado
     */
    public function verificarEstado(Request $request)
    {
        if (!$request->has('key')) {
            return response()->json(['error' => 'Se requiere el "key"'], 400);
        }

        try {
            $response = $this->client->get('estado_procesamiento', [
                'query' => [
                    'key' => $request->query('key')
                ]
            ]);

            return response()->json(
                json_decode($response->getBody()->getContents()), 
                $response->getStatusCode()
            );
        } catch (RequestException $e) {
            return $this->handleGuzzleError($e);
        }
    }

    /**
     * Función helper para manejar errores de Guzzle
     */
    private function handleGuzzleError(RequestException $e)
    {
        if ($e->hasResponse()) {
            // Reenviar el error de la API de Python (ej. 400, 404, 500)
            return response()->json(
                json_decode($e->getResponse()->getBody()->getContents()), 
                $e->getResponse()->getStatusCode()
            );
        }
        // Error de red (ej. no se pudo conectar)
        return response()->json(['error' => 'No se pudo conectar al servicio de procesamiento.'], 503);
    }
}