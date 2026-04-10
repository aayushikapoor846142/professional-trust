<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class TempUser extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','email','json_data'];
    protected $encodedAttributes = ['unique_id','email'];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($object) {
            $object->unique_id = randomNumber();
        });
    }
}
