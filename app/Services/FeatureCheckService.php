<?php

namespace App\Services;

use App\Models\UserSubscriptionHistory;
use App\Models\StaffUser;
use Illuminate\Support\Facades\Auth;
use App\Models\MembershipPlanFeatureValue;
use Illuminate\Support\Facades\Log;
use App\Models\CaseComment;
use App\Models\Article;

class FeatureCheckService
{
    /**
     * Check if user can add staff - Enhanced with upgrade scenarios and removal handling
     */
    public function canAddStaff($userId = null, $context = 'add')
    {
        $userId = $userId ?? Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type' ,'membership')
            ->where('subscription_status', 'active')
            ->first();
            
        if (!$subscription) {
            return [
                'allowed' => false,
                'message' => 'No active subscription found. Please subscribe to add staff members.',
                'has_subscription' => false,
                'current_count' => 0,
                'limit' => 0,
                'remaining' => 0,
                'usage_percentage' => 0,
                'plan_title' => 'No Plan'
            ];
        }
        
        // Check if plan allows staff management
        $configure = MembershipPlanFeatureValue::where('membership_plan_id',$subscription->membership_plans_plan_id)
            ->where('feature_key','staff_add')
            ->first();
       
        if(!empty($configure)){
            if ($configure->value == 0) {
                return [
                    'allowed' => false,
                    'message' => 'Staff management is not available in your current subscription plan.',
                    'has_subscription' => true,
                    'current_count' => 0,
                    'limit' => 0,
                    'remaining' => 0,
                    'usage_percentage' => 0,
                    'plan_title' => 'Plan Not Supported'
                ];
            }
        }
        
        // Get current staff count (only active staff)
        $currentStaffCount = $this->getActiveStaffCount($userId);
        
        // Get limit based on plan feature
        $limit = $this->getStaffLimit($subscription->membership_plans_plan_id);
        
        // Get plan details
        $plan = $subscription->membershipPlan;
        $planTitle = $plan->plan_title ?? 'Unknown Plan';
        
        // Calculate remaining slots
        $remaining = $limit === -1 ? -1 : $limit - $currentStaffCount;
        
        // Calculate usage percentage
        $usagePercentage = $limit > 0 ? round(($currentStaffCount / $limit) * 100, 2) : 0;
        
        // Determine if user can add staff
        $canAdd = $remaining > 0 || $remaining === -1;
        
        // Generate appropriate message based on context
        $message = $this->generateMessage($context, $canAdd, $currentStaffCount, $limit, $remaining, $planTitle);
        
        
        return [
            'allowed' => $canAdd,
            'message' => $message,
            'has_subscription' => true,
            'current_count' => $currentStaffCount,
            'limit' => $limit,
            'remaining' => $remaining,
            'usage_percentage' => $usagePercentage,
            'plan_title' => $planTitle,
            'plan_id' => $subscription->membership_plans_plan_id,
            'slots_freed' => $context === 'removal' ? 1 : 0
        ];
    }
    
