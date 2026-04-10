<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscriptionHistory;
use App\Models\MembershipPlanFeatureValue;
use App\Models\StaffUser;

class CheckSubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $userId = Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type', 'membership')
            ->where('subscription_status', 'active')
            ->first();

        if (!$subscription) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No active subscription found. Please subscribe to access staff management features.'
                ], 403);
            }
            
            return response()->view('errors.plan-subscription', [
                'message' => 'No active subscription found. Please subscribe to access staff management features.'
            ], 403);
        }

        // Check if plan allows staff management
        $configure = MembershipPlanFeatureValue::where('membership_plan_id', $subscription->membership_plans_plan_id)
            ->where('feature_key', 'staff_add')
            ->first();

        if (!empty($configure) && $configure->value == 0) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Staff management is not available in your current subscription plan.'
                ], 403);
            }
            
            return response()->view('errors.plan-subscription', [
                'message' => 'Staff management is not available in your current subscription plan.'
            ], 403);
        }

        $currentStaffCount = StaffUser::where('added_by', $userId)->count();
        // Get limit based on plan feature
        $limit = $this->getStaffLimit($subscription->membership_plans_plan_id);


        if ($limit !== -1 && $currentStaffCount >= $limit) {
             return response()->view('errors.plan-subscription', [
                'message' => 'Staff management is not available in your current subscription plan.'
            ], 403);
        }
        

        return $next($request);
    }

    private function getStaffLimit($membershipPlanId)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $membershipPlanId)
            ->where('feature_key', 'staff_add')
            ->first();
            
        if (!$featureValue) {
            return 0; // No staff allowed
        }
        
        // Check if unlimited (-1) or specific limit
        return $featureValue->limit_value ?? 1; // Default to 1 if no limit specified
    }
} 