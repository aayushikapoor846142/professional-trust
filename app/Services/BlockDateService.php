<?php

namespace App\Services;

use App\Models\ProfessionalLeave;
use App\Models\WorkingHours;
use Carbon\Carbon;

class BlockDateService
{
    /**
     * Create professional leaves for given dates and location.
     *
     * @param int $location_id
     * @param int $professional_id
     * @param array $dates
     * @param string $reason
     * @return void
     */
    public function createLeaves($location_id, $professional_id, array $dates, $reason)
    {   
        $dates = array_map('trim', $dates);
        $existing = ProfessionalLeave::where('location_id', $location_id)
            ->whereIn('leave_date', $dates)
            ->pluck('leave_date')
            ->toArray();
  
        $workingHoursByDay = WorkingHours::where('location_id', $location_id)
            ->get()
            ->keyBy(function ($item) {
                return strtolower($item->day);
            });

        $toInsert = [];
        foreach ($dates as $date) {
            
            if (in_array($date, $existing)) {
                continue;
            }
           
            $day = strtolower(Carbon::parse($date)->format('l'));

            if (!isset($workingHoursByDay[$day])) {
                continue;
            }
            $workingHours = $workingHoursByDay[$day];
            $toInsert[] = [
                'professional_id' => $professional_id,
                'location_id' => $location_id,
                'leave_date' => $date,
                'start_time' => $workingHours->from_time,
                'end_time' => $workingHours->to_time,
                'reason' => $reason,
                'unique_id' => randomNumber(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
              
        }
         
        if (!empty($toInsert)) {
            ProfessionalLeave::insert($toInsert);
        }
    }

    public function updateLeave($uid, $location_id, $professional_id, $date, $reason)
    {
        $workingHours = $this->getWorkingHours($location_id, $date);
        if ($workingHours) {
            $profLeaves = $this->getProfessionalLeaveByUniqueId($uid);
            if ($profLeaves) {
                $profLeaves->professional_id = $professional_id;
                $profLeaves->location_id = $location_id;
                $profLeaves->leave_date = $date;
                $profLeaves->start_time = $workingHours->from_time;
                $profLeaves->end_time = $workingHours->to_time;
                $profLeaves->reason = $reason;
                $profLeaves->save();
                return ['status' => true, 'message' => 'Leaves marked Successfully'];
            }
        }
        return ['status' => false, 'message' => 'You have selected a day off.'];
    }

    public function deleteLeave($unique_id)
    {
        $data = $this->getProfessionalLeaveByUniqueId($unique_id);
        if ($data) {
            ProfessionalLeave::deleteRecord($data->id);
            return true;
        }
        return false;
    }

    public function deleteMultipleLeaves(array $unique_ids)
    {
        $ids = ProfessionalLeave::whereIn('unique_id', $unique_ids)->pluck('id')->toArray();
        if (!empty($ids)) {
            ProfessionalLeave::whereIn('id', $ids)->delete();
        }
        return true;
    }

    public function getLocationLeavesData($location_id)
    {
        $leave_dates = ProfessionalLeave::where('location_id', $location_id)
            ->pluck('leave_date')
            ->toArray();
        $workingHours = WorkingHours::where('location_id', $location_id)->pluck('day');
        $disabled_days = $this->getDisabledDays($workingHours);
        $days = !empty($workingHours) ? $workingHours->toArray() : [];
        return [
            'status' => true,
            'disabled_days' => $disabled_days,
            'days' => $days,
            'leave_dates' => $leave_dates,
        ];
    }

    // Private helper methods
    private function getWorkingHours($location_id, $date)
    {
        return WorkingHours::where('location_id', $location_id)
            ->where('day', strtolower(Carbon::parse(trim($date))->format('l')))
            ->first();
    }

    private function getProfessionalLeaveByUniqueId($unique_id)
    {
        return ProfessionalLeave::where('unique_id', $unique_id)->first();
    }

    private function getDisabledDays($workingDays)
    {
        $weekdays = [];
        for ($i = 0; $i < 7; $i++) {
            $weekdays[] = strtolower(Carbon::now()->startOfWeek()->addDays($i)->format('l'));
        }
        $days = !empty($workingDays) ? $workingDays->toArray() : [];
        $disabled_days = array_values(array_diff($weekdays, $days));
        foreach ($disabled_days as $key => $day) {
            $disabled_days[$key] = ucfirst($day);
        }
        return $disabled_days;
    }
} 