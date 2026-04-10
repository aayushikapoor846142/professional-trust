<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentBookingFlow extends Model
{
    use HasFactory;
    protected $table = 'appointment_booking_flow';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id',
        'title',
        'status',
        'description',
        'time_duration_id',
        'appointment_type_id',
        'location_id',
        'appointment_mode',
        'timezone',
        'service_id',
        'working_hours_id',
        'added_by',
        'professional_id',
    ];


    public function timeDuration()
    {
        return $this->belongsTo('App\Models\TimeDuration','time_duration_id','id');
    }

    public function appointmentType()
    {
        return $this->belongsTo('App\Models\AppointmentTypes','appointment_type_id','id');
    }

    public function service()
    {
        return $this->belongsTo('App\Models\ProfessionalServices','service_id','id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\CompanyLocations','location_id','id');
    }

    public function workingHours()
    {
        return $this->belongsTo('App\Models\WorkingHours','working_hours_id','id');
    }
     public function scopeVisibleToUser($query, $userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: show their own + their professional's records
            return $query->where(function ($q) use ($userId, $professionalId) {
                $q->where('added_by', $userId)
                ->orWhere('added_by', $professionalId);
            });
        } else {
            // Professional: show their own + all their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id');

            return $query->where(function ($q) use ($userId, $staffIds) {
                $q->where('added_by', $userId);

                if ($staffIds->isNotEmpty()) {
                    $q->orWhereIn('added_by', $staffIds);
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
            return $this->added_by == $userId || in_array($this->added_by, $staffIds);
        }
    }
}
