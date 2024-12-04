<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DonkiService;

class DonkiController extends Controller
{
    protected $donkiService;

    public function __construct(DonkiService $donkiService)
    {
        $this->donkiService = $donkiService;
    }

    public function getInstruments()
    {
        $endpoints = config('donki.routes');
        $instruments = [];

        foreach ($endpoints as $endpoint) {
            $data = $this->donkiService->fetchData($endpoint);

            foreach ($data as $item) {
                if (isset($item['instruments'])) {
                    foreach ($item['instruments'] as $instrument) {
                        $instruments[] = $instrument['displayName'];
                    }
                }
            }
        }

        return response()->json(array_unique($instruments));
    }

    public function getActivityIds()
    {
        $endpoints = config('donki.routes');
        $routes = config('donki.routes');
        $client = new Client();
        $activityIds = [];

        foreach ($routes as $routeName => $endpoint) {
            $response = $client->get("https://api.nasa.gov/DONKI/{$endpoint}", [
                'query' => [
                    'api_key' => env('NASA_API_KEY'),
                ],
            ]);
    
            $data = json_decode($response->getBody(), true);
    
            foreach ($data as $item) {
                if (isset($item['activityID'])) {
                    $activityIds[] = $item['activityID'];
                }
            }
        }
    
        $uniqueActivityIds = array_unique($activityIds);
    
        return response()->json([
            'total_activities' => count($uniqueActivityIds),
            'activity_ids' => $uniqueActivityIds,
        ]);
    }
    public function getInstrumentUsage()
    {
        $endpoints = config('donki.routes');
        $instrumentCount = [];
        $totalCount = 0;

        foreach ($endpoints as $endpoint) {
            $data = $this->donkiService->fetchData($endpoint);

            foreach ($data as $item) {
                if (isset($item['instruments'])) {
                    foreach ($item['instruments'] as $instrument) {
                        $name = $instrument['displayName'];
                        $instrumentCount[$name] = ($instrumentCount[$name] ?? 0) + 1;
                        $totalCount++;
                    }
                }
            }
        }

        $usage = [];
        foreach ($instrumentCount as $instrument => $count) {
            $usage[$instrument] = $count / $totalCount;
        }

        return response()->json($usage);
    }

    public function getInstrumentPercentage(Request $request)
{
    $instrumentName = $request->input('instrument');
    if (!$instrumentName) {
        return response()->json(['error' => 'Instrument name is required.'], 400);
    }

    $endpoints = config('donki.routes');
    $count = 0;
    $totalCount = 0;

    foreach ($endpoints as $endpoint) {
        $data = $this->donkiService->fetchData($endpoint);

        foreach ($data as $item) {
            if (isset($item['instruments'])) {
                foreach ($item['instruments'] as $instrument) {
                    $totalCount++;
                    if ($instrument['displayName'] === $instrumentName) {
                        $count++;
                    }
                }
            }
        }
    }

    if ($totalCount === 0) {
        return response()->json(['error' => 'No data found to calculate percentages.'], 404);
    }

    $percentage = $count / $totalCount;

    return response()->json([
        'instrument' => $instrumentName,
        'percentage' => $percentage,
    ]);
}
}
