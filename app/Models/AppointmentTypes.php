<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentTypes extends Model
{
    use HasFactory;
    protected $table = 'appointment_types';
    static function deleteRecord($id){
        AppointmentTypes::where("id",$id)->delete();
    }
    public function timeDuration()
    {
        return $this->belongsTo('App\Models\TimeDuration','duration','id');
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
}
