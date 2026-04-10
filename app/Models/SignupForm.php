<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SignupForm extends Model
{
    use HasFactory,SoftDeletes;

    
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    static function deleteRecord($id)
    {
        $action = SignupForm::where('id',$id)->first();
        $file_path = otherFileDir();
        if($action->image != ''){
            mediaDeleteApi($file_path,$action->image);
        }
        if($action->banner_image != ''){
            mediaDeleteApi($file_path,$action->banner_image);
        }
  
        SignupForm::where("id", $id)->delete();
    }
}
