<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipPlan extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id',
        'plan_title',
        'description',
        'payment_type',
        'amount',
        'currency',
        'added_by',
        'stripe_plan',
        'stripe_price'
    ];

    protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            $object->unique_id = randomNumber();
        });

        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }
    
    /**
     * The user who added the membership plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
  public function modules()
    {
        return $this->belongsToMany(Module::class, 'membership_plan_module', 'membership_plan_id', 'module_id');
    }

    public function activeSubscriptionHistory()
    {
        return $this->hasMany(UserSubscriptionHistory::class, 'membership_plans_plan_id', 'id')
            ->where('subscription_status', 'active');
    }
   

    static function deleteRecord($id)
    {
        MembershipPlan::where("unique_id", $id)->delete();
     
    }
   public function membershipPlanModules()
    {
        return $this->hasMany(MembershipPlanModule::class);
    }
 public function UserSubscriptionHistory()
    {
        return $this->hasMany(UserSubscriptionHistory::class, 'membership_plans_plan_id', 'id');
    }

    /**
     * Get the features for this membership plan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function features()
    {
        return $this->hasMany(MembershipPlanFeatureValue::class, 'membership_plan_id');
    }

    /**
     * Get active features for this plan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function activeFeatures()
    // {
    //     return $this->hasMany(MembershipPlanFeatureValue::class, 'membership_plan_id')
    //         ->where('is_enabled', 1);
    // }
    
    public function activeFeatures()
    {
        return $this->hasMany(MembershipPlanFeatureValue::class, 'membership_plan_id');
          
    }
}
