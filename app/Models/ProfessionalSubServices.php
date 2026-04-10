<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProfessionalSubServices extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','service_id' ,'user_id','sub_services_type_id','tbd','professional_service_id','professional_fees','minimum_fees','maximum_fees','consultancy_fees','form_id','description','added_by','document_folders','status'];


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
            return $this->added_by == $userId;
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
    public function forms()
    {
        return $this->belongsTo('App\Models\Forms','form_id','id');
    }
    public function subService()
    {
        return $this->belongsTo('App\Models\ImmigrationServices','service_id');
    }
    public function subServiceTypes()
    {
        return $this->belongsTo('App\Models\SubServicesTypes','sub_services_type_id','id');
    }

    public function subServicesType()
    {
        return $this->belongsTo('App\Models\SubServicesTypes','sub_services_type_id','id');
    }

    public function form()
    {
        return $this->belongsTo('App\Models\Forms','form_id','id');
    }

    public static function deleteRecord($id)
    {
        ProfessionalSubServices::where('id', $id)->delete();
    }

}
