<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurInitiatives extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','initiator_id','title','image','short_title','description','slug','explore_content_link','report_content_link','page_type','static_page','added_by','show_on_home'];

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    public function initiator()
    {
        return $this->belongsTo('App\Models\Initiator', 'initiator_id');
    }
     static function deleteRecord($id)
    {
        OurInitiatives::where("id", $id)->delete();
    }
    public function seoDetails()
    {
        return $this->hasOne('App\Models\SeoDetails', 'reference_id', 'id')->where("module_type","initiatives");
    }

}
