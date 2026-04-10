<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPlanFeature extends Model
{
    use HasFactory;

    protected $table = 'membership_plan_features';

    protected $fillable = [
        'feature_name',
        'feature_slug',
        'description',
        'is_active'
    ];

    /**
     * Get the feature values for this feature
     */
    public function featureValues()
    {
        return $this->hasMany(MembershipPlanFeatureValue::class, 'feature_id');
    }

    /**
     * Get feature values for a specific plan
     */
    public function getFeatureValueForPlan($planId)
    {
        return $this->featureValues()
            ->where('membership_plan_id', $planId)
            ->first();
    }
} 