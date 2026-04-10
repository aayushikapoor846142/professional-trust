<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "levels";

    protected $fillable = ['unique_id', 'name','slug','summary','added_by','risk_level'];

    static function deleteRecord($id){
        Level::where("id",$id)->delete();
    }

    public function addedBy()
    {
        return $this->belongsTo('App\Models\User','added_by');
    }
}
