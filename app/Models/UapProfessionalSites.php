<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UapProfessionalSites extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "uap_professional_sites";

    public function screenshots()
    {
        return $this->hasMany('App\Models\UapSitesScreenshot', 'uap_site_id', 'id');
  
    }

    static function deleteRecord($id){
        UapProfessionalSites::where("id",$id)->delete();
        UapSitesScreenshot::where("uap_site_id",$id)->delete();
    }
}
