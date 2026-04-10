<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Scopes\AddedByScope;

class AppointmentBooking extends Model
{
    protected $table = 'appointment_booking';
    use HasFactory;

    protected static function booted()
    {
        parent::booted();

      //  static::addGlobalScope(new AddedByScope());
    }

    public function getStartTimeConvertedAttribute()
    {
        if (!$this->start_time) {
            return null;
        }
    
        // Get professional's location timezone from CompanyLocations
        $timezone =  $this->location_id
            ? CompanyLocations::where('id', $this->location_id)->value('timezone')
            : 'UTC';
    
        return Carbon::createFromFormat('H:i:s', $this->start_time, 'UTC')
                     ->setTimezone($timezone)
                     ->format('h:i A');
    }
    
    
    public function getEndTimeConvertedAttribute()
    {
        if (!$this->end_time) {
            return null;
        }
        $timezone =  $this->location_id
        ? CompanyLocations::where('id', $this->location_id)->value('timezone')
        : 'UTC';

        return Carbon::createFromFormat('H:i:s', $this->end_time, 'UTC')
        ->setTimezone($timezone)
        ->format('h:i A');
       
    }
    
    public function getProfileTimezoneStartTimeAttribute()
    {
         if (!$this->start_time) {
            return null;
        }
    
        $timezone = auth()->user()->timezone ?? 'UTC';
        return Carbon::createFromFormat('H:i:s', $this->start_time, 'UTC')
                     ->setTimezone($timezone)
                     ->format('h:i A');
    }
    
    
    public function getProfileTimezoneEndTimeAttribute()
    {
          if (!$this->end_time) {
            return null;
        }
    
        $timezone = auth()->user()->timezone ?? 'UTC';
        return Carbon::createFromFormat('H:i:s', $this->end_time, 'UTC')
                     ->setTimezone($timezone)
                     ->format('h:i A');
       
    }
    
    // // In WorkingHours.php
    // public function scopeVisibleToUser($query, $userId)
    // {
    //     $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

    //     return $query->where(function ($q) use ($userId, $professionalId) {
    //         $q->where('added_by', $userId);

    //         if ($professionalId) {
    //             $q->orWhere('added_by', $professionalId);
    //         }
    //     });
    // }
   
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

    static function deleteRecord($id)
    {
        AppointmentBooking::where("id", $id)->delete();
    }

    public function service()
    {
        return $this->belongsTo('App\Models\ImmigrationServices','sub_service_id','id');
    }
    
    
    public function professional()
    {
        return $this->belongsTo('App\Models\User','professional_id','id');
    }
    public function client()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    public function location()
    {
     return $this->belongsTo('App\Models\CompanyLocations', 'location_id', 'id')->withTrashed();
    }
   public function reminder()
    {
        return $this->hasOne(AppointmentReminder::class, 'appointment_id');
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
