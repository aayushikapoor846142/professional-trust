<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryLevels extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'name','type','summary','added_by'];

    static function deleteRecord($id){
        CategoryLevels::where("id",$id)->delete();
    }

    public function addedBy()
    {
        return $this->belongsTo('App\Models\User','added_by');
    }
}
