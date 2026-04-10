<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubscriptionHistory extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_subscription_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id',
        'membership_plans_plan_id',
        'stripe_subscription_id',
        'subscription_status',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($object) {
            $object->unique_id = randomNumber();
        });
        static::updating(function ($object) {
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }
    /**
     * Get the membership plan associated with the subscription history.
     */
    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plans_plan_id');
    }
      
    /**
     * Get the subscription invoice histories associated with this subscription history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptionHistory()
    {
        return $this->hasMany(SubscriptionInvoiceHistory::class, 'subscription_history_id', 'id');
    }

    /**
     * Get the user associated with the subscription history.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id', 'user_id');
    }

    public function supportByUsers()
    {
        return $this->hasMany(SupportByUser::class, 'subscription_id', 'stripe_subscription_id');
    }
}
