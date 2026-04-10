<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ScreenshotLog extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'error_log',
        'type',
        'unique_id',
    ];
    protected $encodedAttributes = [
        'error_log',
        'type',
        'unique_id',
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
