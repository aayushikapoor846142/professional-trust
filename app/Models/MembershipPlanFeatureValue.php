<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPlanFeatureValue extends Model
{
    use HasFactory;

    protected $table = 'membership_plan_features_values';

    protected $fillable = [
        'membership_plan_feature_id',
        'membership_plan_id',
        'feature_value',
        'is_enabled',
        'limit_value'
    ];

    /**
     * Get the feature definition
     */
    public function feature()
    {
        return $this->belongsTo(MembershipPlanFeature::class, 'membership_plan_feature_id');
    }

    /**
     * Get the membership plan
     */
    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    /**
     * Check if feature is enabled for this plan
     */
    public function isEnabled()
    {
        return $this->is_enabled == 1;
    }

    /**
     * Get the limit value (returns -1 for unlimited)
     */
    public function getLimit()
    {
        return $this->value ?? -1;
    }

    /**
     * Check if feature is unlimited
     */
    public function isUnlimited()
    {
        return $this->getLimit() == -1;
    }
} 