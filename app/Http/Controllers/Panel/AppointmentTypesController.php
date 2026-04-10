<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AppointmentTypesService;
use Illuminate\Support\Facades\Validator;
use View;

class AppointmentTypesController extends Controller
{
    protected $appointmentTypesService;

    public function __construct(AppointmentTypesService $appointmentTypesService)
    {
        $this->appointmentTypesService = $appointmentTypesService;
    }

    /**
     * Display the list of Role.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Appointment Types";
        $professionalId = auth()->user()->getRelatedProfessionalId();
        $viewData['durations'] = $this->appointmentTypesService->getDurations($professionalId, auth()->user()->id);

        $view = view('admin-panel.03-appointments.appointment-system.appointment-types.lists', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    /**
     * Get the list of Country with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $professionalId = auth()->user()->getRelatedProfessionalId();
        $search = $request->input("search");
        $records = $this->appointmentTypesService->getAppointmentTypesList($search, $professionalId, auth()->user()->id);

        $viewData['records'] = $records;
        $view = View::make('admin-panel.03-appointments.appointment-system.appointment-types.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    /**
     * Show the form for creating a new roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $professionalId = auth()->user()->getRelatedProfessionalId();
        $viewData['pageTitle'] = "Add Appointment Types";
        $viewData['durations'] = $this->appointmentTypesService->getDurations($professionalId, auth()->user()->id);

        $view = view('admin-panel.03-appointments.appointment-system.appointment-types.add', $viewData);
        $contents = $view->render();

        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    /**
     * Store a newly created role in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'duration' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = array();

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['error_type'] = 'validation';
            $response['message'] = $errMsg;
            return response()->json($response);
        }
        $professionalId = auth()->user()->getRelatedProfessionalId();
        $userId = auth()->user()->id;
        $appointmentTypes = $this->appointmentTypesService->findByNameAndProfessional($request->name, $professionalId);
        if (!empty($appointmentTypes)) {
            $response['status'] = false;
            $response['error_type'] = 'unique_name';
            $response['message'] = 'Name is already added.';
            return response()->json($response);
        }

        $appointment_types = $this->appointmentTypesService->createAppointmentType($request->all(), $professionalId, $userId);
        $appointmentTypes = $this->appointmentTypesService->getAppointmentTypesForBookingFlow();

        $appointmentBookingFlow = $this->appointmentTypesService->getAppointmentBookingFlow($request->duration_booking_flow_id);

        $html = view('admin-panel.03-appointments.appointment-system.appointment-booking-flow.components.appointment-type.select', [
            'appointmentTypes' => $appointmentTypes,
            'selected' => $appointmentBookingFlow ? $appointmentBookingFlow->appointment_type_id : ''
        ])->render();

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('appointment-types');
        $response['message'] = "Record added successfully";
        $response['html'] = $html;

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified roles.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $record = $this->appointmentTypesService->findByUniqueId($id);
        if (!$record) {
            return response()->json([
                'status' => false,
                'error_type' => 'not_found',
                'message' => 'Record not found.'
            ]);
        }
        $professionalId = auth()->user()->getRelatedProfessionalId();
        $viewData['record'] = $record;
        $viewData['durations'] = $this->appointmentTypesService->getDurations($professionalId, auth()->user()->id);
        $viewData['pageTitle'] = "Edit Appointment Type";
        $view = view('admin-panel.03-appointments.appointment-system.appointment-types.edit', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    /**
     * Update the specified country in the database.
     *
     * @param string $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $object = $this->appointmentTypesService->findByUniqueId($id);
        if (!$object) {
            return response()->json([
                'status' => false,
                'error_type' => 'not_found',
                'message' => 'Record not found.'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'duration' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = array();

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }
        $professionalId = auth()->user()->getRelatedProfessionalId();

        $appointmentTypes = $this->appointmentTypesService->findByNameAndProfessionalExceptId($request->name, $professionalId, $object->id);
        if (!empty($appointmentTypes)) {
            $response['status'] = false;
            $response['error_type'] = 'unique_name';
            $response['message'] = 'Name is already added.';
            return response()->json($response);
        }

        $this->appointmentTypesService->updateAppointmentType($object, $request->all());

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('appointment-types');
        $response['message'] = "Record updated successfully";

        return response()->json($response);
    }

    /**
     * Remove the specified country from the database.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSingle($id)
    {
        $object = $this->appointmentTypesService->findByUniqueId($id);
        if (!$object) {
            return redirect()->back()->with('error', 'Record not found.');
        }
        $this->appointmentTypesService->deleteByUniqueId($id);
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    /**
     * Remove multiple Country from the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMultiple(Request $request)
    {
        $ids = explode(",", $request->input("ids"));
        $this->appointmentTypesService->deleteMultipleByUniqueIds($ids);
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }
}
