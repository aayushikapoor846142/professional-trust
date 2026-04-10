<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class FaqCategory extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'name','slug','added_by'];
    protected $encodedAttributes =['unique_id','name','slug','added_by'];

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    public function faqs()
    {
        return $this->hasMany('App\Models\Faq', 'category_id')->orderBy("sort_order",'asc');
    }

    public function knowledgeBase()
    {
        return $this->hasMany('App\Models\KnowledgeBase', 'category_id')->orderBy("sort_order",'asc')->where("status",1);
    }
    
    static function deleteRecord($id)
    {
        $action = FaqCategory::where('id',$id)->first();
        FaqCategory::where("id", $id)->delete();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
}
