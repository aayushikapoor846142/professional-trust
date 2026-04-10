<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TimeController extends Controller
{
    /**
     * Convert UTC time to local timezone
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertTime(Request $request)
    {
        // Validate input parameters
        $validator = Validator::make($request->all(), [
            'utcTime' => 'required|string',
            'timezone' => 'required|string|timezone',
        ], [
            'utcTime.required' => 'UTC time is required',
            'utcTime.string' => 'UTC time must be a string',
            'timezone.required' => 'Timezone is required',
            'timezone.string' => 'Timezone must be a string',
            'timezone.timezone' => 'Invalid timezone provided',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $utcTime = $request->input('utcTime');    // UTC time from the request
            $timezone = $request->input('timezone');  // Timezone from the request
         
            // Validate UTC time format
            if (!Carbon::canParse($utcTime)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid UTC time format provided',
                ], 422);
            }

            // Convert the UTC time to the user's timezone using Carbon
            $localTime = Carbon::parse($utcTime)->setTimezone($timezone)->format('h:i A'); // Convert to time with AM/PM

            return response()->json([
                'status' => true,
                'localTime' => $localTime,  // Send the local time back to the client
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error converting time: ' . $e->getMessage(),
            ], 500);
        }
    }
}
