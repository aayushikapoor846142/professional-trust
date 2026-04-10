<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAllRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // echo 'etere';exit();
        // if ($request->url() == url('unauthorised-professionals') || $request->url() == url('professionals') || $request->url() == url('register') || $request->url() == url('register/professional') || $request->url() == url('/')) {
        //     return redirect('report-unauthorized-professional');
        // }
        
         return $next($request);
    }
}
