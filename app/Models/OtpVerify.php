<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class OtpVerify extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table = 'otp_verify';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id',
        'email',
        'otp',
        'otp_expiry_time',
        'attempt',
        'resend_attempt'
    ];

    protected $encodedAttributes = [
        'unique_id',
        'email',
        'otp',
        'otp_expiry_time',
    ];
    protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            $object->unique_id = randomNumber();
            $object->user_location = json_encode(detectUserLocation());
        });

        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
           $object->user_location = json_encode(detectUserLocation());
        });
    }
}
