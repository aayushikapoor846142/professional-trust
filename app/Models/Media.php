<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class Media extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable=['file_name','file_type','added_by','unique_id'];
    protected $encodedAttributes = ['file_name','file_type','added_by','unique_id'];
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
    
    static function deleteRecord($id,$file_name)
    {
        
        // if(file_exists(mediaDir('r').$file_name)){
        //     // return $file_name;
        //     unlink(mediaDir('r').$file_name);
        
        // }
        $media_path = mediaDir();
        mediaDeleteApi($media_path,$file_name);
        Media::where("id", $id)->delete();
    }

}
