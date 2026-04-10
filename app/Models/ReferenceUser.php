<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceUser extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'token','first_name','last_name','email','phone_no','status','added_by'];

    
    static function deleteRecord($id){
        ReferenceUser::where("id",$id)->delete();
       
    }
}
