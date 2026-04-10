<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProfessionalLeave extends Model
{
    protected $fillable = [
        'unique_id',
        'professional_id',
        'location_id',
        'leave_date',
        'start_time',
        'end_time',
        'reason',
    ];

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function location()
    {
        return $this->belongsTo(CompanyLocations::class);
    }
    static function deleteRecord($id)
    {
        ProfessionalLeave::where("id", $id)->delete();
    }
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
}
