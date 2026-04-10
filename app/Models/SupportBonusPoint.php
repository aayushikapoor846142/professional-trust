<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportBonusPoint extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','percent', 'amount','status','added_by','currency'];

    static function deleteRecord($id)
    {
        SupportBonusPoint::where("unique_id", $id)->delete();
    }
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
}
