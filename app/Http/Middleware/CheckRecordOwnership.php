<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Roles;
class CheckRecordOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $modelClass, $idParameter = 'id',$ownershipField = 'user_id'): Response
    {
        
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }
       
        // Get the record ID from the request
        $recordId = $request->route($idParameter);

        if (!$recordId) {
            abort(403, 'Record not found.');
        }

        // Get the model instance
        $model = $modelClass::where('unique_id', $recordId)->first();

        if (!$model) {
            abort(404, 'Record not found.');
        }

        // Check if the model has the isEditableBy method
        if (!method_exists($model, 'isEditableBy')) {
            // If no isEditableBy method, check if user is admin
            if (Auth::user()->role === 'admin') {
                return $next($request);
            }
            abort(403, 'You are not authorized to perform this action.');
        }

        // Check if user can edit/delete this record
        if (!$model->isEditableBy(Auth::id(), $ownershipField)) {
            abort(403, 'You are not authorized to perform this action.');
        }

        return $next($request);
    }
} 