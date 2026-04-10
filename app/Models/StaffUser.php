<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffUser extends Model
{
    use HasFactory,SoftDeletes;

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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id',
        'user_id',
        'added_by',
    ];

    
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
    
    /**
     * Get the user associated with the staff record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    

    /**
     * Get the user who added this staff record.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
