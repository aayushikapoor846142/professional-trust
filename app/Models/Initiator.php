<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Initiator extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'name','slug','url','image','added_by'];

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    static function deleteRecord($id)
    {
        Initiator::where("id", $id)->where('deleted_by',auth()->user()->id??0);
        Initiator::where("id", $id)->delete();
    }
}
