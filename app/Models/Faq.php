<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Faq extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','title','category_id','sort_order','pin','description','added_by'];
    protected $encodedAttributes =['unique_id','title','category_id','sort_order','pin','description','added_by'];

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\FaqCategory', 'category_id');
    }

    static function deleteRecord($id)
    {
        Faq::where("id", $id)->delete();
    }
    public function seoDetails()
    {
        return $this->hasOne('App\Models\SeoDetails', 'reference_id', 'id')->where("module_type", "faq");
    }
}
