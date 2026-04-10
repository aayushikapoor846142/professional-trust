<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class AdsPlan extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id', 'plan_name','added_by'];
    protected $encodedAttributes =['unique_id', 'plan_name','added_by'];

    static function deleteRecord($id)
    {
        $action = AdsPlan::where('id',$id)->first();
        AdsPlan::where("id", $id)->delete();
    }

}
