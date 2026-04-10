<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcements extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'title','announcement_type_id','description','expiry_date','status','added_by'];

    static function deleteRecord($id)
    {
        Announcements::where("id", $id)->delete();
    }

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');

    }

    public function announcementType()
    {
        return $this->belongsTo('App\Models\AnnouncementType','announcement_type_id');

    }
}
