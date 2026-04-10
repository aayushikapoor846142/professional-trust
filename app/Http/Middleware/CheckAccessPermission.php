<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\RolePrevilege;
use App\Models\Module;

class CheckAccessPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
  
        // return $next($request);

          // Check if user is authenticated
          if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }
     
         // Allow all actions for admin users
         if (Auth::user()->role === 'professional') {
            return $next($request);
        }
       // return $next($request);
        
        $role = Auth::user()->role;
        $routeName = $request->route()->getName(); 
        
        if($routeName == 'panel.list')
        {
            return $next($request);
        }
        $parts = explode('.', $routeName);
        $count = count($parts);
    
        if ($count < 2) {
            return $next($request);
        }
    
        
        $action = $parts[$count - 1];
      
        $module = $parts[$count - 2];
       
        $route_prefix = implode('.', array_slice($parts, 0, $count - 2)) . '.' . $module;
        $module = Module::with(['moduleAction'])->where('route_prefix',$route_prefix)->where('panel','professional')->first();
        
        $actions = $module->moduleAction->pluck('action')->toArray();
        
        if(!in_array($action,$actions)){
            return $next($request);
        }
       
        $hasAccess = checkPrivilege([
            'route_prefix' => $route_prefix,
            'action' => $action,
        ]);
        
        if (!$hasAccess) {
            if($request->ajax()){
                return response()->json(['status' => false,'message' => 'You do not have permission to perform this action.']);
            }
            return redirect()->route('unauthorized-access')->with('error', 'You do not have permission to perform this action.');
        }
    
        return $next($request);        
          

    }
}
