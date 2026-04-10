<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Tags extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table = "tags";
    protected $fillable = ['unique_id','name','added_by'];
    protected $encodedAttributes = ['unique_id', 'name','added_by'];
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');

    }


    static function deleteRecord($id)
    {
        Tags::where("id", $id)->delete();
    }

   
}
