<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Category extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table = 'categories';

    protected $fillable = ['unique_id', 'name','slug','added_by'];
    protected $encodedAttributes =['unique_id', 'name','slug','added_by'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    public function articles()
    {
        return $this->hasMany('App\Models\Article', 'category_id');
    }

    public function guides()
    {
        return $this->hasMany('App\Models\Guide', 'category_id');
    }

    static function deleteRecord($id,$file_name)
    {
        $action = Category::where('id',$id)->first();
        // Professional::where('category_id',$id)->update(['category_id' => 0]);
        // Media::where(column: "id", $id)->delete();
     
        $cat_path = categoryDir();
     mediaDeleteApi($cat_path,$file_name);
  
        Category::where("id ", $id)->delete();
    }
}
