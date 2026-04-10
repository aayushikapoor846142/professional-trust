<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class News extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','title','category_id','description','source_link','images','added_by'];
    protected $encodedAttributes = ['unique_id','title','category_id','description','source_link','images','added_by'];
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
        News::where("id", $id)->delete();
    }

    static function removeImage($file_name)
    {
        if(file_exists(newsDir('r').$file_name)){
            unlink(newsDir('r').$file_name);
        }
        if(file_exists(filename: newsDir('m').$file_name)){
            unlink(newsDir('m').$file_name);
        }
        if(file_exists(newsDir('t').$file_name)){
            unlink(newsDir('t').$file_name);
        }
    }
}
