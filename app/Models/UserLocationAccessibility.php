<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserLocationAccessibility extends Model
{
    use HasFactory,SoftDeletes;

     protected $table = "user_location_accessibilities";

        protected $fillable = [
        'unique_id',
        'user_id',
        'ip_address',
        'log_info',
        'city',
        'device_type',
        'state',
        'country',
        'timezone',
        'device_name',
        'browser_name',
        'is_signup_location'
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
