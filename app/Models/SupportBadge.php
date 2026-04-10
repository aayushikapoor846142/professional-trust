<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportBadge extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','points', 'badge_name','status','added_by'];

    static function deleteRecord($id)
    {
        SupportBadge::where("unique_id", $id)->delete();
    }
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
}
