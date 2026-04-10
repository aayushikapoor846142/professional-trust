<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UapProfessionals extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "uap_professionals";

    protected $fillable = ['unique_id', 'name','owner_name','contact','address','email','alternate_email','social_media','website_link','added_by','country','city','state','is_publish','status','invitation_status','reference_user_id','token','inviation_accept_date','uap_type','category_level_id'];

    public function addedBy()
    {
        return $this->belongsTo('App\Models\User','added_by');
    }

    static function deleteRecord($id){
        $uapProf = UapProfessionals::where("id",$id)->first();
        if(!empty($uapProf)){
            UapProfessionals::where("id",$id)->delete();
            $profSites = UapProfessionalSites::where("uap_id",$uapProf->id)->get()->pluck('id')->toArray();
            UapSitesScreenshot::whereIn("uap_site_id",$profSites)->delete();
            UapProfessionalSites::where("uap_id",$uapProf->id)->delete();

        }
    }

    public function uapPingLevelTags()
    {
        return $this->hasMany('App\Models\UapLevelTag','uap_id','id')->where('is_ping',1);
    }

    public function uapLevelTags()
    {
        return $this->hasMany('App\Models\UapLevelTag','uap_id','id');
    }

}
