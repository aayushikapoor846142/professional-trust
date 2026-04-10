<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Guide extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','name','category_id','slug','description','reading_time','summary','images','added_by'];
    protected $encodedAttributes =['unique_id','name','category_id','slug','description','reading_time','summary','images','added_by'];

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
      public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    static function deleteRecord($id)
    {
        Guide::where("id", $id)->delete();
    }

    static function removeImage($file_name)
    {
        if(file_exists(guideDir('r').$file_name)){
            unlink(guideDir('r').$file_name);
        }
        if(file_exists(filename: guideDir('m').$file_name)){
            unlink(guideDir('m').$file_name);
        }
        if(file_exists(guideDir('t').$file_name)){
            unlink(guideDir('t').$file_name);
        }
    }

    public function seoDetails()
    {
        return $this->hasOne('App\Models\SeoDetails', 'reference_id', 'id')->where("module_type", "guide");
    }

}
