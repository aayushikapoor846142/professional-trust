<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnouncementType extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'name','image','color_code','added_by'];

    static function deleteRecord($id)
    {
        AnnouncementType::where("id", $id)->delete();
    }

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');

    }

}
