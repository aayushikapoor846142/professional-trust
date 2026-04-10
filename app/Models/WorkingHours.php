<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\UserIdScope;

class WorkingHours extends Model
{
    protected $table = "working_hours";
    use HasFactory;
    
        // protected static function booted()
        // {
        //     parent::booted();

        //     static::addGlobalScope(new AppointmentUserIdScope());
        // }
  public function scopeVisibleToUser($query, $userId)
    {
        $professionalId = StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: show their own + their professional's records
            return $query->where(function ($q) use ($userId, $professionalId) {
                $q->where('professional_id', $userId)
                ->orWhere('professional_id', $professionalId);
            });
        } else {
            // Professional: show their own + all their staff's records
            $staffIds = StaffUser::where('added_by', $userId)->pluck('user_id');

            return $query->where(function ($q) use ($userId, $staffIds) {
                $q->where('professional_id', $userId);

                if ($staffIds->isNotEmpty()) {
                    $q->orWhereIn('professional_id', $staffIds);
                }
            });
        }
    }
    public function breaks()
    {
        return $this->hasMany(WorkingHourBreak::class);
    }

}
