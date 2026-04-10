<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Professional;
use App\Models\Country;
use App\Models\Types;
use App\Models\LicenseType;

class CheckProfile
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (Auth::check()) {

      if (Auth::user()->role === 'professional') {
 
        if (Auth::user()->status === 'pending') {
          return redirect()->to('professional/approval-pending');
        } elseif (Auth::user()->status === 'draft') {
          return redirect()->to(mainTrustvisoryUrl().'/login')->withErrors('Your profile is incomplete. Please login and complete your profile');
        } elseif (Auth::user()->status === 'suspended' || Auth::user()->status === 'inactive') {
            return redirect()->to('account/suspend');
          }
        

      } elseif (Auth::user()->role === 'client') {
        if (Auth::user()->status === 'suspended' || Auth::user()->status === 'inactive') {
          return redirect()->to('account/suspend');
        }
      } 
    }

    return $next($request);
  }
}
