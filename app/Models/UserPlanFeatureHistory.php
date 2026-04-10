<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class UserPlanFeatureHistory extends Model
{
    use HasFactory;

    protected $table = 'user_plan_feature_history';

    protected $fillable = [
        'user_id',
        'unique_id',
        'added_by',
        'module_name',
        'feature_key',
        'plan_limit',
        'current_usage',
        'description',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'current_usage' => 'integer'
    ];

    // Constants for common module names
    const MODULE_STAFF = 'staff';
    const MODULE_PROPOSALS = 'proposals';
    const MODULE_REVIEWS = 'reviews';
    const MODULE_APPOINTMENTS = 'appointments';
    const MODULE_ARTICLES = 'articles';

    // Constants for common feature keys
    const FEATURE_STAFF_ADD = 'staff_add';
    const FEATURE_CASE_SUBMIT_PROPOSAL = 'case_submit_proposal';
    const FEATURE_REVIEWS = 'reviews';
    const FEATURE_APPOINTMENT_BOOKING = 'appointment_booking';
    const FEATURE_ARTICLES = 'articles';

    /**
     * Get the user that owns the feature history record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who added this record
     */
    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by module
     */
    public function scopeForModule($query, $moduleName)
    {
        return $query->where('module_name', $moduleName);
    }

    /**
     * Scope to filter by feature key
     */
    public function scopeForFeature($query, $featureKey)
    {
        return $query->where('feature_key', $featureKey);
    }

    /**
     * Get the latest record for a user and feature
     */
    public function scopeLatestForUserAndFeature($query, $userId, $featureKey)
    {
        return $query->where('user_id', $userId)
                    ->where('feature_key', $featureKey)
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
    }

    /**
     * Get current usage count for a user and feature
     */
    public static function getCurrentUsage($userId, $featureKey)
    {
        // Count total records for this user and feature (add actions only)
        $count = self::where('user_id', $userId)
            ->where('feature_key', $featureKey)
            ->whereJsonContains('metadata->action_type', 1) // Only count add actions
            ->count();
        
        return $count;
    }

    /**
     * Get plan limit for a user and feature
     */
    public static function getPlanLimit($userId, $featureKey)
    {
        $latestRecord = self::latestForUserAndFeature($userId, $featureKey)->first();
        return $latestRecord ? $latestRecord->plan_limit : 0;
    }

    /**
     * Get remaining limit for a user and feature
     */
    public static function getRemainingLimit($userId, $featureKey)
    {
        $currentUsage = self::getCurrentUsage($userId, $featureKey);
        $planLimit = self::getPlanLimit($userId, $featureKey);
        
        if ($planLimit === -1) {
            return -1; // Unlimited
        }
        
        return max(0, $planLimit - $currentUsage);
    }

    /**
     * Get membership plan ID from metadata
     */
    public function getMembershipPlanId()
    {
        return $this->metadata['membership_plan_id'] ?? null;
    }

    /**
     * Get plan title from metadata
     */
    public function getPlanTitle()
    {
        return $this->metadata['plan_title'] ?? null;
    }

    /**
     * Get action type from metadata
     */
    public function getActionType()
    {
        return $this->metadata['action_type'] ?? 1;
    }

    /**
     * Get count from metadata
     */
    public function getCount()
    {
        return $this->metadata['count'] ?? 1;
    }

    /**
     * Check if action is an add action
     */
    public function isAddAction(): bool
    {
        return $this->getActionType() === 1;
    }

    /**
     * Check if action is a remove action
     */
    public function isRemoveAction(): bool
    {
        return $this->getActionType() === 0;
    }

    /**
     * Create a new feature history record
     * 
     * @param int $userId User ID
     * @param string $moduleName Module name
     * @param string $featureKey Feature key
     * @param int $planLimit Plan limit
     * @param int $currentUsage Current usage
     * @param string $description Description
     * @param array $metadata Additional metadata
     * @return UserPlanFeatureHistory
     */
    public static function createFeatureHistory(
        $userId,
        $moduleName,
        $featureKey,
        $planLimit,
        $currentUsage,
        $description,
        $metadata = [],
        $addedBy = null
    ) {
        try {
            $record = new self();
            $record->user_id = $userId;
            $record->unique_id = function_exists('randomNumber') ? randomNumber() : (string) time() . rand(1000, 9999);
            $record->added_by = $addedBy ?? Auth::id() ?? $userId;
            $record->module_name = $moduleName;
            $record->feature_key = $featureKey;
            $record->plan_limit = $planLimit;
            $record->current_usage = $currentUsage;
            $record->description = $description;
            $record->metadata = $metadata;
            
            // Log the values before saving
            \Log::info('Creating UserPlanFeatureHistory record', [
                'user_id' => $userId,
                'module_name' => $moduleName,
                'feature_key' => $featureKey,
                'plan_limit' => $planLimit,
                'current_usage' => $currentUsage,
                'description' => $description
            ]);
            
            $record->save();
            
            // Log successful creation
            \Log::info('UserPlanFeatureHistory record created successfully', [
                'record_id' => $record->id,
                'unique_id' => $record->unique_id,
                'user_id' => $record->user_id,
                'added_by' => $record->added_by,
                'module_name' => $record->module_name,
                'feature_key' => $record->feature_key
            ]);
            
            return $record;
        } catch (\Exception $e) {
            \Log::error('Error creating UserPlanFeatureHistory record', [
                'user_id' => $userId,
                'module_name' => $moduleName,
                'feature_key' => $featureKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get feature history summary for a user
     * 
     * @param int $userId User ID
     * @param string $featureKey Feature key
     * @return array
     */
    public static function getFeatureSummary($userId, $featureKey)
    {
        $currentUsage = self::getCurrentUsage($userId, $featureKey);
        $planLimit = self::getPlanLimit($userId, $featureKey);
        $remainingLimit = self::getRemainingLimit($userId, $featureKey);
        
        return [
            'current_usage' => $currentUsage,
            'plan_limit' => $planLimit,
            'remaining_limit' => $remainingLimit,
            'usage_percentage' => $planLimit > 0 ? round(($currentUsage / $planLimit) * 100, 2) : 0
        ];
    }
}
