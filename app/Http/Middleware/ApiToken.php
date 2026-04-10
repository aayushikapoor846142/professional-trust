<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class ApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (! $request->user() || ! Auth::guard('sanctum')->check()) {
        //     $controller = app(BaseController::class);
        //     return $controller->unauthorizeToken('Unauthorized. Token not provided or invalid',400);
        // }
        return $next($request);
    }
}
