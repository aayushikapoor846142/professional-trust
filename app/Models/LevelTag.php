<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevelTag extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'tag_name','level','summary','added_by'];

    static function deleteRecord($id){
        LevelTag::where("id",$id)->delete();
    }

    public function addedBy()
    {
        return $this->belongsTo('App\Models\User','added_by');
    }

    public function levels()
    {
        return $this->belongsTo('App\Models\Level', 'level','id');
    }
}
