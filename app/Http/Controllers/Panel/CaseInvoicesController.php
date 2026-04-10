<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\AdvisoryType;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Stripe\Stripe;
use Stripe\PaymentLink;
use Stripe\Checkout\Session;
use Razorpay\Api\Api;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Carbon\Carbon;
use App\Models\InvoicePaymentLink;
use App\Models\SupportByUser;
use Stripe\Subscription;
use Stripe\PaymentMethod;
use App\Models\AutoLoginToken;
use App\Models\SubscriptionInvoiceHistory;
use App\Models\StripeErrorLogs;
use Illuminate\Support\Facades\Log;
use App\Models\UserSubscriptionHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentTransaction;
use App\Models\PointEarn;
use App\Models\CompanyLocations;
use App\Models\CaseWithProfessionals;
use Illuminate\Support\Facades\Hash;
use App\Models\PaymentLinkParameter;
use Illuminate\Support\Facades\Crypt;
use App\Services\CaseInvoiceService;

class CaseInvoicesController extends Controller
{

    protected $caseInvoiceService;

    public function __construct(CaseInvoiceService $caseInvoiceService)
    {
        $this->caseInvoiceService = $caseInvoiceService;
    }

    /**
     * Display the list of Action.
     *
     * @return \Illuminate\View\View
     */
    public function index($case_id)
    {
        $viewData['pageTitle'] = "Case Invoice";
        $viewData['case_id'] = $case_id;
        return view('admin-panel.08-cases.case-with-professionals.invoices.lists', $viewData);
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
        

        $case = CaseWithProfessionals::where('unique_id',$request->case_id)->first();

        $records = Invoice::with(['invoicePaymentLink'])->where(function ($query) use ($search) {
            if ($search != '') {
                $query->where("first_name", "LIKE", "%" . $search . "%");
                $query->orWhere("last_name", "LIKE", "%" . $search . "%");
                $query->orWhere("email", "LIKE", "%" . $search . "%");
                $query->orWhere("invoice_number", "LIKE", "%" . $search . "%");
            }
        })
        ->where("invoice_type","post_case")
        ->where('added_by',auth()->id())
        ->where('reference_id',$case->id)
        ->orderBy($sortColumn, $sortDirection)
        ->paginate();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.08-cases.case-with-professionals.invoices.ajax-list', $viewData);
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
    public function add($case_id)
    {
        $viewData['pageTitle'] = "Add Invoice";

        $viewData['case_id'] = $case_id;
        $case = CaseWithProfessionals::with(['clients'])->where('unique_id',$case_id)->first();
        $viewData['case'] = $case;
        return view('admin-panel.08-cases.case-with-professionals.invoices.add', $viewData);
    }

    /**
     * Store a newly created action in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request,$case_id)
    {
        $request->merge([
            'case_id' => $case_id
        ]);
        $response = $this->caseInvoiceService->saveInvoice($request);
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified action.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $record = Invoice::where('unique_id',$id)->first();

      
        $case = CaseWithProfessionals::where('id',$record->reference_id)->first();

        $viewData['case_id'] = $case->unique_id;
        $viewData['case'] = $case;
        $viewData['record'] = $record;
        $viewData['pageTitle'] = "Edit Invoice";
        return view('admin-panel.08-cases.case-with-professionals.invoices.edit', $viewData);
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
      
        $response = $this->caseInvoiceService->updateInvoice($id, $request);
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
        $invoice = Invoice::where('unique_id',$id)->first();
        Invoice::deleteRecord($invoice->id);
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    /**
     * Remove multiple Country from the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   
    public function downloadInvoicePDF($invoice_id)
    {
        return $this->caseInvoiceService->downloadInvoicePDF($invoice_id);
    }


    public function copyLink($id)
    {
        // $invoices = InvoicePaymentLink::where('invoice_id',$id)->first();

        $invoices = Invoice::where('unique_id',$id)->first();

        $invoiceId = $invoices->id;

        $matching = PaymentLinkParameter::get()->first(function ($record) use ($invoiceId) {
            return Crypt::decrypt($record->invoice_id) == $invoiceId;
        });

        if (!empty($matching)) {
            $url = clientTrustvisoryUrl() . '/payment-transaction/' . $matching->invoice_id .'/' .urlencode($matching->transaction_id).
            '?utk=' . urlencode($matching->token) . 
            '&uid=' . urlencode($matching->user_id);

            $viewData['invoices'] = $invoices;
            $viewData['url'] = $url;
            $viewData['unique_id'] = $id;
            $viewData['pageTitle'] = 'Copy Link';
            $view = View::make('admin-panel.09-utilities.invoices.copy-link', $viewData);
            $contents = $view->render();
            
            $response['status'] = true;
            $response['contents'] = $contents;
            return response()->json($response);

        } else {
            $response['status'] = false;
            $response['message'] = 'Url not found';
            return response()->json($response);
        }
        // $token = randomString();
        // $paymentLinkParam = new PaymentLinkParameter;
        // $paymentLinkParam->user_id = encryptVal($invoices->user_id);
        // $paymentLinkParam->token = encryptVal($token);
        // $paymentLinkParam->invoice_id = encryptVal($invoices->id);
        // $paymentLinkParam->transaction_id = encryptVal($invoices->transaction_id);
        // $paymentLinkParam->added_by = auth()->user()->id;
        // $paymentLinkParam->save();

        $user = User::where('id',$invoices->user_id)->first();
        $token = "";
        if($user->token == ""){
            $user->token = randomString();
            $user->save();
        }
        $token = $user->token;
        $url = baseUrl('invoices/pay/' . encryptVal($invoices->id)) . 
        '?utk=' . encryptVal($token) . 
        '&rtk=' . encryptVal(randomString());

    //    $url = clientTrustvisoryUrl() . '/invoices/pay/' . $paymentLinkParam->invoice_id . 
    //         '?utk=' . urlencode($paymentLinkParam->token) . 
    //         '&tid=' . urlencode($paymentLinkParam->transaction_id) . 
    //         '&uid=' . urlencode($paymentLinkParam->user_id);

        
        // $url = clientTrustvisoryUrl() . '/payment-transaction/' . $paymentLinkParam->invoice_id .'/' .urlencode($paymentLinkParam->transaction_id).
        //     '?utk=' . urlencode($paymentLinkParam->token) . 
        //     '&tid=' . urlencode($paymentLinkParam->transaction_id) . 
        //     '&uid=' . urlencode($paymentLinkParam->user_id);

        $viewData['invoices'] = $invoices;
        $viewData['url'] = $url;
        $viewData['unique_id'] = $id;
        $viewData['pageTitle'] = 'Copy Link';
        $view = View::make('admin-panel.08-cases.case-with-professionals.invoices.copy-link', $viewData);
        $contents = $view->render();
        
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);

    }

    
}
