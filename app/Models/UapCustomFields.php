<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UapCustomFields extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','slug' ,'name','note','added_by'];

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    static function deleteRecord($id)
    {
        UapCustomFields::where("id", $id)->delete();
    }
}
