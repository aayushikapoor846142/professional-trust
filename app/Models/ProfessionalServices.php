<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ProfessionalServices extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table="professional_services";
    
    protected $fillable = ['unique_id','parent_service_id','service_id','user_id'];
    protected $encodedAttributes =['unique_id','parent_service_id','service_id','user_id'];


    public function scopeVisibleToUser($query, $userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: show their own + their professional's records
            return $query->where(function ($q) use ($userId, $professionalId) {
                $q->where('user_id', $userId)
                ->orWhere('user_id', $professionalId);
            });
        } else {
            // Professional: show their own + all their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id');

            return $query->where(function ($q) use ($userId, $staffIds) {
                $q->where('user_id', $userId);

                if ($staffIds->isNotEmpty()) {
                    $q->orWhereIn('user_id', $staffIds);
                }
            });
        }
    }

    public function isEditableBy($userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: can only edit their own records (not professional's)
            return $this->user_id == $userId;
        } else {
            // Professional: can edit their own and their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id')->toArray();
            return $this->user_id == $userId || in_array($this->user_id, $staffIds);
        }
    }


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
    public function ImmigrationServices()
    {
        // return $this->belongsTo('App\Models\ImmigrationServices','parent_service_id','id');
        return $this->belongsTo('App\Models\ImmigrationServices','service_id','id');
    }

    public function parentService()
    {
        return $this->belongsTo('App\Models\ImmigrationServices', 'parent_service_id', 'id');
    }
    public function getParentSubServicesAttribute()
    {
        return ProfessionalServices::with(['parentService','subServices'])->where("parent_service_id",$this->parent_service_id)->where("user_id",$this->user_id)->get();

    }
    public function subServices()
    {
        return $this->belongsTo('App\Models\ImmigrationServices', 'service_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function price()
    {
        return $this->hasOne('App\Models\ProfessionalServicePrice', 'professional_service_id');
    }

    public static function deleteRecord($id)
    {
 
        $record = ProfessionalServices::where("unique_id",$id)->first();
        ProfessionalServicePrice::where("professional_service_id",$record->id)->delete();
        ProfessionalSubServices::where('professional_service_id',$record->id)->delete();
        ProfessionalServices::where('unique_id', $id)->delete();
    }

    public function Cases()
    {
        return $this->hasMany(Cases::class, 'parent_service_id', 'sub_service_id');
    }

    public function assessmentForms()
    {
        return $this->hasMany('App\Models\ServiceAssesmentForm', 'professional_service_id');
    }
    public function professionalServiceTypes()
    {
        return $this->hasMany('App\Models\ProfessionalSubServices', 'professional_service_id');
    }

    public function checkIfCaseLinked($professional_id,$parent_service_id,$sub_service_id){
        $case_count = CaseWithProfessionals::where('professional_id',$professional_id)->where("parent_service_id",$parent_service_id)->where('sub_service_id',$sub_service_id)->count();
        return $case_count;
    }
    
}
