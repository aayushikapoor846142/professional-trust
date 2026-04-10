<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetectUserLocation
{
    public function handle(Request $request, Closure $next)
    {
        // Call detectUserLocation() and attach to the request or log as needed
        // $location = detectUserLocation();
        
        // // Attach the location data to the request (optional)
        // $request->attributes->set('user_location', $location);

        return $next($request);
    }
}
