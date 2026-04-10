<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeoDetails extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'seo_details';

    protected $fillable = [
        'unique_id',
        'module_type',
        'page_route',
        'reference_id',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'added_by',
        'url_type',
        'image_alt_tag',
        'image'
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

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');

    }

    static function deleteRecord($id)
    {
        SeoDetails::where("id", $id)->delete();
    }
}
