<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoicePaymentLink extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','invoice_id','user_id','payment_gateway','paid_date','payment_link','added_by','payment_session_id','amount','payment_status'];

    protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            $object->unique_id = randomNumber();
        });

    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
}
