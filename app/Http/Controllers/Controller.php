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
        $routes = config('donki.routes');
        $client = new Client();
        $results = [];

        foreach ($routes as $routeName => $endpoint) {
            $response = $client->get("https://api.nasa.gov/DONKI/{$endpoint}", [
                'query' => [
                    'api_key' => env('NASA_API_KEY'),
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            foreach ($data as $item) {
                if (isset($item['instruments'])) {
                    foreach ($item['instruments'] as $instrument) {
                        $results[] = $instrument['displayName'];
                    }
                }
            }
        }

        $uniqueInstruments = array_unique($results);

        return response()->json(['instruments' => $uniqueInstruments]);
    }
}