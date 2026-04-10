<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeDuration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "time_duration";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id',
        'professional_id',
        'name',
        'duration',
        'type',
        'break_time',
        'added_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot method to set up model events
     */
    protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            if (empty($object->unique_id)) {
                $object->unique_id = randomNumber();
            }
        });

        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

    /**
     * Delete a record by ID
     *
     * @param int $id
     * @return bool
     */
    static function deleteRecord($id)
    {
        return TimeDuration::where("id", $id)->delete();
    }

    /**
     * Scope to filter records visible to a specific user
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisibleToUser($query, $userId)
    {
        $professionalId = StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: show their own + their professional's records
            return $query->where(function ($q) use ($userId, $professionalId) {
                $q->where('added_by', $userId)
                ->orWhere('added_by', $professionalId);
            });
        } else {
            // Professional: show their own + all their staff's records
            $staffIds = StaffUser::where('added_by', $userId)->pluck('user_id');

            return $query->where(function ($q) use ($userId, $staffIds) {
                $q->where('added_by', $userId);

                if ($staffIds->isNotEmpty()) {
                    $q->orWhereIn('added_by', $staffIds);
                }
            });
        }
    }

    /**
     * Scope to filter by professional ID
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $professionalId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByProfessional($query, $professionalId)
    {
        return $query->where('professional_id', $professionalId);
    }

    /**
     * Scope to search by name
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByName($query, $search)
    {
        if (!empty($search)) {
            return $query->where('name', 'LIKE', "%{$search}%");
        }
        return $query;
    }

    /**
     * Check if the time duration is being used in appointment booking flows
     *
     * @return bool
     */
    public function isInUse()
    {
        return AppointmentBookingFlow::where('time_duration_id', $this->id)->exists();
    }

    /**
     * Get the professional that owns this time duration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    /**
     * Get the user who added this time duration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Get appointment booking flows that use this time duration
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appointmentBookingFlows()
    {
        return $this->hasMany(AppointmentBookingFlow::class, 'time_duration_id');
    }
}
