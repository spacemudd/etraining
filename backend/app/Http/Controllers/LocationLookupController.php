<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SKAgarwal\GoogleApi\PlacesApi;

class LocationLookupController extends Controller
{
    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     * @throws \SKAgarwal\GoogleApi\Exceptions\GooglePlacesApiException
     */
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $googlePlace = new PlacesApi(env('GOOGLE_PLACES_API_KEY'), true, [
            'headers' => [
                'referer' => env('APP_URL'),
            ]
        ]);

        return $googlePlace->placeAutocomplete($request->search);
    }
}
