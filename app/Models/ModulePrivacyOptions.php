<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulePrivacyOptions extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','module_privacy_id' ,'applicable_role','filed_type','allow_to_select','action_label','action_slug','options','added_by'];

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

    public function modulePrivacy()
    {
        return $this->belongsTo('App\Models\ModulePrivacy', 'module_privacy_id');
    }
    
    public function userPrivacy()
    {
        return $this->belongsTo('App\Models\UserPrivacySettings', 'id','privacy_option_id')->where('user_id',auth()->user()->id);
    }

    public function userPrivacys()
    {
        return $this->belongsTo('App\Models\UserPrivacySettings', 'id','privacy_option_id');
    }
}
