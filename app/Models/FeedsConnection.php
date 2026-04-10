<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedsConnection extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'connection_with','user_id','connection_type','status'];

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
    
    public function following()
    {
        return $this->belongsTo('App\Models\User','connection_with','id');
    }

    public function follower()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
}
