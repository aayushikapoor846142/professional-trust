<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\WorkingHours;
use App\Models\CompanyLocations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkingHourBreak;

class WorkingHoursController extends Controller
{
    /**
     * Display the working hours management page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData = $this->prepareViewData();
        return view('admin-panel.03-appointments.appointment-system.working-hours.edit', $viewData);
    }

    /**
     * Update working hours for the authenticated professional.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            // Validate request
            $validator = $this->validateWorkingHoursRequest($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Process working hours update
            $this->processWorkingHoursUpdate($request);

            return response()->json([
                'status' => true,
                'redirect_back' => baseUrl('working-hours'),
                'message' => "Record Saved successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while saving working hours'
            ], 500);
        }
    }

    /**
     * Prepare view data for the working hours page.
     *
     * @return array
     */
    private function prepareViewData()
    {
        $professionalId = Auth::id();
        $getLocId = CompanyLocations::where('unique_id', request()->get('location'))->value('id');
        
        // Get timezones
        $timezones = collect(\DateTimeZone::listIdentifiers())->map(function ($timezone) {
            return ["label" => $timezone, "value" => $timezone];
        })->toArray();

        // Get company locations
        $companyLocations = CompanyLocations::where('user_id', $professionalId)
            ->where('type_label', 'company')
            ->get();

        // Get working hours schedules
        $schedules = WorkingHours::where('professional_id', $professionalId)
            ->where('location_id', $getLocId)
            ->with('breaks')
            ->get();

        // Prepare records array
        $records = $this->prepareRecordsArray($schedules);

        return [
            'pageTitle' => "Working Hours",
            'getLocId' => $getLocId,
            'timezones' => $timezones,
            'getSelectedTimezone' => CompanyLocations::where('id', $getLocId)->value('timezone'),
            'companyLocations' => $companyLocations,
            'professional_id' => $professionalId,
            'load_type' => 'page',
            'records' => $records
        ];
    }

    /**
     * Prepare records array from working hours schedules.
     *
     * @param \Illuminate\Database\Eloquent\Collection $schedules
     * @return array
     */
    private function prepareRecordsArray($schedules)
    {
        $records = [];
        
        foreach ($schedules as $schedule) {
            $breaks = $schedule->breaks->map(function ($break) {
                return [
                    'start' => $break->break_start,
                    'end' => $break->break_end,
                ];
            })->toArray();
        
            $records[$schedule->day] = [
                'from' => $schedule->from_time,
                'to' => $schedule->to_time,
                'no_break_time' => $schedule->no_break_time,
                'breaks' => $breaks,
            ];
        }
        
        return $records;
    }

    /**
     * Validate working hours request.
     *
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    private function validateWorkingHoursRequest(Request $request)
    {
        $rules = [
            'location_id' => 'required|integer',
            'timezone' => 'required'
        ];

        $messages = [
            'location_id.required' => 'Location is required',
            'timezone.required' => 'Timezone is required',
            'location_id.integer' => 'Location must be a valid ID',
        ];

        // Add validation rules for each schedule
        foreach ($request->input('schedule', []) as $index => $daySchedule) {
            if (isset($daySchedule['day'])) {
                $rules["schedule.$index.day"] = 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday';
                $rules["schedule.$index.from"] = 'required';
                $rules["schedule.$index.to"] = 'required|after:schedule.' . $index . '.from';

                if (empty($daySchedule['no_break_time'])) {
                    if (isset($daySchedule['breaks']) && is_array($daySchedule['breaks'])) {
                        foreach ($daySchedule['breaks'] as $breakIndex => $break) {
                            $rules["schedule.$index.breaks.$breakIndex.start"] = 'required';
                            $rules["schedule.$index.breaks.$breakIndex.end"] = 'required|after:schedule.' . $index . '.breaks.' . $breakIndex . '.start';
                            
                            if ($breakIndex > 0) {
                                $rules["schedule.$index.breaks.$breakIndex.start"] .= '|after:schedule.' . $index . '.breaks.' . ($breakIndex - 1) . '.end';
                            }
                        }
                    }
                }
            }
        }

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Process working hours update.
     *
     * @param Request $request
     * @return void
     */
    private function processWorkingHoursUpdate(Request $request)
    {
        $schedules = $request->input("schedule", []);
        $getLocId = $request->location_id;
        $activeDays = [];

        DB::transaction(function () use ($schedules, $getLocId, &$activeDays) {
            foreach ($schedules as $schedule) {
                if (isset($schedule['day'])) {
                    $this->updateWorkingHourSchedule($schedule, $getLocId);
                    $activeDays[] = $schedule['day'];
                }
            }

            // Update company location timezone
            $this->updateCompanyLocationTimezone($getLocId, request('timezone'));

            // Delete inactive days
            $this->deleteInactiveDays($activeDays);
        });
    }

    /**
     * Update individual working hour schedule.
     *
     * @param array $schedule
     * @param int $locationId
     * @return void
     */
    private function updateWorkingHourSchedule($schedule, $locationId)
    {
        $existingSchedule = WorkingHours::where('day', $schedule['day'])
            ->where('professional_id', Auth::id())
            ->where('location_id', $locationId)
            ->first();

        $workingHour = $existingSchedule ?? new WorkingHours();
        $workingHour->unique_id = $existingSchedule ? $existingSchedule->unique_id : randomNumber();
        $workingHour->professional_id = Auth::id();
        $workingHour->location_id = $locationId;
        $workingHour->no_break_time = isset($schedule['no_break_time']) ? 1 : 0;
        $workingHour->day = $schedule['day'];
        $workingHour->from_time = $schedule['from'];
        $workingHour->to_time = $schedule['to'];
        $workingHour->save();

        // Handle breaks
        $this->updateWorkingHourBreaks($workingHour, $schedule);
    }

    /**
     * Update working hour breaks.
     *
     * @param WorkingHours $workingHour
     * @param array $schedule
     * @return void
     */
    private function updateWorkingHourBreaks($workingHour, $schedule)
    {
        if (empty($schedule['no_break_time']) && isset($schedule['breaks']) && is_array($schedule['breaks'])) {
            // Delete existing breaks
            WorkingHourBreak::where('working_hours_id', $workingHour->id)->delete();

            // Create new breaks
            foreach ($schedule['breaks'] as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    $this->createWorkingHourBreak($workingHour->id, $break);
                }
            }
        }
    }

    /**
     * Create working hour break.
     *
     * @param int $workingHourId
     * @param array $break
     * @return void
     */
    private function createWorkingHourBreak($workingHourId, $break)
    {
        $existingBreak = WorkingHourBreak::where([
            'break_start' => $break['start'],
            'working_hours_id' => $workingHourId,
            'break_end' => $break['end']
        ])->first();

        if (!$existingBreak) {
            WorkingHourBreak::create([
                'working_hours_id' => $workingHourId,
                'break_start' => $break['start'],
                'break_end' => $break['end']
            ]);
        }
    }

    /**
     * Update company location timezone.
     *
     * @param int $locationId
     * @param string $timezone
     * @return void
     */
    private function updateCompanyLocationTimezone($locationId, $timezone)
    {
        $companyLocation = CompanyLocations::find($locationId);
        if ($companyLocation) {
            $companyLocation->timezone = $timezone;
            $companyLocation->save();
        }
    }

    /**
     * Delete inactive working hour days.
     *
     * @param array $activeDays
     * @return void
     */
    private function deleteInactiveDays($activeDays)
    {
        WorkingHours::whereNotIn('day', $activeDays)
            ->where('professional_id', Auth::id())
            ->delete();
    }
}
