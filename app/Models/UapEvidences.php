<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UapEvidences extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'uap_id','name','file_name','file_type','added_by'];

    static function deleteRecord($id){
        $uap_evidences = UapEvidences::where('id',$id)->first();
        awsDeleteFile(config("awsfilepath.investigator_evidences"). '/' . $uap_evidences->file_name);
        UapEvidences::where("id",$id)->delete();
    }
}
