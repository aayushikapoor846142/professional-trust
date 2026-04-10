<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class Professional extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "professionals";

    protected $fillable = [
        'unique_id','category_id','linked_user_id','is_linked'
    ];

    // protected $encodedAttributes =[
    //     'unique_id','category_id','linked_user_id','is_linked'
    // ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($object) {
            $object->unique_id = randomNumber();
        });
        static::updating(function ($object) {
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

    public static function deleteRecord($id){
        $professionalObj = Professional::where("unique_id",$id)->first();
        if(isset($professionalObj)){
            ProfessionalSite::where('professional_id',$professionalObj->id)->delete();
            OtherProfessionalDetail::where('professional_id',$professionalObj->id)->delete();
            $professionalObj->delete();
        }
    }

    public function professionalDetail()
    {
        return $this->hasMany('App\Models\OtherProfessionalDetail','professional_id','id');

    }

    public function professionalWebsiteDetail()
    {
        return $this->belongsTo('App\Models\OtherProfessionalDetail','id','professional_id')->where('meta_key','Website Link');
    }
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');

    }
    
    public function AssignedTo()
    {
        return $this->belongsTo('App\Models\User','added_by');

    }

    

    public function professionalAddressDetail()
    {
        return $this->belongsTo('App\Models\OtherProfessionalDetail','id','professional_id')->where('meta_key','Address');
    }

    public function professionalAboutDetail()
    {
        return $this->belongsTo('App\Models\OtherProfessionalDetail','id','professional_id')->where('meta_key','About');
    }

    public function ClaimProfile()
    {
        return $this->belongsTo('App\Models\ClaimProfile','id','professional_id')->where('status','approved');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category','category_id','id');
    }

    public function services()
    {
        return $this->hasMany('App\Models\ProfessionalServices','user_id','linked_user_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','linked_user_id','id');
    }

    public function userDetails()
    {
        return $this->belongsTo('App\Models\UserDetails','linked_user_id','user_id');
    }

    public function companyLocations()
    {
        return $this->hasMany('App\Models\CompanyLocations','user_id','linked_user_id');
    }

    public function reviews(){
    	
        return $this->hasOne('App\Models\Reviews', 'professional_id','id');
    }

  
}
