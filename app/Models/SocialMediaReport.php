<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class SocialMediaReport extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "social_media_reports";

    protected $fillable = ['unique_id', 'type','first_name','last_name','email','status_updated_by','status_updated_at','level','reference_token','submitted_from','sid','suggestion','evidences','mark_as_unauthorized'];
   
    public function socialMediaUap()
    {
        return $this->hasMany('App\Models\ReportSocialMediaContent','unauthorised_id','id');
    }

    public function SocialGroupUap()
    {
        return $this->hasMany('App\Models\ReportSocialMediaGroup','unauthorised_id','id');
    }

    static function deleteRecord($id)
    {
        $record = SocialMediaReport::where("unique_id", $id)->first();
        ReportSocialMediaContent::where("unauthorised_id",$record->id)->delete();
        ReportSocialMediaGroup::where("unauthorised_id",$record->id)->delete();
        SocialMediaReport::where("unique_id", $id)->delete();
    }
}