    /**
     * Get staff limit for plan
     */
    private function getStaffLimit($membershipPlanId)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $membershipPlanId)
            ->where('feature_key', 'staff_add')
            ->first();
            
        if (!$featureValue) {
            return 0; // No staff allowed
        }
        
        // Check if unlimited (-1) or specific limit
        return $featureValue->value ?? 1; // Default to 1 if no limit specified
    }
    
    /**
     * Get active staff count for a user (excluding deleted staff)
     */
    private function getActiveStaffCount($userId)
    {
        // Count from UserPlanFeatureHistory table for staff_add feature
        $historyCount = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
            ->where('feature_key', 'staff_add')
            ->count();
            
        // If no history records exist, fallback to direct database count
        if ($historyCount == 0) {
            return StaffUser::where('added_by', $userId)
                ->whereHas('user', function($query) {
                    $query->whereNull('deleted_at'); // Only count non-deleted users
                })
                ->count();
        }
        
        return $historyCount;
    }
    
    /**
     * Generate appropriate message based on context
     */
    private function generateMessage($context, $canAdd, $currentCount, $limit, $remaining, $planTitle)
    {
        switch ($context) {
            case 'add':
                if ($canAdd) {
                    if ($remaining === -1) {
                        return "Staff management is available. You can add unlimited staff members.";
                    } else {
                        return "You’ve added {$currentCount} staff member. {$remaining} remaining under your current plan.";
                    }
                } else {
                    if ($limit === -1) {
                        return "You have reached the limit of unlimited staff members.";
                    } else {
                        return "You have reached the limit of {$limit} staff members. Please upgrade your subscription to add more staff members.";
                    }
                }
                
            case 'removal':
                if ($canAdd) {
                    return "Staff slot freed successfully. You can now add more staff members.";
                } else {
                    return "Staff removal completed but limit reached.";
                }
                
            case 'restore':
                if ($canAdd) {
                    return "Staff member can be restored.";
                } else {
                    return "Cannot restore staff member. Limit reached.";
                }
                
            case 'upgrade':
                if ($canAdd) {
                    return "Plan upgraded successfully. You can now add {$remaining} more staff member(s).";
                } else {
                    return "Plan upgraded but limit still reached.";
                }
                
            default:
                if ($canAdd) {
                    return "Staff management is available";
                } else {
                    return "Staff management is limited";
                }
        }
    }
    
    /**
     * Check staff limit after removal (wrapper for canAddStaff)
     */
    public function checkStaffLimitAfterRemoval($userId = null)
    {
        return $this->canAddStaff($userId, 'removal');
    }
    
    /**
     * Get detailed staff limit information (wrapper for canAddStaff)
     */
    public function getStaffLimitDetails($userId = null)
    {
        return $this->canAddStaff($userId, 'details');
    }
    
    /**
     * Validate staff removal (wrapper for canAddStaff)
     */
    public function validateStaffRemoval($userId = null)
    {
        $result = $this->canAddStaff($userId, 'removal');
        
        return [
            'can_remove' => $result['current_count'] > 0,
            'message' => $result['current_count'] > 0 ? 'Staff member can be removed.' : 'No staff members to remove.',
            'current_count' => $result['current_count'],
            'limit' => $result['limit'],
            'remaining_after_removal' => $result['remaining'] + 1,
            'slots_will_be_freed' => $result['current_count'] > 0 ? 1 : 0
        ];
    }
    
    /**
     * Handle staff removal (wrapper for canAddStaff)
     */
    public function handleStaffRemoval($userId = null)
    {
        $result = $this->canAddStaff($userId, 'removal');
        
        
        return $result;
    }


    
    public function canAddProposal($userId = null, $context = 'add')
    {
        $userId = $userId ?? Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type' ,'membership')
            ->where('subscription_status', 'active')
            ->first();
            
        if (!$subscription) {
            return [
                'allowed' => false,
                'message' => 'No active subscription found. Please subscribe to submit proposals.',
                'has_subscription' => false,
                'current_count' => 0,
                'limit' => 0,
                'remaining' => 0,
                'usage_percentage' => 0,
                'plan_title' => 'No Plan'
            ];
        }
        
        // Check if plan allows proposal management
        $configure = MembershipPlanFeatureValue::where('membership_plan_id',$subscription->membership_plans_plan_id)
            ->where('feature_key','case_submit_proposal')
            ->first();
       
        if(!empty($configure)){
            if ($configure->value == 0) {
                return [
                    'allowed' => false,
                    'message' => 'Proposal management is not available in your current subscription plan.',
                    'has_subscription' => true,
                    'current_count' => 0,
                    'limit' => 0,
                    'remaining' => 0,
                    'usage_percentage' => 0,
                    'plan_title' => 'Plan Not Supported'
                ];
            }
        }
        
        // Get current proposal usage from UserPlanFeatureHistory for accurate tracking
        $usageData = $this->getCurrentProposalUsageFromHistory($userId);
        $currentProposalCount = $usageData['current_count'];
        $limit = $usageData['limit'];
        $remaining = $usageData['remaining'];
        $usagePercentage = $usageData['usage_percentage'];
        $planTitle = $usageData['plan_title'];
        
        // Determine if user can add proposals
        $canAdd = $remaining > 0 || $remaining === -1;
        
        // Generate appropriate message based on context
        $message = $this->generateProposalMessage($context, $canAdd, $currentProposalCount, $limit, $remaining, $planTitle);
        
        
        return [
            'allowed' => $canAdd,
            'message' => $message,
            'has_subscription' => true,
            'current_count' => $currentProposalCount,
            'limit' => $limit,
            'remaining' => $remaining,
            'usage_percentage' => $usagePercentage,
            'plan_title' => $planTitle,
            'plan_id' => $subscription->membership_plans_plan_id
        ];
    }

    /**
     * Get active proposal count for a user from UserPlanFeatureHistory
     */
    private function getActiveProposalCount($userId)
    {
        // Get count from UserPlanFeatureHistory table for case_submit_proposal feature
        $historyCount = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
            ->where('module_name', 'proposals')
            ->where('feature_key', 'case_submit_proposal')
            ->whereJsonContains('metadata->action_type', 1) // Only count add actions
            ->count();

        // Fallback to CaseComment table for backward compatibility
        // This ensures we don't lose count if some proposals were submitted before the feature tracking was implemented
        $commentCount = CaseComment::where('added_by', $userId)
            ->where('status', 'sent')
            ->distinct('case_id')
            ->count('case_id');

        // Return the higher count to ensure we don't underestimate usage
        return max($historyCount, $commentCount);
    }
    
    /**
     * Get proposal limit for plan
     */
    private function getProposalLimit($membershipPlanId)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $membershipPlanId)
            ->where('feature_key', 'case_submit_proposal')
            ->first();
            
        if (!$featureValue) {
            return 0; // No proposals allowed
        }
        
        // Check if unlimited (-1) or specific limit
        return $featureValue->value ?? 1; // Default to 1 if no limit specified
    }
    
    /**
     * Generate appropriate message based on context for proposals
     */
    private function generateProposalMessage($context, $canAdd, $currentCount, $limit, $remaining, $planTitle)
    {
        switch ($context) {
            case 'add':
                if ($canAdd) {
                    if ($remaining === -1) {
                        return "Proposal management is available. You can submit unlimited proposals.";
                    } else {
                        return "You’ve added {$currentCount} proposal. {$remaining} remaining under your current plan.";
                    }
                } else {
                    if ($limit === -1) {
                        return "You have reached the limit of unlimited proposals.";
                    } else {
                        return "You have reached the limit of {$limit} proposals. Please upgrade your subscription to submit more proposals.";
                    }
                }
                
            case 'upgrade':
                if ($canAdd) {
                    return "Plan upgraded successfully. You can now submit {$remaining} more proposal(s).";
                } else {
                    return "Plan upgraded but limit still reached.";
                }
                
            case 'details':
                if ($canAdd) {
                    return "You can submit {$remaining} more proposal(s).";
                } else {
                    return "You have reached your proposal limit of {$limit}.";
                }
                
            default:
                if ($canAdd) {
                    return "Proposal management is available";
                } else {
                    return "Proposal management is limited";
                }
        }
    }
    
    /**
     * Get detailed proposal limit information (wrapper for canAddProposal)
     */
    public function getProposalLimitDetails($userId = null)
    {
        return $this->canAddProposal($userId, 'details');
    }

    /**
     * Check if user can add reviews - New function for reviews with unlimited text support
     */
    public function canAddReviewNew($userId = null, $context = 'add')
    {
        $userId = $userId ?? Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type' ,'membership')
            ->where('subscription_status', 'active')
            ->first();
            
        if (!$subscription) {
            return [
                'allowed' => false,
                'message' => 'No active subscription found. Please subscribe to submit reviews.',
                'has_subscription' => false,
                'current_count' => 0,
                'limit' => 0,
                'remaining' => 0,
                'usage_percentage' => 0,
                'plan_title' => 'No Plan'
            ];
        }
        
        // Check if plan allows review management
        $configure = MembershipPlanFeatureValue::where('membership_plan_id',$subscription->membership_plans_plan_id)
            ->where('feature_key','reviews')
            ->first();
       
        if(!empty($configure)){
            if ($configure->value == 0) {
                return [
                    'allowed' => false,
                    'message' => 'Review management is not available in your current subscription plan.',
                    'has_subscription' => true,
                    'current_count' => 0,
                    'limit' => 0,
                    'remaining' => 0,
                    'usage_percentage' => 0,
                    'plan_title' => 'Plan Not Supported'
                ];
            }
        }
        
        // Get current review count
        $currentReviewCount = $this->getActiveReviewCountNew($userId);
        
        // Get limit based on plan feature
        $limit = $this->getReviewLimitNew($subscription->membership_plans_plan_id);
        
        // Get plan details
        $plan = $subscription->membershipPlan;
        $planTitle = $plan->plan_title ?? 'Unknown Plan';
        
        // Handle limit logic - support both unlimited text and numeric values
        if ($limit === 'unlimited' || $limit == -1) {
            $limitText = 'unlimited';
            $remaining = 'unlimited';
            $canAdd = true;
            $usagePercentage = 0;
        } else {
            $limitText = (int)$limit;
            $remaining = (int)$limit - $currentReviewCount;
            $canAdd = $remaining > 0;
            $usagePercentage = $limit > 0 ? round(($currentReviewCount / $limit) * 100, 2) : 0;
        }
        
        // Generate appropriate message based on context
        $message = $this->generateReviewMessageNew($context, $canAdd, $currentReviewCount, $limitText, $remaining, $planTitle);
        
        
        return [
            'allowed' => $canAdd,
            'message' => $message,
            'has_subscription' => true,
            'current_count' => $currentReviewCount,
            'limit' => $limitText,
            'remaining' => $remaining,
            'usage_percentage' => $usagePercentage,
            'plan_title' => $planTitle,
            'plan_id' => $subscription->membership_plans_plan_id
        ];
    }

    /**
     * Get active review count for a user
     */
    private function getActiveReviewCountNew($userId)
    {
        // Count from UserPlanFeatureHistory table for reviews feature
        $historyCount = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
            ->where('feature_key', 'reviews')
            ->count();
            
        // If no history records exist, fallback to direct database count
        if ($historyCount == 0) {
            return \App\Models\Reviews::where('professional_id', $userId)
                ->count();
        }
        
        return $historyCount;
    }
    
    /**
     * Get review limit for plan - supports both unlimited text and numeric values
     */
    private function getReviewLimitNew($membershipPlanId)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $membershipPlanId)
            ->where('feature_key', 'reviews')
            ->first();
            
        if (!$featureValue) {
            return 0; // No reviews allowed
        }
        
        // Check if unlimited text or numeric value
        if ($featureValue->value === 'unlimited' || $featureValue->value == -1) {
            return 'unlimited';
        }
        
        // Return numeric limit
        return (int)($featureValue->value ?? 1);
    }
    
    /**
     * Generate appropriate message based on context for reviews
     */
    private function generateReviewMessageNew($context, $canAdd, $currentCount, $limit, $remaining, $planTitle)
    {
        switch ($context) {
            case 'add':
                if ($canAdd) {
                    if ($remaining === 'unlimited') {
                        return "You’ve added {$currentCount} review. {$remaining} under your current plan.";
                    } else {
                        return "You can submit {$remaining} review(s).";
                    }
                } else {
                    if ($limit === 'unlimited') {
                        return "You have reached the limit of unlimited reviews.";
                    } else {
                        return "You have reached the limit of {$limit} reviews. Please upgrade your subscription to submit more reviews.";
                    }
                }
                
            case 'upgrade':
                if ($canAdd) {
                    if ($remaining === 'unlimited') {
                        return "Plan upgraded successfully. You can now submit unlimited reviews.";
                    } else {
                        return "Plan upgraded successfully. You can now submit {$remaining} more review(s).";
                    }
                } else {
                    return "Plan upgraded but limit still reached.";
                }
                
            case 'details':
                if ($canAdd) {
                    if ($remaining === 'unlimited') {
                        return "You can submit unlimited reviews.";
                    } else {
                        return "You can submit {$remaining} more review(s).";
                    }
                } else {
                    if ($limit === 'unlimited') {
                        return "You have reached the limit of unlimited reviews.";
                    } else {
                        return "You have reached your review limit of {$limit}.";
                    }
                }
                
            default:
                if ($canAdd) {
                    return "Review management is available";
                } else {
                    return "Review management is limited";
                }
        }
    }

   

    /**
     * Save plan feature usage to history table
     * This function is used for all modules to track user plan feature usage
     * 
     * @param string $moduleName The module name (e.g., 'staff', 'proposals', 'reviews', 'appointments')
     * @param int $userId The user ID
     * @param int $actionType 1 for add, 0 for remove
     * @param int $count Number of items added/removed (default 1)
     * @param array $additionalData Additional data to store in metadata
     * @return array Response with success status and details
     */
    public function savePlanFeature($moduleName, $userId, $actionType = 1, $count = 1, $additionalData = [])
    {
        try {
            // Get user's active subscription
            $subscription = UserSubscriptionHistory::where('user_id', $userId)
                ->where('subscription_type', 'membership')
                ->where('subscription_status', 'active')
                ->first();

            if (!$subscription) {
                return [
                    'success' => false,
                    'message' => 'No active subscription found for user.',
                    'data' => null
                ];
            }

            // Get plan details
            $plan = $subscription->membershipPlan;
            $planTitle = $plan->plan_title ?? 'Unknown Plan';
            $planId = $subscription->membership_plans_plan_id;

            // Determine feature key based on module name
            $featureKey = $this->getFeatureKeyForModule($moduleName);
            
            if (!$featureKey) {
                return [
                    'success' => false,
                    'message' => 'Invalid module name provided.',
                    'data' => null
                ];
            }

            // Get plan limit for this feature
            $planLimit = $this->getPlanLimitForFeature($planId, $featureKey);

            // Always use 1 as current usage for new entries
            $currentUsage = 1;

            // Always use 1 as the new usage value (not calculated)
            $newUsage = 1;
            

            // Create history record using the model's helper method
            $metadata = array_merge($additionalData, [
                'action_type' => $actionType,
                'count' => $count,
                'membership_plan_id' => $planId,
                'plan_title' => $planTitle,
                'subscription_id' => $subscription->id,
                'subscription_type' => $subscription->subscription_type,
                'subscription_status' => $subscription->subscription_status,
                'previous_usage' => $currentUsage,
                'usage_change' => $actionType == 1 ? $count : -$count
            ]);

            // Create history record using the model's helper method
            $currentUserId = Auth::id() ?? $userId;
            
            
            $historyRecord = \App\Models\UserPlanFeatureHistory::createFeatureHistory(
                $userId,
                $moduleName,
                $featureKey,
                $planLimit,
                $newUsage,
                $this->generateDescription($moduleName, $actionType, $count),
                $metadata,
                $currentUserId
            );

            return [
                'success' => true,
                'message' => 'Plan feature usage recorded successfully.',
                'data' => [
                    'history_id' => $historyRecord->id,
                    'unique_id' => $historyRecord->unique_id,
                    'current_usage' => $newUsage,
                    'plan_limit' => $planLimit,
                    'plan_title' => $planTitle,
                    'remaining_limit' => $planLimit === 'unlimited' ? 'unlimited' : max(0, $planLimit - $newUsage)
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error saving plan feature usage', [
                'user_id' => $userId,
                'module_name' => $moduleName,
                'action_type' => $actionType,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error recording plan feature usage: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Save case_submit_proposal feature usage to UserPlanFeatureHistory
     * 
     * @param int $userId User ID who submitted the proposal
     * @param int $caseId Case ID for the proposal
     * @param array $additionalData Additional data to store in metadata
     * @return array Result of the operation
     */
    public function saveCaseSubmitProposal($userId, $caseId, $additionalData = [])
    {
        try {
            // Get user's active subscription
            $subscription = UserSubscriptionHistory::where('user_id', $userId)
                ->where('subscription_type', 'membership')
                ->where('subscription_status', 'active')
                ->first();

            if (!$subscription) {
                return [
                    'success' => false,
                    'message' => 'No active subscription found for user.',
                    'data' => null
                ];
            }

            // Get plan details
            $plan = $subscription->membershipPlan;
            $planTitle = $plan->plan_title ?? 'Unknown Plan';
            $planId = $subscription->membership_plans_plan_id;

            // Get plan limit for case_submit_proposal feature
            $planLimit = $this->getPlanLimitForFeature($planId, 'case_submit_proposal');

            // Always use 1 as current usage for new entries
            $currentUsage = 1;
            
            // Create metadata with additional case information
            $metadata = array_merge($additionalData, [
                'action_type' => 1, // Add action
                'count' => 1,
                'membership_plan_id' => $planId,
                'plan_title' => $planTitle,
                'subscription_id' => $subscription->id,
                'subscription_type' => $subscription->subscription_type,
                'subscription_status' => $subscription->subscription_status,
                'previous_usage' => $currentUsage,
                'usage_change' => 1,
                'case_id' => $caseId
            ]);

            // Get current user ID (who is performing the action)
            $currentUserId = Auth::id() ?? $userId;
            
            
            // Create history record using the model's helper method
            $historyRecord = \App\Models\UserPlanFeatureHistory::createFeatureHistory(
                $userId,
                'proposals',
                'case_submit_proposal',
                $planLimit,
                $currentUsage,
                'Case proposal submitted',
                $metadata,
                $currentUserId
            );

            return [
                'success' => true,
                'message' => 'Case submit proposal usage recorded successfully.',
                'data' => [
                    'history_id' => $historyRecord->id,
                    'unique_id' => $historyRecord->unique_id,
                    'current_usage' => $currentUsage,
                    'plan_limit' => $planLimit,
                    'plan_title' => $planTitle,
                    'remaining_limit' => $planLimit === 'unlimited' ? 'unlimited' : max(0, $planLimit - $currentUsage),
                    'case_id' => $caseId
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error saving case submit proposal usage', [
                'user_id' => $userId,
                'case_id' => $caseId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error recording case submit proposal usage: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get feature key based on module name
     */
    private function getFeatureKeyForModule($moduleName)
    {
        $featureMap = [
            'staff' => 'staff_add',
            'proposals' => 'case_submit_proposal',
            'reviews' => 'reviews',
            'appointments' => 'appointment_booking',
            'articles' => 'articles',
            'feeds' => 'feeds',
            'threads' => 'threads',
            'connections' => 'connections'
        ];

        return $featureMap[$moduleName] ?? null;
    }

    /**
     * Get plan limit for a specific feature
     */
    private function getPlanLimitForFeature($planId, $featureKey)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $planId)
            ->where('feature_key', $featureKey)
            ->first();

        if (!$featureValue) {
            return 0;
        }

        // Handle unlimited values - return 'unlimited' as text instead of -1
        if ($featureValue->value === 'unlimited' || $featureValue->value == -1) {
            return 'unlimited';
        }

        return (int)($featureValue->value ?? 1);
    }

    /**
     * Generate description for the history record
     */
    private function generateDescription($moduleName, $actionType, $count)
    {
        $action = $actionType == 1 ? 'added' : 'removed';
        $item = $count == 1 ? 'item' : 'items';
        
        return ucfirst($moduleName) . " {$action}: {$count} {$item}";
    }

    /**
     * Get current usage and remaining limit for a user and feature from history table
     */
    public function getFeatureUsageFromHistory($userId, $featureKey)
    {
        $currentUsage = \App\Models\UserPlanFeatureHistory::getCurrentUsage($userId, $featureKey);
        $remainingLimit = \App\Models\UserPlanFeatureHistory::getRemainingLimit($userId, $featureKey);

        return [
            'current_usage' => $currentUsage,
            'remaining_limit' => $remainingLimit
        ];
    }

    /**
     * Delete a specific plan feature history entry based on staff_user_id in metadata
     */
    public function deletePlanFeatureHistory($moduleName, $userId, $staffUserId)
    {
        try {
            // Find the history record that matches the staff_user_id in metadata
            $historyRecord = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
                ->where('module_name', $moduleName)
                ->whereJsonContains('metadata->staff_user_id', $staffUserId)
                ->first();

            if ($historyRecord) {

                // Delete the record
                $historyRecord->delete();

                return [
                    'success' => true,
                    'message' => 'History record deleted successfully.',
                    'deleted_record_id' => $historyRecord->id
                ];
            } else {

                return [
                    'success' => false,
                    'message' => 'No history record found for this staff member.',
                    'deleted_record_id' => null
                ];
            }

        } catch (\Exception $e) {
            Log::error('Error deleting plan feature history record', [
                'user_id' => $userId,
                'module_name' => $moduleName,
                'staff_user_id' => $staffUserId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error deleting history record: ' . $e->getMessage(),
                'deleted_record_id' => null
            ];
        }
    }


      public function deleteConnectFeatureHistory($moduleName, $userId, $staffUserId)
    {
        try {
            // Find the history record that matches the staff_user_id in metadata
            $historyRecord = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
                ->where('module_name', $moduleName)
                ->whereJsonContains('metadata->user_id', $staffUserId)
                ->first();

            if ($historyRecord) {

                // Delete the record
                $historyRecord->delete();

                return [
                    'success' => true,
                    'message' => 'History record deleted successfully.',
                    'deleted_record_id' => $historyRecord->id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No history record found for this.',
                    'deleted_record_id' => null
                ];
            }

        } catch (\Exception $e) {
            Log::error('Error deleting plan feature history record', [
                'user_id' => $userId,
                'module_name' => $moduleName,
                'staff_user_id' => $staffUserId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error deleting history record: ' . $e->getMessage(),
                'deleted_record_id' => null
            ];
        }
    }

    /**
     * Get current proposal usage from UserPlanFeatureHistory table
     */
    public function getCurrentProposalUsageFromHistory($userId = null)
    {
        $userId = $userId ?? Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type', 'membership')
            ->where('subscription_status', 'active')
            ->first();

        if (!$subscription) {
            return [
                'current_count' => 0,
                'limit' => 0,
                'remaining' => 0,
                'usage_percentage' => 0,
                'plan_title' => 'No Plan',
                'has_subscription' => false
            ];
        }

        // Get plan details
        $plan = $subscription->membershipPlan;
        $planTitle = $plan->plan_title ?? 'Unknown Plan';
        $planId = $subscription->membership_plans_plan_id;

        // Get limit from plan
        $limit = $this->getProposalLimit($planId);

        // Get current usage from UserPlanFeatureHistory
        $currentCount = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
            ->where('module_name', 'proposals')
            ->where('feature_key', 'case_submit_proposal')
            ->whereJsonContains('metadata->action_type', 1) // Only count add actions
            ->count();

        // Calculate remaining slots
        $remaining = $limit === -1 ? -1 : $limit - $currentCount;

        // Calculate usage percentage
        $usagePercentage = $limit > 0 ? round(($currentCount / $limit) * 100, 2) : 0;

        return [
            'current_count' => $currentCount,
            'limit' => $limit,
            'remaining' => $remaining,
            'usage_percentage' => $usagePercentage,
            'plan_title' => $planTitle,
            'plan_id' => $planId,
            'has_subscription' => true
        ];
    }

    /**
     * Check if user can add articles based on their subscription plan
     */
    public function canAddArticle($userId = null, $context = 'add')
    {
        $userId = $userId ?? Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type' ,'membership')
            ->where('subscription_status', 'active')
            ->first();
            
        if (!$subscription) {
            return [
                'allowed' => false,
                'message' => 'No active subscription found. Please subscribe to create articles.',
                'has_subscription' => false,
                'current_count' => 0,
                'limit' => 0,
                'remaining' => 0,
                'usage_percentage' => 0,
                'plan_title' => 'No Plan'
            ];
        }
        
        // Check if plan allows article management
        $configure = MembershipPlanFeatureValue::where('membership_plan_id',$subscription->membership_plans_plan_id)
            ->where('feature_key','articles')
            ->first();
       
        if(!empty($configure)){
            if ($configure->value == 0) {
                return [
                    'allowed' => false,
                    'message' => 'Article management is not available in your current subscription plan.',
                    'has_subscription' => true,
                    'current_count' => 0,
                    'limit' => 0,
                    'remaining' => 0,
                    'usage_percentage' => 0,
                    'plan_title' => 'Plan Not Supported'
                ];
            }
        }
        
        // Get current article count
        $currentArticleCount = $this->getActiveArticleCount($userId);
        
        // Get limit based on plan feature
        $limit = $this->getArticleLimit($subscription->membership_plans_plan_id);
        
        // Get plan details
        $plan = $subscription->membershipPlan;
        $planTitle = $plan->plan_title ?? 'Unknown Plan';
        
        // Handle limit logic - support both unlimited text and numeric values
        if ($limit === 'unlimited' || $limit == -1) {
            $limitText = 'unlimited';
            $remaining = 'unlimited';
            $canAdd = true;
            $usagePercentage = 0;
        } else {
            $limitText = (int)$limit;
            $remaining = (int)$limit - $currentArticleCount;
            $canAdd = $remaining > 0;
            $usagePercentage = $limit > 0 ? round(($currentArticleCount / $limit) * 100, 2) : 0;
        }
        
        // Generate appropriate message based on context
        $message = $this->generateArticleMessage($context, $canAdd, $currentArticleCount, $limitText, $remaining, $planTitle);
        
        
        return [
            'allowed' => $canAdd,
            'message' => $message,
            'has_subscription' => true,
            'current_count' => $currentArticleCount,
            'limit' => $limitText,
            'remaining' => $remaining,
            'usage_percentage' => $usagePercentage,
            'plan_title' => $planTitle,
            'plan_id' => $subscription->membership_plans_plan_id
        ];
    }

    /**
     * Get active article count for a user
     */
    private function getActiveArticleCount($userId)
    {
        // Count total records in UserPlanFeatureHistory for this user and feature
        $historyCount = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
            ->where('feature_key', 'articles')
            ->whereJsonContains('metadata->action_type', 1) // Only count add actions
            ->count();
        
        return $historyCount;
    }

    /**
     * Get article limit for a membership plan
     */
    private function getArticleLimit($membershipPlanId)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $membershipPlanId)
            ->where('feature_key', 'articles')
            ->first();

        if (!$featureValue) {
            return 0;
        }

        // Handle unlimited values
        if ($featureValue->value === 'unlimited' || $featureValue->value == -1) {
            return 'unlimited';
        }

        return (int)($featureValue->value ?? 1);
    }

    /**
     * Generate message for article limit
     */
    private function generateArticleMessage($context, $canAdd, $currentCount, $limit, $remaining, $planTitle)
    {
        switch ($context) {
            case 'add':
                if ($canAdd) {
                    if ($limit === 'unlimited') {
                        return "You’ve added {$currentCount} articles. {$remaining} under your current plan.";
                    } else {
                        return "You can create {$remaining} more articles.";
                    }
                } else {
                    if ($limit === 'unlimited') {
                        return "Article management is limited.";
                    } else {
                        return "Article limit reached. You have created {$currentCount} articles out of {$limit} allowed.";
                    }
                }
                
            default:
                if ($canAdd) {
                    return "Article management is available";
                } else {
                    return "Article management is limited";
                }
        }
    }

    /**
     * Check if user can add articles based on their subscription plan
     */
    public function canAddFeed($userId = null, $context = 'add')
    {
        $userId = $userId ?? Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type' ,'membership')
            ->where('subscription_status', 'active')
            ->first();
            
        if (!$subscription) {
            return [
                'allowed' => false,
                'message' => 'No active subscription found. Please subscribe to create feed.',
                'has_subscription' => false,
                'current_count' => 0,
                'limit' => 0,
                'remaining' => 0,
                'usage_percentage' => 0,
                'plan_title' => 'No Plan'
            ];
        }
        
        // Check if plan allows article management
        $configure = MembershipPlanFeatureValue::where('membership_plan_id',$subscription->membership_plans_plan_id)
            ->where('feature_key','feeds')
            ->first();
       
        if(!empty($configure)){
            if ($configure->value == 0) {
                return [
                    'allowed' => false,
                    'message' => 'Feed management is not available in your current subscription plan.',
                    'has_subscription' => true,
                    'current_count' => 0,
                    'limit' => 0,
                    'remaining' => 0,
                    'usage_percentage' => 0,
                    'plan_title' => 'Plan Not Supported'
                ];
            }
        }
        
        // Get current article count
        $currentArticleCount = $this->getActiveFeedCount($userId);
        
        // Get limit based on plan feature
        $limit = $this->getFeedLimit($subscription->membership_plans_plan_id);
        
        // Get plan details
        $plan = $subscription->membershipPlan;
        $planTitle = $plan->plan_title ?? 'Unknown Plan';
        
        // Handle limit logic - support both unlimited text and numeric values
        if ($limit === 'unlimited' || $limit == -1) {
            $limitText = 'unlimited';
            $remaining = 'unlimited';
            $canAdd = true;
            $usagePercentage = 0;
        } else {
            $limitText = (int)$limit;
            $remaining = (int)$limit - $currentArticleCount;
            $canAdd = $remaining > 0;
            $usagePercentage = $limit > 0 ? round(($currentArticleCount / $limit) * 100, 2) : 0;
        }
        
        // Generate appropriate message based on context
        $message = $this->generateFeedMessage($context, $canAdd, $currentArticleCount, $limitText, $remaining, $planTitle);
        
        return [
            'allowed' => $canAdd,
            'message' => $message,
            'has_subscription' => true,
            'current_count' => $currentArticleCount,
            'limit' => $limitText,
            'remaining' => $remaining,
            'usage_percentage' => $usagePercentage,
            'plan_title' => $planTitle,
            'plan_id' => $subscription->membership_plans_plan_id
        ];
    }

    /**
     * Get active article count for a user
     */
    private function getActiveFeedCount($userId)
    {
        // Count total records in UserPlanFeatureHistory for this user and feature
        $historyCount = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
            ->where('feature_key', 'feeds')
            ->whereJsonContains('metadata->action_type', 1) // Only count add actions
            ->count();
        
        return $historyCount;
    }

    /**
     * Get article limit for a membership plan
     */
    private function getFeedLimit($membershipPlanId)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $membershipPlanId)
            ->where('feature_key', 'feeds')
            ->first();

        if (!$featureValue) {
            return 0;
        }

        // Handle unlimited values
        if ($featureValue->value === 'unlimited' || $featureValue->value == -1) {
            return 'unlimited';
        }

        return (int)($featureValue->value ?? 1);
    }

    /**
     * Generate message for article limit
     */
    private function generateFeedMessage($context, $canAdd, $currentCount, $limit, $remaining, $planTitle)
    {
        switch ($context) {
            case 'add':
                if ($canAdd) {
                    if ($limit === 'unlimited') {
                        return "You’ve added {$currentCount} feed. {$remaining} under your current plan.";
                    } else {
                        return "You can create {$remaining} more feed.";
                    }
                } else {
                    if ($limit === 'unlimited') {
                        return "Feed management is limited.";
                    } else {
                        return "Feed limit reached. You have created {$currentCount} feed out of {$limit} allowed.";
                    }
                }
                
            default:
                if ($canAdd) {
                    return "Feed management is available";
                } else {
                    return "Feed management is limited";
                }
        }
    }

    // thread
    /**
     * Check if user can add articles based on their subscription plan
     */
    public function canAddThread($userId = null, $context = 'add')
    {
        $userId = $userId ?? Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type' ,'membership')
            ->where('subscription_status', 'active')
            ->first();
            
        if (!$subscription) {
            return [
                'allowed' => false,
                'message' => 'No active subscription found. Please subscribe to create threads.',
                'has_subscription' => false,
                'current_count' => 0,
                'limit' => 0,
                'remaining' => 0,
                'usage_percentage' => 0,
                'plan_title' => 'No Plan'
            ];
        }
        
        // Check if plan allows article management
        $configure = MembershipPlanFeatureValue::where('membership_plan_id',$subscription->membership_plans_plan_id)
            ->where('feature_key','threads')
            ->first();
       
        if(!empty($configure)){
            if ($configure->value == 0) {
                return [
                    'allowed' => false,
                    'message' => 'Thread management is not available in your current subscription plan.',
                    'has_subscription' => true,
                    'current_count' => 0,
                    'limit' => 0,
                    'remaining' => 0,
                    'usage_percentage' => 0,
                    'plan_title' => 'Plan Not Supported'
                ];
            }
        }
        
        // Get current article count
        $currentArticleCount = $this->getActiveThreadCount($userId);
        
        // Get limit based on plan feature
        $limit = $this->getThreadLimit($subscription->membership_plans_plan_id);
        
        // Get plan details
        $plan = $subscription->membershipPlan;
        $planTitle = $plan->plan_title ?? 'Unknown Plan';
        
        // Handle limit logic - support both unlimited text and numeric values
        if ($limit === 'unlimited' || $limit == -1) {
            $limitText = 'unlimited';
            $remaining = 'unlimited';
            $canAdd = true;
            $usagePercentage = 0;
        } else {
            $limitText = (int)$limit;
            $remaining = (int)$limit - $currentArticleCount;
            $canAdd = $remaining > 0;
            $usagePercentage = $limit > 0 ? round(($currentArticleCount / $limit) * 100, 2) : 0;
        }
        
        // Generate appropriate message based on context
        $message = $this->generateThreadMessage($context, $canAdd, $currentArticleCount, $limitText, $remaining, $planTitle);
        
        
        return [
            'allowed' => $canAdd,
            'message' => $message,
            'has_subscription' => true,
            'current_count' => $currentArticleCount,
            'limit' => $limitText,
            'remaining' => $remaining,
            'usage_percentage' => $usagePercentage,
            'plan_title' => $planTitle,
            'plan_id' => $subscription->membership_plans_plan_id
        ];
    }

    /**
     * Get active article count for a user
     */
    private function getActiveThreadCount($userId)
    {
        // Count total records in UserPlanFeatureHistory for this user and feature
        $historyCount = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
            ->where('feature_key', 'threads')
            ->whereJsonContains('metadata->action_type', 1) // Only count add actions
            ->count();
        
        return $historyCount;
    }

    /**
     * Get article limit for a membership plan
     */
    private function getThreadLimit($membershipPlanId)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $membershipPlanId)
            ->where('feature_key', 'threads')
            ->first();

        if (!$featureValue) {
            return 0;
        }

        // Handle unlimited values
        if ($featureValue->value === 'unlimited' || $featureValue->value == -1) {
            return 'unlimited';
        }

        return (int)($featureValue->value ?? 1);
    }

    /**
     * Generate message for article limit
     */
    private function generateThreadMessage($context, $canAdd, $currentCount, $limit, $remaining, $planTitle)
    {
        switch ($context) {
            case 'add':
                if ($canAdd) {
                    if ($limit === 'unlimited') {
                      return "You’ve added {$currentCount} threads. {$remaining} under your current plan.";
                    } else {
                        return " You can create {$remaining} more threads.";
                    }
                } else {
                    if ($limit === 'unlimited') {
                        return "Thread management is limited.";
                    } else {
                        return "Thread limit reached. You have created {$currentCount} articles out of {$limit} allowed.";
                    }
                }
                
            default:
                if ($canAdd) {
                    return "Thread management is available";
                } else {
                    return "Thread management is limited";
                }
        }
    }


    /**
     * Check if user can add articles based on their subscription plan
     */
    public function canAddTransactions($userId = null, $context = 'add')
    {
        $userId = $userId ?? Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type' ,'membership')
            ->where('subscription_status', 'active')
            ->first();
            
        if (!$subscription) {
            return [
                'allowed' => false,
                'message' => 'No active subscription found',
                'has_subscription' => false,
                'current_count' => 0,
                'limit' => 0,
                'remaining' => 0,
                'usage_percentage' => 0,
                'plan_title' => 'No Plan'
            ];
        }
        
        // Check if plan allows article management
        $configure = MembershipPlanFeatureValue::where('membership_plan_id',$subscription->membership_plans_plan_id)
            ->where('feature_key','transactions')
            ->first();
       
        if(!empty($configure)){
            if ($configure->value == 0) {
                return [
                    'allowed' => false,
                    'has_subscription' => true,
                ];
            }
        }
        
        // Get limit based on plan feature
        $limit = $this->getTransactionLimit($subscription->membership_plans_plan_id);
        
        // Get plan details
        $plan = $subscription->membershipPlan;
        $planTitle = $plan->plan_title ?? 'Unknown Plan';
        
        if($limit == 1){
            return [
                'allowed' => true,
                'has_subscription' => true,
            ];
        }else{
            return [
                'allowed' => false,
                'has_subscription' => true,
            ];
        }
        
    }

   

    /**
     * Get article limit for a membership plan
     */
    private function getTransactionLimit($membershipPlanId)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $membershipPlanId)
            ->where('feature_key', 'transactions')
            ->first();
      
        if (!$featureValue) {
            return 0;
        }

        if($featureValue->value == 0){
            return 0;
        }else{
            return 1;
        }

    }


    // booking
    /**
     * Check if user can add articles based on their subscription plan
     */
    public function canAddAppointmentBooking($userId = null, $context = 'add')
    {
        $userId = $userId ?? Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type' ,'membership')
            ->where('subscription_status', 'active')
            ->first();
            
        if (!$subscription) {
            return [
                'allowed' => false,
                'message' => 'No active subscription found. Please subscribe to create bookings.',
                'has_subscription' => false,
                'current_count' => 0,
                'limit' => 0,
                'remaining' => 0,
                'usage_percentage' => 0,
                'plan_title' => 'No Plan'
            ];
        }
        
        // Check if plan allows article management
        $configure = MembershipPlanFeatureValue::where('membership_plan_id',$subscription->membership_plans_plan_id)
            ->where('feature_key','appointment_booking')
            ->first();
       
        if(!empty($configure)){
            if ($configure->value == 0) {
                return [
                    'allowed' => false,
                    'message' => 'Appointment Booking is not available in your current subscription plan.',
                    'has_subscription' => true,
                    'current_count' => 0,
                    'limit' => 0,
                    'remaining' => 0,
                    'usage_percentage' => 0,
                    'plan_title' => 'Plan Not Supported'
                ];
            }
        }
        
        // Get current article count
        $currentArticleCount = $this->getActiveAppointmentBookingCount($userId);
        
        // Get limit based on plan feature
        $limit = $this->getAppointmentBookingLimit($subscription->membership_plans_plan_id);
        
        // Get plan details
        $plan = $subscription->membershipPlan;
        $planTitle = $plan->plan_title ?? 'Unknown Plan';
        
        // Handle limit logic - support both unlimited text and numeric values
        if ($limit === 'unlimited' || $limit == -1) {
            $limitText = 'unlimited';
            $remaining = 'unlimited';
            $canAdd = true;
            $usagePercentage = 0;
        } else {
            $limitText = (int)$limit;
            $remaining = (int)$limit - $currentArticleCount;
            $canAdd = $remaining > 0;
            $usagePercentage = $limit > 0 ? round(($currentArticleCount / $limit) * 100, 2) : 0;
        }
        
        // Generate appropriate message based on context
        $message = $this->generateAppointmentBookingMessage($context, $canAdd, $currentArticleCount, $limitText, $remaining, $planTitle);
        
        
        return [
            'allowed' => $canAdd,
            'message' => $message,
            'has_subscription' => true,
            'current_count' => $currentArticleCount,
            'limit' => $limitText,
            'remaining' => $remaining,
            'usage_percentage' => $usagePercentage,
            'plan_title' => $planTitle,
            'plan_id' => $subscription->membership_plans_plan_id
        ];
    }

    /**
     * Get active article count for a user
     */
    private function getActiveAppointmentBookingCount($userId)
    {
        // Count total records in UserPlanFeatureHistory for this user and feature
        $historyCount = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
            ->where('feature_key', 'appointment_booking')
            ->whereJsonContains('metadata->action_type', 1) // Only count add actions
            ->count();
        
        return $historyCount;
    }

    /**
     * Get article limit for a membership plan
     */
    private function getAppointmentBookingLimit($membershipPlanId)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $membershipPlanId)
            ->where('feature_key', 'appointment_booking')
            ->first();

        if (!$featureValue) {
            return 0;
        }

        // Handle unlimited values
        if ($featureValue->value === 'unlimited' || $featureValue->value == -1) {
            return 'unlimited';
        }

        return (int)($featureValue->value ?? 1);
    }

    /**
     * Generate message for article limit
     */
    private function generateAppointmentBookingMessage($context, $canAdd, $currentCount, $limit, $remaining, $planTitle)
    {
        switch ($context) {
            case 'add':
                if ($canAdd) {
                    if ($limit === 'unlimited') {
                        return "You’ve added {$currentCount} bookings. {$remaining} under your current plan.";
                    } else {
                        return "You can create {$remaining} more bookings.";
                    }
                } else {
                    if ($limit === 'unlimited') {
                        return "Booking management is limited.";
                    } else {
                        return "Booking limit reached. You have created {$currentCount} bookings out of {$limit} allowed.";
                    }
                }
                
            default:
                if ($canAdd) {
                    return "Bookings management is available";
                } else {
                    return "Bookings management is limited";
                }
        }
    }

    // connection
    
    // booking
    /**
     * Check if user can add articles based on their subscription plan
     */
    public function canAddConnection($userId = null, $context = 'add')
    {
        $userId = $userId ?? Auth::id();

        // Get user's active subscription
        $subscription = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type' ,'membership')
            ->where('subscription_status', 'active')
            ->first();
            
        if (!$subscription) {
            return [
                'allowed' => false,
                'message' => 'No active subscription found. Please subscribe to create connection.',
                'has_subscription' => false,
                'current_count' => 0,
                'limit' => 0,
                'remaining' => 0,
                'usage_percentage' => 0,
                'plan_title' => 'No Plan'
            ];
        }
        
        // Check if plan allows article management
        $configure = MembershipPlanFeatureValue::where('membership_plan_id',$subscription->membership_plans_plan_id)
            ->where('feature_key','connections')
            ->first();
       
        if(!empty($configure)){
            if ($configure->value == 0) {
                return [
                    'allowed' => false,
                    'message' => 'Appointment Booking is not available in your current subscription plan.',
                    'has_subscription' => true,
                    'current_count' => 0,
                    'limit' => 0,
                    'remaining' => 0,
                    'usage_percentage' => 0,
                    'plan_title' => 'Plan Not Supported'
                ];
            }
        }
        
        // Get current article count
        $currentArticleCount = $this->getActiveConnectionCount($userId);
        
        // Get limit based on plan feature
        $limit = $this->getConnectionLimit($subscription->membership_plans_plan_id);
        
        // Get plan details
        $plan = $subscription->membershipPlan;
        $planTitle = $plan->plan_title ?? 'Unknown Plan';
        
        // Handle limit logic - support both unlimited text and numeric values
        if ($limit === 'unlimited' || $limit == -1) {
            $limitText = 'unlimited';
            $remaining = 'unlimited';
            $canAdd = true;
            $usagePercentage = 0;
        } else {
            $limitText = (int)$limit;
            $remaining = (int)$limit - $currentArticleCount;
            $canAdd = $remaining > 0;
            $usagePercentage = $limit > 0 ? round(($currentArticleCount / $limit) * 100, 2) : 0;
        }
        
        // Generate appropriate message based on context
        $message = $this->generateConnectionMessage($context, $canAdd, $currentArticleCount, $limitText, $remaining, $planTitle);
        
        return [
            'allowed' => $canAdd,
            'message' => $message,
            'has_subscription' => true,
            'current_count' => $currentArticleCount,
            'limit' => $limitText,
            'remaining' => $remaining,
            'usage_percentage' => $usagePercentage,
            'plan_title' => $planTitle,
            'plan_id' => $subscription->membership_plans_plan_id
        ];
    }

    /**
     * Get active article count for a user
     */
    private function getActiveConnectionCount($userId)
    {
        // Count total records in UserPlanFeatureHistory for this user and feature
        $historyCount = \App\Models\UserPlanFeatureHistory::where('user_id', $userId)
            ->where('feature_key', 'connections')
            ->whereJsonContains('metadata->action_type', 1) // Only count add actions
            ->count();
        
        return $historyCount;
    }

    /**
     * Get article limit for a membership plan
     */
    private function getConnectionLimit($membershipPlanId)
    {
        $featureValue = MembershipPlanFeatureValue::where('membership_plan_id', $membershipPlanId)
            ->where('feature_key', 'connections')
            ->first();

        if (!$featureValue) {
            return 0;
        }

        // Handle unlimited values
        if ($featureValue->value === 'unlimited' || $featureValue->value == -1) {
            return 'unlimited';
        }

        return (int)($featureValue->value ?? 1);
    }

    /**
     * Generate message for article limit
     */
    private function generateConnectionMessage($context, $canAdd, $currentCount, $limit, $remaining, $planTitle)
    {
        switch ($context) {
            case 'add':
                if ($canAdd) {
                    if ($limit === 'unlimited') {
                        return "You’ve added {$currentCount} connections. {$remaining} under your current plan.";
                    } else {
                        return "You can create {$remaining} more connections.";
                    }
                } else {
                    if ($limit === 'unlimited') {
                        return "Connections management is limited.";
                    } else {
                        return "Connections limit reached. You have created {$currentCount} connections out of {$limit} allowed.";
                    }
                }
                
            default:
                if ($canAdd) {
                    return "Connections management is available";
                } else {
                    return "Connections management is limited";
                }
        }
    }

} 