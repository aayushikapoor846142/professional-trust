<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class OtherProfessionalDetail extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','professional_id','meta_key','meta_value','added_by'];
    protected $encodedAttributes = ['meta_key','meta_value'];
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

    public function professional()
    {
        return $this->belongsTo('App\Models\Professional','professional_id');
    }
}
