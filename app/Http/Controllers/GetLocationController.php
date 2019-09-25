<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GetLocationController extends Controller
{
    public function getLocation(Request $request)
    {
        $ip = $request->get('ip');

        $location = \geoip()->getlocation($ip);

        $lat = $location->getAttribute('lat');
        $long = $location->getAttribute('lon');

        $data['lat'] = $lat;
        $data['long'] = $long;

        Log::info($data);

        return response()->json([
            'data' => $data
        ], 200);
    }
}
