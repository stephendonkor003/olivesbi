<?php

namespace App\Http\Controllers\Locality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocalityBusinessController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lon' => ['required', 'numeric', 'between:-180,180'],
            'place' => ['nullable', 'string', 'max:255'],
        ]);

        $key = config('services.google_places.key');

        if (empty($key)) {
            return response()->json([
                'available' => false,
                'message' => 'Google Places API key is not configured.',
                'business_count' => null,
                'waste_collection_company_count' => null,
                'samples' => [
                    'businesses' => [],
                    'waste_collection_companies' => [],
                ],
            ]);
        }

        $lat = (float) $data['lat'];
        $lon = (float) $data['lon'];
        $place = trim($data['place'] ?? '');
        $area = $place !== '' ? $place : "{$lat}, {$lon}";

        $businesses = $this->searchPlaces("businesses in {$area}", $lat, $lon, $key);
        $wasteCompanies = $this->searchPlaces("waste collection companies near {$area}", $lat, $lon, $key);

        return response()->json([
            'available' => true,
            'message' => 'Google Places first-page search results. Counts are indicative, not a full registry.',
            'business_count' => count($businesses),
            'waste_collection_company_count' => count($wasteCompanies),
            'samples' => [
                'businesses' => array_slice($businesses, 0, 5),
                'waste_collection_companies' => array_slice($wasteCompanies, 0, 5),
            ],
        ]);
    }

    private function searchPlaces(string $query, float $lat, float $lon, string $key): array
    {
        $response = Http::timeout(12)->get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
            'query' => $query,
            'location' => "{$lat},{$lon}",
            'radius' => 3000,
            'key' => $key,
        ]);

        if (! $response->ok() || $response->json('status') === 'REQUEST_DENIED') {
            return [];
        }

        return collect($response->json('results', []))
            ->map(fn ($place) => [
                'name' => $place['name'] ?? 'Unnamed business',
                'address' => $place['formatted_address'] ?? null,
                'rating' => $place['rating'] ?? null,
            ])
            ->unique('name')
            ->values()
            ->all();
    }
}
