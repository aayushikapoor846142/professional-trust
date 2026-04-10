<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ModulePrivacyOptions;

class UserPrivacySettings extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'unique_id',
        'user_id',
        'privacy_option_id',
        'privacy_option_value',
        'added_by'
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

    public function modulePrivacyOptions()
    {
        return $this->belongsTo(ModulePrivacyOptions::class, 'privacy_option_id', 'id');
    }
}
