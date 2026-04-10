<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ApiKey;
use App\Models\BaseModel;

class ApiGroups extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id', 'name','added_by'];
    protected $encodedAttributes =['unique_id', 'name','added_by'];

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    static function deleteRecord($id)
    {
        ApiKey::where('group_id',$id)->delete();
        ApiGroups::where("id", $id)->delete();
    }


}
