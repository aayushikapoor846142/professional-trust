<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CaseWithProfessionals extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'unique_id',
        'professional_id',
        'client_id',
        'case_title',
        'case_description',
        'parent_service_id',
        'sub_service_id',
        'service_type_id',
        'status',
        'completed_step',
        'form_json',
        'form_reply_json',
        'added_by',
        'paid_amount',
        'payment_status',
        'paid_date',
        'form_id',
        'priority'
    ];

    protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            $object->unique_id = randomNumber();
        });

        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

    public function services()
    {
        return $this->belongsTo('App\Models\ImmigrationServices', 'parent_service_id');
    }

    public function subServices()
    {
        return $this->belongsTo('App\Models\ImmigrationServices', 'sub_service_id');
    }

    
    public function clients()
    {
        return $this->belongsTo('App\Models\User', 'client_id');
    }

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    public function subServicesTypes()
    {
        return $this->belongsTo('App\Models\ProfessionalSubServices', 'service_type_id','sub_services_type_id');
    }

    public function caseChats()
    {
        return $this->belongsTo('App\Models\CaseChat', 'id','case_id');
    }

    public function retainAgreements()
    {
        return $this->belongsTo('App\Models\CaseRetainAgreements', 'id','professional_case_id');
    }
    public function professional()
    {
        return $this->belongsTo('App\Models\User', 'professional_id');
    }
    public function client()
    {
        return $this->belongsTo('App\Models\User', 'client_id');
    }

    public function assignedStaff()
    {
        return $this->hasMany('App\Models\StaffCases', 'case_id');
    }
    
    public function completedCaseStage()
    {
        return $this->hasMany('App\Models\CaseStages', 'case_id')->where('status','complete');
    }

    public function totalCaseStage()
    {
        return $this->hasMany('App\Models\CaseStages', 'case_id');
    }

    public function caseFiles()
    {
        return $this->hasMany('App\Models\CaseDocuments', 'case_id')->whereHas('CaseFolder');
    }

     public function isEditableBy($userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: can only edit their own records (not professional's)
            return $this->professional_id == $userId;
        } else {
            // Professional: can edit their own and their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id')->toArray();
            return $this->professional_id == $userId || in_array($this->professional_id, $staffIds);
        }
    }
    
}
