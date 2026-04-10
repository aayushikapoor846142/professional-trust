<?php

namespace App\Services;

use App\Models\AppointmentTypes;
use App\Models\TimeDuration;
use App\Models\AppointmentBookingFlow;
use Illuminate\Support\Facades\Validator;

class AppointmentTypesService
{
    public function getDurations($professionalId, $userId)
    {
        return TimeDuration::where('professional_id', $professionalId)
            ->visibleToUser($userId)
            ->get();
    }

    public function getAppointmentTypesList($search, $professionalId, $userId)
    {
        return AppointmentTypes::with('timeDuration')
            ->where(function ($query) use ($search, $professionalId) {
                if ($search != '') {
                    $query->where('name', 'LIKE', "%{$search}%");
                }
                $query->where('professional_id', $professionalId);
            })
            ->visibleToUser($userId)
            ->orderBy('id', 'desc')
            ->paginate();
    }

    public function findByNameAndProfessional($name, $professionalId)
    {
        return AppointmentTypes::where('name', $name)
            ->where('professional_id', $professionalId)
            ->first();
    }

    public function createAppointmentType($data, $professionalId, $userId)
    {
        $appointment_types = new AppointmentTypes();
        $appointment_types->unique_id = randomNumber();
        $appointment_types->name = $data['name'];
        $appointment_types->currency = $data['currency'] ?? null;
        $appointment_types->price = $data['price'];
        $appointment_types->duration = $data['duration'];
        $appointment_types->professional_id = $professionalId;
        $appointment_types->added_by = $userId;
        $appointment_types->save();
        return $appointment_types;
    }

    public function getAppointmentTypesForBookingFlow()
    {
        return checkAppointmentTypes();
    }

    public function getAppointmentBookingFlow($uniqueId)
    {
        return AppointmentBookingFlow::where('unique_id', $uniqueId)->first();
    }

    public function findByUniqueId($uniqueId)
    {
        return AppointmentTypes::with('timeDuration')->where('unique_id', $uniqueId)->first();
    }

    public function findByNameAndProfessionalExceptId($name, $professionalId, $exceptId)
    {
        return AppointmentTypes::where('name', $name)
            ->where('professional_id', $professionalId)
            ->where('id', '!=', $exceptId)
            ->first();
    }

    public function updateAppointmentType($object, $data)
    {
        return AppointmentTypes::where('id', $object->id)->update([
            'name' => $data['name'],
            'duration' => $data['duration'],
            'currency' => $data['currency'] ?? null,
            'price' => $data['price'],
        ]);
    }

    public function deleteByUniqueId($uniqueId)
    {
        $appointmentTypes = AppointmentTypes::where('unique_id', $uniqueId)->first();
        if ($appointmentTypes) {
            AppointmentTypes::deleteRecord($appointmentTypes->id);
            return true;
        }
        return false;
    }

    public function deleteMultipleByUniqueIds($uniqueIds)
    {
        $ids = AppointmentTypes::whereIn('unique_id', $uniqueIds)->pluck('id');
        foreach ($ids as $id) {
            AppointmentTypes::deleteRecord($id);
        }
        return true;
    }
} 