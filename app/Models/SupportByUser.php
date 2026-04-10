<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SupportByUser extends Model
{
    protected $table = 'support_by_users';
    use HasFactory,SoftDeletes;

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

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function userSubscriptionHistory()
    {
        return $this->belongsTo(UserSubscriptionHistory::class, 'subscription_id', 'stripe_subscription_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'reference_id', 'id');
    }
}
