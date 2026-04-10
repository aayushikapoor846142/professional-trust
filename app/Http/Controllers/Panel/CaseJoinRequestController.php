<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\CaseJoinRequest;
use App\Models\CaseWithAssociate;
use App\Models\CaseWithProfessionals;
use App\Models\ProfessionalServices;
use App\Models\ProfessionalSubServices;
use App\Models\SubServicesTypes;
use App\MOdels\LeadCase;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentLinkParameter;

class CaseJoinRequestController extends Controller
{
 
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    /**
     * Display the list of Role.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Case Join Request";
        return view('admin-panel.06-roles.case-join-request.lists', $viewData);
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
        $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');

        $records = CaseJoinRequest::where('professional_id',auth()->user()->id)
                ->orderBy($sortColumn, $sortDirection)
                ->paginate();
      
        $viewData['records'] = $records;
        $view = View::make('admin-panel.06-roles.case-join-request.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    public function view($id)
    {
        $caseJoinRequest = CaseJoinRequest::with(['associate','leadCase'])->where('unique_id',$id)->first();
        
        $professionalService =  ProfessionalServices::where("parent_service_id", $caseJoinRequest->leadCase->parent_service_id)->where('service_id',$caseJoinRequest->leadCase->sub_service_id)->where('user_id',auth()->user()->id)->first();

        $professionalSubService = ProfessionalSubServices::with(['subServicesType'])->where('professional_service_id',$professionalService->id)->where('user_id',auth()->user()->id)->get();

        $viewData['professionalSubService'] = $professionalSubService;
        $viewData['pageTitle'] = "View Case Join Request";
        $viewData['caseJoinRequest'] = $caseJoinRequest;
        return view('admin-panel.06-roles.case-join-request.view', $viewData);
    }

    public function acceptModal($id)
    {
        $caseJoinRequest = CaseJoinRequest::with(['associate','leadCase'])->where('unique_id',$id)->first();
        
        $professionalService =  ProfessionalServices::where("parent_service_id", $caseJoinRequest->leadCase->parent_service_id)->where('service_id',$caseJoinRequest->leadCase->sub_service_id)->where('user_id',auth()->user()->id)->first();

        $professionalSubService = ProfessionalSubServices::with(['subServicesType'])->where('professional_service_id',$professionalService->id)->where('user_id',auth()->user()->id)->get()->pluck('sub_services_type_id')->toArray();
       
        $subServiceType = SubServicesTypes::whereIn('id',$professionalSubService)->get();
        $viewData['id'] = $id;
        $viewData['pageTitle'] = 'Accept modal';
        $viewData['professionalSubService'] = $professionalSubService;
        $viewData['subServiceType'] = $subServiceType;
        $view = view(' admin-panel.06-roles.case-join-request.accept-modal', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);

    }

    public function accept(Request $request, $id)
    {
        $caseJoinRequest = CaseJoinRequest::with(['leadCase'])->where('unique_id',$id)->first();
        $leadCase = LeadCase::with(['client'])->where('id',$caseJoinRequest->lead_case_id)->first();
        $leadCase->service_type_id = $request->sub_service_type_id;
        $leadCase->save();

        // $caseJoinRequest->status = 1;
        $caseJoinRequest->save();

        $professionalServices = ProfessionalSubServices::where('user_id', auth()->user()->id)
                    ->where('service_id', $caseJoinRequest->leadCase->sub_service_id)
                    ->where('sub_services_type_id', $request->sub_service_type_id)
                    ->first();

        $caseWithProfessionals = new CaseWithProfessionals();
        $caseWithProfessionals->professional_id = auth()->user()->id;
        $caseWithProfessionals->client_id = $caseJoinRequest->leadCase->client->id;
        $caseWithProfessionals->case_title = $caseJoinRequest->leadCase->case_title;
        $caseWithProfessionals->case_description = $caseJoinRequest->leadCase->case_description;
        $caseWithProfessionals->parent_service_id = $caseJoinRequest->leadCase->parent_service_id;
        $caseWithProfessionals->sub_service_id = $caseJoinRequest->leadCase->sub_service_id;
        $caseWithProfessionals->service_type_id = $request->sub_service_type_id;
        $caseWithProfessionals->status = 'draft';
        $caseWithProfessionals->added_by = auth()->user()->id;
        $caseWithProfessionals->is_associate_case = 1;
        $caseWithProfessionals->save();

        $caseWithAssociate = new CaseWithAssociate();
        $caseWithAssociate->associate_id = $caseJoinRequest->associate_id;
        $caseWithAssociate->case_id = 0;
        $caseWithAssociate->professional_id = auth()->user()->id;
        $caseWithAssociate->lead_case_id = $caseJoinRequest->lead_case_id;
        $caseWithAssociate->client_id = $caseWithProfessionals->client_id;
        $caseWithAssociate->save();

        // Create Invoice
        $invoice = new Invoice();
        $invoice->user_id = $leadCase->client->id;
        $invoice->first_name = $leadCase->client->first_name;
        $invoice->last_name = $leadCase->client->last_name;
        $invoice->email = $leadCase->client->email;
        $invoice->country_code = $leadCase->client->country_code;
        $invoice->phone_no = $leadCase->client->phone_no;
   
        // $invoice->address = $request->input("address");
        // $invoice->city = $request->input("city");
        // $invoice->state = $request->input("state");
        // $invoice->zip = $request->input("zip");
        // $invoice->country = $request->input(key: "country");

        // $invoice->b_address = $request->input("billing_address");
        // $invoice->b_city = $request->input("billing_city");
        // $invoice->b_state = $request->input("billing_state");
        // $invoice->b_zip = $request->input("billing_zip");
        // $invoice->b_country = $request->input(key: "billing_country");

        $invoice->currency = getCurrency();
        $invoice->tax = 0;
        $invoice->sub_total = $professionalServices->consultancy_fees;
        $invoice->total_amount = $professionalServices->consultancy_fees;
        $invoice->payment_status = 'pending';
        $invoice->invoice_type = 'professional-case';
        $invoice->reference_id = $caseWithProfessionals->id ?? 0;
          $invoice->added_by = auth()->user()->id;
        $invoice->save();

        $invoice_id = $invoice->id;
        $invoice_item = new InvoiceItem();
        $invoice_item->invoice_id = $invoice_id;
        $invoice_item->particular = "Amount paid for Professional Case<b>TrustVisory</b>";
        $invoice_item->amount = $professionalServices;
        $invoice_item->save();  
                

        $token = randomString();
        $params = [
            'user_id' => \Crypt::encryptString($invoice->user_id),
            'token' => \Crypt::encryptString($token),
            'invoice_id' => \Crypt::encryptString($invoice->id),
            'transaction_id' => \Crypt::encryptString(0),
        ];

        $paymentLinkParam = new \App\Models\PaymentLinkParameter;
        $paymentLinkParam->user_id = encryptVal($invoice->user_id);
        $paymentLinkParam->token = encryptVal($token);
        $paymentLinkParam->invoice_id = encryptVal($invoice->id);
        $paymentLinkParam->transaction_id = encryptVal(0);
        $paymentLinkParam->added_by = auth()->user()->id;
        $paymentLinkParam->signature = $this->generateSignature($params);
        $paymentLinkParam->save();

        // $professionalServices->consultancy_fees;

        // return globalInvoiceUrl($invoice->unique_id);
       
        // Return JSON response for AJAX requests instead of redirect
        return response()->json([
            'status' => true,
            'message' => 'Case posted successfully'
        ]);
    }

    private function generateSignature($params)
    {
        ksort($params);
        $string = http_build_query($params);
        return hash_hmac('sha256', $string, apiKeys('STRIPE_SECRET'));
    }

    public function reject($id)
    {
        $caseJoinRequest = CaseJoinRequest::with(['leadCase'])->where('unique_id',$id)->first();
        $caseJoinRequest->status = 2;
        $caseJoinRequest->save();

        return redirect()->back()->with("success", "REquest rejected successfully");
    }
}
