<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Presentation extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','name','category_id','slide_type_id','slug','sort_order','description','summary','images','reading_time','added_by','status','files','show_on_home',"is_featured"];
    protected $encodedAttributes = ['unique_id','name','category_id','slide_type_id','slug','sort_order','description','summary','images','reading_time','added_by','status','files'];

    public function slideType()
    {
        return $this->belongsTo('App\Models\SlideType', 'slide_type_id');
    }
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
        Presentation::where("id", $id)->delete();
    }

    static function removeFiles($file_name)
    {
        $path = articleDir();
        mediaDeleteApi($path,$file_name);
        // if(file_exists(articleDir('r').$file_name)){
        //     unlink(articleDir('r').$file_name);
        // }
        // if(file_exists(filename: articleDir('m').$file_name)){
        //     unlink(articleDir('m').$file_name);
        // }
        // if(file_exists(articleDir('t').$file_name)){
        //     unlink(articleDir('t').$file_name);
        // }
    }

    public function seoDetails()
    {
        return $this->hasOne('App\Models\SeoDetails', 'reference_id', 'id')->where("module_type", "article");
    }
}
