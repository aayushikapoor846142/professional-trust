<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppendSessionIdToUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('get') && !$request->ajax()) {
            // Check if session ID exists in the session storage
            if (!$request->session()->has('sid')) {
                // Generate a new unique session ID and store it in the session
                $sessionId = \Str::uuid();
                $request->session()->put('sid', $sessionId);
            } else {
                // Retrieve existing session ID from the session
                $sessionId = $request->session()->get('sid');
            }

            // Check if the request URL already contains the session ID
            if (!$request->get('sid')) {
                // Append the session ID to the current URL and redirect
                $current_url = url()->current();
                return redirect()->to($current_url."?sid=".$sessionId);
            }
        }

        // If everything is fine, continue with the request
        return $next($request);
    }
}
