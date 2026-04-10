<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleType extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','name','slug','sort_order','added_by'];

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
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');
    }

    static function deleteRecord($id)
    {
        ArticleType::where("id", $id)->delete();
    }
}
