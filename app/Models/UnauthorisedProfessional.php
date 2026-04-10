<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class UnauthorisedProfessional extends BaseModel
{
    use HasFactory,SoftDeletes;

    // The attributes that are mass assignable.
    protected $fillable = ['unique_id', 'type','first_name','last_name','email','status_updated_by','status_updated_at','level','reference_token','submitted_from','sid','suggestion','evidences','mark_as_unauthorized'];
    protected $encodedAttributes = ['suggestion'];
    protected $appends = ['uap_details'];
    static function deleteRecord($id)
    {
        $record = UnauthorisedProfessional::where("unique_id", $id)->first();
        IndividualUaps::where("unauthorised_id",$record->id)->delete();
        CorporateUap::where("unauthorised_id",$record->id)->delete();
        SocialMediaUap::where("unauthorised_id",$record->id)->delete();
        SocialGroupUap::where("unauthorised_id",$record->id)->delete();
        UnauthorisedProfessional::where("unique_id", $id)->delete();
    }
    
    public function getUapDetailsAttribute(){
        $detail = array();
        if($this->type == 'individual'){
            $detail = $this->individual;
        }elseif($this->type == 'corporate'){
            $detail =  $this->corporate;
        }
        return $detail;
    }
    public function individual()
    {
        return $this->belongsTo('App\Models\IndividualUaps','id','unauthorised_id');
    }

    public function corporate()
    {
        return $this->belongsTo('App\Models\CorporateUap','id','unauthorised_id');
    }

    public function socialMediaUap()
    {
        return $this->hasMany('App\Models\SocialMediaUap','unauthorised_id','id');
    }

    public function SocialGroupUap()
    {
        return $this->hasMany('App\Models\SocialGroupUap','unauthorised_id','id');
    }

    public function assignTo()
    {
        return $this->hasMany('App\Models\UnauthorisedProfessionalsAssign','uap_id','id')->where('staff_id',\Auth::user()->id);
    }

    

}
