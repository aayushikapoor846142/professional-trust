<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeDuration;
use App\Services\TimeDurationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use View;
use App\Models\AppointmentBookingFlow;

class TimeDurationController extends Controller
{
    protected $timeDurationService;

    public function __construct(TimeDurationService $timeDurationService)
    {
        $this->timeDurationService = $timeDurationService;
    }

    /**
     * Display the list of Time Duration.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $viewData['pageTitle'] = "Time Duration";
            $view = view('admin-panel.03-appointments.appointment-system.time-duration.lists', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['status'] = true;
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('TimeDuration index error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while loading the page.'
            ]);
        }
    }

    /**
     * Get the list of Time Duration with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        try {
            $professionalId = auth()->user()->getRelatedProfessionalId();
            $search = $request->input("search");
            
            $records = $this->timeDurationService->getTimeDurationsForProfessional(
                $professionalId, 
                auth()->user()->id, 
                $search
            );
          
            $viewData['records'] = $records;
            $view = View::make('admin-panel.03-appointments.appointment-system.time-duration.ajax-list', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['last_page'] = $records->lastPage();
            $response['current_page'] = $records->currentPage();
            $response['total_records'] = $records->total();
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('TimeDuration getAjaxList error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while loading the data.'
            ]);
        }
    }

    /**
     * Show the form for creating a new time duration.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        try {
            $viewData['pageTitle'] = "Add Time Duration";
            $view = view('admin-panel.03-appointments.appointment-system.time-duration.add', $viewData);
            $contents = $view->render();
            $response['status'] = true;
            $response['contents'] = $contents;
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('TimeDuration add error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while loading the form.'
            ]);
        }
    }

    /**
     * Store a newly created time duration in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        try {
            $validator = $this->timeDurationService->validateTimeDuration($request->all());

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error_type' => 'validation',
                    'message' => $this->timeDurationService->formatValidationErrors($validator)
                ]);
            }

            $professionalId = auth()->user()->getRelatedProfessionalId();

            // Check for duplicate name
            if ($this->timeDurationService->nameExists($request->name, $professionalId)) {
                return response()->json([
                    'status' => false,
                    'error_type' => 'unique_name',
                    'message' => 'Name is already added.'
                ]);
            }

            DB::beginTransaction();

            $this->timeDurationService->createTimeDuration(
                $request->all(),
                $professionalId,
                auth()->user()->id
            );

            $bookingFlowData = $this->timeDurationService->getTimeDurationsForBookingFlow(
                $professionalId,
                auth()->user()->id,
                $request->duration_booking_flow_id
            );

            $html = view('admin-panel.03-appointments.appointment-system.appointment-booking-flow.components.time-duration.select', [
                'durations' => $bookingFlowData['durations'],
                'selected' => $bookingFlowData['selected']
            ])->render();

            DB::commit();

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('time-duration');
            $response['message'] = "Record added successfully";
            $response['html'] = $html;
            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TimeDuration save error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while saving the record.'
            ]);
        }
    }

    /**
     * Show the form for editing the specified time duration.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $record = $this->timeDurationService->getByUniqueId($id);
            
            if (!$record) {
                return response()->json([
                    'status' => false,
                    'message' => 'Time duration not found.'
                ]);
            }

            // Authorization check - ensure user can edit this record
            $professionalId = auth()->user()->getRelatedProfessionalId();
            if (!$this->timeDurationService->canEditRecord($record, $professionalId)) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to edit this record.'
                ]);
            }

            $viewData['record'] = $record;
            $viewData['pageTitle'] = "Edit Time Duration";
            $view = view('admin-panel.03-appointments.appointment-system.time-duration.edit', $viewData);
            $contents = $view->render();
            $response['status'] = true;
            $response['contents'] = $contents;
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('TimeDuration edit error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while loading the edit form.'
            ]);
        }
    }

    /**
     * Update the specified time duration in the database.
     *
     * @param string $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        try {
            $object = $this->timeDurationService->getByUniqueId($id);
            
            if (!$object) {
                return response()->json([
                    'status' => false,
                    'message' => 'Time duration not found.'
                ]);
            }

            // Authorization check
            $professionalId = auth()->user()->getRelatedProfessionalId();
            if (!$this->timeDurationService->canEditRecord($object, $professionalId)) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to update this record.'
                ]);
            }

            $validator = $this->timeDurationService->validateTimeDuration($request->all());

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $this->timeDurationService->formatValidationErrors($validator)
                ]);
            }

            // Check for duplicate name (excluding current record)
            if ($this->timeDurationService->nameExists($request->name, $professionalId, $object->id)) {
                return response()->json([
                    'status' => false,
                    'error_type' => 'unique_name',
                    'message' => 'Name is already added.'
                ]);
            }

            DB::beginTransaction();

            $this->timeDurationService->updateTimeDuration($object, $request->all());

            DB::commit();

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('time-duration');
            $response['message'] = "Record updated successfully";
            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TimeDuration update error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the record.'
            ]);
        }
    }

    /**
     * Remove the specified time duration from the database.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSingle($id)
    {
        try {
            $timeDuration = $this->timeDurationService->getByUniqueId($id);
            
            if (!$timeDuration) {
                return redirect()->back()->with("error", "Time duration not found.");
            }

            // Authorization check
            $professionalId = auth()->user()->getRelatedProfessionalId();
            if (!$this->timeDurationService->canEditRecord($timeDuration, $professionalId)) {
                return redirect()->back()->with("error", "You are not authorized to delete this record.");
            }

            // Check if time duration is being used
            if ($this->timeDurationService->isTimeDurationInUse($timeDuration->id)) {
                return redirect()->back()->with("error", "Cannot delete time duration as it is currently in use.");
            }

            if ($this->timeDurationService->deleteTimeDuration($timeDuration)) {
                return redirect()->back()->with("success", "Record deleted successfully");
            } else {
                return redirect()->back()->with("error", "Failed to delete the record.");
            }
        } catch (\Exception $e) {
            Log::error('TimeDuration deleteSingle error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with("error", "An error occurred while deleting the record.");
        }
    }

    /**
     * Remove multiple time durations from the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMultiple(Request $request)
    {
        try {
            $ids = explode(",", $request->input("ids"));
            $professionalId = auth()->user()->getRelatedProfessionalId();
            
            $result = $this->timeDurationService->bulkDelete($ids, $professionalId);

            $response['status'] = true;
            
            if ($result['deleted_count'] > 0) {
                \Session::flash('success', "{$result['deleted_count']} record(s) deleted successfully");
            }
            
            if ($result['error_count'] > 0) {
                \Session::flash('warning', "{$result['error_count']} record(s) could not be deleted (in use or unauthorized)");
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('TimeDuration deleteMultiple error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting the records.'
            ]);
        }
    }
}
