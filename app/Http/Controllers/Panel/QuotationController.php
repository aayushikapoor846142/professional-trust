<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\ProfessionalServices;
use App\Services\QuotationService;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    protected $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    /**
     * Display the list of Action.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Quotations";
        return view('admin-panel.09-utilities.quotations.lists', $viewData);
    }

    /**
     * Get the list of Country with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");
        $records = Quotation::where(function ($query) use ($search) {
                if ($search != '') {
                    $query->where("quotation_title", "LIKE", "%" . $search . "%");
                }
            })->with(['userAdded'])
            ->orderBy('id', "desc")
            ->paginate();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.09-utilities.quotations.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    /**
     * Show the form for creating a new action.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $viewData['pageTitle'] = "Add Quotation";
        $services = ProfessionalServices::whereHas('subServices')->where("user_id",auth()->user()->id)->get();
        $professional_services = array();
        foreach($services as $service){
            $temp = $service->subServices;
            $professional_services[] =  $temp;
        }
        $viewData['services'] = $professional_services;
        return view('admin-panel.09-utilities.quotations.add', $viewData);
    }

    /**
     * Store a newly created action in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());
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
            $quotation = $this->quotationService->createQuotation($request->all(), auth()->user()->id);
            $response['status'] = true;
            $response['redirect_back'] = baseUrl('quotations');
            $response['message'] = "Record added successfully";
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Quotation Save Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified action.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $viewData['record'] = Quotation::where('unique_id',$id)->first();
        $services = ProfessionalServices::whereHas('subServices')->where("user_id",auth()->user()->id)->get();
        $professional_services = array();
        foreach($services as $service){
            $temp = $service->subServices;
            $professional_services[] =  $temp;
        }
        $viewData['services'] = $professional_services;
        $viewData['pageTitle'] = "Edit Quotation";
        return view('admin-panel.09-utilities.quotations.edit', $viewData);
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
        try {
            $validator = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());
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
            $quotation = $this->quotationService->updateQuotation($id, $request->all());
            if (!$quotation) {
                return response()->json(['status' => false, 'message' => 'Quotation not found']);
            }
            $response['status'] = true;
            $response['redirect_back'] = baseUrl('quotations');
            $response['message'] = "Record updated successfully";
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Quotation Update Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified country from the database.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSingle($id)
    {
        try {
            $deleted = $this->quotationService->deleteQuotation($id);
            if (!$deleted) {
                return redirect()->back()->with("error", "Quotation not found");
            }
            return redirect()->back()->with("success", "Record deleted successfully");
        } catch (\Exception $e) {
            Log::error('Quotation Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    /**
     * Remove multiple Country from the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMultiple(Request $request)
    {
        try {
            $ids = explode(",", $request->input("ids"));
            foreach ($ids as $uniqueId) {
                $this->quotationService->deleteQuotation($uniqueId);
            }
            $response['status'] = true;
            \Session::flash('success', 'Records deleted successfully');
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Quotation Bulk Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getValidationRules(): array
    {
        return [
            'service_id' => 'required',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.amount' => 'required|numeric|min:1',
            'currency' => 'required',
            'total_amount' => 'required|numeric|min:1',
            'quotation_title' => 'required|string|max:255',
        ];
    }

    private function getValidationMessages(): array
    {
        return [
            'items.required' => 'At least one item is required.',
            'items.array' => 'Invalid item format.',
            'items.min' => 'You must add at least one item.',
            'items.*.name.required' => 'The item name is required.',
            'items.*.name.string' => 'The item name must be a valid text.',
            'items.*.name.max' => 'The item name should not exceed 255 characters.',
            'items.*.amount.required' => 'The item amount is required.',
            'items.*.amount.numeric' => 'The item amount must be a number.',
            'items.*.amount.min' => 'The item amount must be at least 1.',
            'quotation_title.required' => 'The quotation title is required.',
            'quotation_title.string' => 'The quotation title must be a valid text.',
            'quotation_title.max' => 'The quotation title should not exceed 255 characters.',
        ];
    }
}
