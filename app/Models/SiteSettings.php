<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class SiteSettings extends Model
{
    use HasFactory,SoftDeletes;


    // The attributes that are mass assignable.
    protected $table="site_settings";
    
    protected $fillable = ['unique_id', 'site_title','site_logo','email','meta_title','meta_description','meta_keywords','updated_by','uap_reply_to','suggestion_email','company_name','company_address','city','state','zipcode','country','reconsider_tax','reconsideration_information','agent_application_fees'];
    protected $encodedAttributes = ['unique_id', 'site_title','site_logo','email','meta_title','meta_description','meta_keywords','updated_by','uap_reply_to','suggestion_email','reconsideration_information'];
    static function deleteRecord($id)
    {
        SiteSettings::where("id", $id)->delete();
    }

}
