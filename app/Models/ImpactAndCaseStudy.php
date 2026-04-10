<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImpactAndCaseStudy extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','title','link','sort_order','pin','description','added_by','category_id','link_type','custom_link','custom_page','summary','reference_link','status'];

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
        ImpactAndCaseStudy::where("id", $id)->delete();
    }
    public function seoDetails()
    {
        return $this->hasOne('App\Models\SeoDetails', 'reference_id', 'id')->where("module_type","knowledgebase");
    }
}
