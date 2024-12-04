<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class NASAController extends Controller
{
    public function getInstruments()
    {
        // Cargar las rutas desde config/donki.php
        $routes = config('donki.routes');
        $client = new Client();
        $results = [];

        foreach ($routes as $routeName => $endpoint) {
            // Consumir cada endpoint dinámicamente
            $response = $client->get("https://api.nasa.gov/DONKI/{$endpoint}", [
                'query' => [
                    'api_key' => env('NASA_API_KEY'),
                ],
            ]);

            // Decodificar y procesar los datos
            $data = json_decode($response->getBody(), true);

            foreach ($data as $item) {
                if (isset($item['instruments'])) {
                    foreach ($item['instruments'] as $instrument) {
                        $results[] = $instrument['displayName'];
                    }
                }
            }
        }

        // Eliminar duplicados y retornar los instrumentos únicos
        $uniqueInstruments = array_unique($results);

        return response()->json(['instruments' => $uniqueInstruments]);
    }
}