<?php

namespace App\Services;

use App\Models\TimeDuration;
use App\Models\AppointmentBookingFlow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TimeDurationService
{
    /**
     * Validation rules for time duration operations
     */
    private const VALIDATION_RULES = [
        'name' => 'required|max:255',
        'duration' => 'required|max:255',
        'type' => 'required|max:255',
        'break_time' => 'required|max:255',
    ];

    /**
     * Custom validation messages
     */
    private const VALIDATION_MESSAGES = [
        'name.required' => 'The name field is required.',
        'duration.required' => 'The duration field is required.',
        'type.required' => 'The type field is required.',
        'break_time.required' => 'The break time field is required.',
    ];

    /**
     * Validate time duration data
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateTimeDuration(array $data)
    {
        return Validator::make($data, self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
    }

    /**
     * Format validation errors for JSON response
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return array
     */
    public function formatValidationErrors($validator)
    {
        $error = $validator->errors()->toArray();
        $errMsg = [];
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return $errMsg;
    }

    /**
     * Check if time duration name already exists for a professional
     *
     * @param string $name
     * @param int $professionalId
     * @param int|null $excludeId
     * @return bool
     */
    public function nameExists($name, $professionalId, $excludeId = null)
    {
        $query = TimeDuration::where('name', $name)
            ->where('professional_id', $professionalId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Create a new time duration
     *
     * @param array $data
     * @param int $professionalId
     * @param int $addedBy
     * @return TimeDuration
     */
    public function createTimeDuration(array $data, $professionalId, $addedBy)
    {
        return TimeDuration::create([
            'unique_id' => randomNumber(),
            'professional_id' => $professionalId,
            'name' => $data['name'],
            'duration' => $data['duration'],
            'type' => $data['type'],
            'break_time' => $data['break_time'],
            'added_by' => $addedBy,
        ]);
    }

    /**
     * Update an existing time duration
     *
     * @param TimeDuration $timeDuration
     * @param array $data
     * @return bool
     */
    public function updateTimeDuration(TimeDuration $timeDuration, array $data)
    {
        return $timeDuration->update([
            'name' => $data['name'],
            'duration' => $data['duration'],
            'type' => $data['type'],
            'break_time' => $data['break_time'],
        ]);
    }

    /**
     * Delete a time duration if it's not in use
     *
     * @param TimeDuration $timeDuration
     * @return bool
     */
    public function deleteTimeDuration(TimeDuration $timeDuration)
    {
        if ($this->isTimeDurationInUse($timeDuration->id)) {
            return false;
        }

        return TimeDuration::deleteRecord($timeDuration->id);
    }

    /**
     * Check if time duration is being used in other tables
     *
     * @param int $timeDurationId
     * @return bool
     */
    public function isTimeDurationInUse($timeDurationId)
    {
        // Check in appointment booking flow
        $usedInBookingFlow = AppointmentBookingFlow::where('time_duration_id', $timeDurationId)->exists();
        
        // Add more checks here if time duration is used in other tables
        // Example: $usedInAppointments = AppointmentBooking::where('time_duration_id', $timeDurationId)->exists();
        
        return $usedInBookingFlow;
    }

    /**
     * Get time durations for a professional with search and pagination
     *
     * @param int $professionalId
     * @param int $userId
     * @param string $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTimeDurationsForProfessional($professionalId, $userId, $search = '')
    {
        return TimeDuration::where(function ($query) use ($search, $professionalId) {
                if ($search != '') {
                    $query->where("name", "LIKE", "%" . $search . "%");
                }
                $query->where("professional_id", $professionalId);
            })
            ->orderBy('id', "desc")
            ->visibleToUser($userId)
            ->paginate();
    }

    /**
     * Get time duration by unique ID
     *
     * @param string $uniqueId
     * @return TimeDuration|null
     */
    public function getByUniqueId($uniqueId)
    {
        return TimeDuration::where('unique_id', $uniqueId)->first();
    }

    /**
     * Check if user can edit the given record
     *
     * @param TimeDuration $record
     * @param int $professionalId
     * @return bool
     */
    public function canEditRecord($record, $professionalId)
    {
        return $record->professional_id == $professionalId;
    }

    /**
     * Bulk delete time durations
     *
     * @param array $uniqueIds
     * @param int $professionalId
     * @return array
     */
    public function bulkDelete($uniqueIds, $professionalId)
    {
        $deletedCount = 0;
        $errorCount = 0;

        foreach ($uniqueIds as $uniqueId) {
            $timeDuration = $this->getByUniqueId($uniqueId);
            
            if ($timeDuration && $this->canEditRecord($timeDuration, $professionalId)) {
                if (!$this->isTimeDurationInUse($timeDuration->id)) {
                    if ($this->deleteTimeDuration($timeDuration)) {
                        $deletedCount++;
                    } else {
                        $errorCount++;
                    }
                } else {
                    $errorCount++;
                }
            } else {
                $errorCount++;
            }
        }

        return [
            'deleted_count' => $deletedCount,
            'error_count' => $errorCount
        ];
    }

    /**
     * Get time durations for appointment booking flow component
     *
     * @param int $professionalId
     * @param int $userId
     * @param string $bookingFlowId
     * @return array
     */
    public function getTimeDurationsForBookingFlow($professionalId, $userId, $bookingFlowId = null)
    {
        $durations = checkTimeDuration();
        
        $appointmentBookingFlow = null;
        if ($bookingFlowId) {
            $appointmentBookingFlow = AppointmentBookingFlow::where('unique_id', $bookingFlowId)->first();
        }

        return [
            'durations' => $durations,
            'selected' => $appointmentBookingFlow ? $appointmentBookingFlow->time_duration_id : '',
            'appointment_booking_flow' => $appointmentBookingFlow
        ];
    }
} 