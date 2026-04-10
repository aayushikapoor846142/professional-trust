<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use App\Models\BaseModel;
class UserDetails extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','user_id','photo_for_verification','is_verified_reviewer','company_logo','address_2','languages_known'];
    protected $table = "user_details";
   // protected $encodedAttributes = ['unique_id','user_id','photo_for_verification','is_verified_reviewer','company_logo'];
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
