<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SubscriptionInvoiceHistory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'subscription_invoice_history';

    protected $fillable = [
        'unique_id',
        'user_id',
        'subscription_history_id',
        'stripe_subscription_id',
        'stripe_invoice_number',
        'next_invoice_date',
        'stripe_invoice_status'
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
}
