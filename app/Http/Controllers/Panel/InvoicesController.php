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
use App\Services\InvoiceService;


class InvoicesController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display the list of Action.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Global Invoice";
        $viewData['paymentStatus'] = $statuses = Invoice::distinct()->pluck('payment_status');
        return view('admin-panel.09-utilities.invoices.lists', $viewData);
    }

    /**
     * Get the list of Country with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
         $hour_range = $request->input('hour_range');
        $search = $request->input("search");
                   $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');
        

        $records = Invoice::with(['invoicePaymentLink'])->where(function ($query) use ($search) {
            // Apply search filter if search term is provided
                if (!empty($search)) {
                    $query->where(function ($query) use ($search) {
                        $query->where("first_name", "LIKE", "%{$search}%")
                            ->orWhere("last_name", "LIKE", "%{$search}%")
                            ->orWhere("total_amount", "LIKE", "%{$search}%")
                                ->orWhere("payment_status", "LIKE", "%{$search}%");
                    });
                }
        // Ap
        });

        // Apply status filter if provided; default to 'paid' when none selected
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $records->whereIn('payment_status', $statuses);
        }

        // Apply amount filters: predefined ranges OR custom min/max
        $hasPriceRanges = $request->filled('price_range');
        $minRange = $request->filled('min_range') && $request->input('min_range') !== '' ? $request->input('min_range') : null;
        $maxRange = $request->filled('max_range') && $request->input('max_range') !== '' ? $request->input('max_range') : null;
        $hasSlider = $minRange !== null || $maxRange !== null;
        
        if ($hasPriceRanges || $hasSlider) {
            $ranges = $hasPriceRanges ? (is_array($request->price_range) ? $request->price_range : [$request->price_range]) : [];
            $records->where(function ($q) use ($ranges, $minRange, $maxRange) {
                foreach ($ranges as $range) {
                    switch ($range) {
                        case 'under-100':
                            $q->orWhere('total_amount', '<', 100);
                            break;
                        case '100-500':
                            $q->orWhereBetween('total_amount', [100, 500]);
                            break;
                        case '500-1000':
                            $q->orWhereBetween('total_amount', [500, 1000]);
                            break;
                        case 'over-1000':
                            $q->orWhere('total_amount', '>', 1000);
                            break;
                    }
                }
                // Custom min/max slider range
                $minIsNumeric = is_numeric($minRange);
                $maxIsNumeric = is_numeric($maxRange);
                if ($minIsNumeric && $maxIsNumeric) {
                    $q->orWhereBetween('total_amount', [(float)$minRange, (float)$maxRange]);
                } elseif ($minIsNumeric) {
                    $q->orWhere('total_amount', '>=', (float)$minRange);
                } elseif ($maxIsNumeric) {
                    $q->orWhere('total_amount', '<=', (float)$maxRange);
                }
            });
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if ($startDate && $endDate) {
            $records->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        } elseif ($startDate && !$endDate) {
            $records->whereDate('created_at', '>=', $startDate);
        } elseif (!$startDate && $endDate) {
            $records->whereDate('created_at', '<=', $endDate);
        }
        
        // Quick date ranges: today, this_week, this_month
        if ($request->filled('hour_range')) {
            $ranges = is_array($request->hour_range) ? $request->hour_range : [$request->hour_range];
            $records->where(function ($q) use ($ranges) {
                foreach ($ranges as $r) {
                    switch ($r) {
                        case 'today':
                            $q->orWhereBetween('created_at', [Carbon::today(), Carbon::tomorrow()]);
                            break;
                        case 'this_week':
                            $q->orWhereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $q->orWhereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                            break;
                    }
                }
            });
        }
        
        $records = $records->where("invoice_type","global")
        ->where('added_by',auth()->id())
         ->orderBy($sortColumn, $sortDirection)
        ->paginate();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.09-utilities.invoices.ajax-list', $viewData);
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
        $viewData['pageTitle'] = "Add Invoice";

        $client_ids = CaseWithProfessionals::where('professional_id',auth()->user()->id)->distinct()->pluck('client_id')->toArray();
        $viewData['users'] = $users = \App\Models\User::whereIn('id', $client_ids)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function ($user) {
                                $user->name = $user->first_name . ' ' . $user->last_name; 
                                return $user;
                            });
                           
    
        return view('admin-panel.09-utilities.invoices.add', $viewData);
    }

    /**
     * Store a newly created action in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $response = $this->invoiceService->saveInvoice($request);
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

      
        $client_ids = CaseWithProfessionals::where('professional_id',auth()->user()->id)->distinct()->pluck('client_id')->toArray();
        $viewData['users'] = $users = \App\Models\User::whereIn('id', $client_ids)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function ($user) {
                                $user->name = $user->first_name . ' ' . $user->last_name;
                                return $user;
                            });
         $viewData['record'] = $record;
        $viewData['pageTitle'] = "Edit Invoice";
        return view('admin-panel.09-utilities.invoices.edit', $viewData);
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
      
        $response = $this->invoiceService->updateInvoice($id, $request);
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
    public function deleteMultiple(Request $request)
    {
        $ids = explode(",", $request->input("ids"));
        for ($i = 0; $i < count($ids); $i++) {
            $act = AdvisoryType::where('unique_id',$ids[$i])->first();
            AdvisoryType::deleteRecord($act->id);
        }
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }

    public function generateLink(Request $request,$id)
    {
      
        $viewData['pageTitle'] = "Choose option"; // Set the page title
        $viewData['invoice_id'] = $id;
        $viewData['invoice'] = Invoice::where('unique_id',$id)->first();
        $view = View::make('admin-panel.09-utilities.invoices.choose-option-modal', $viewData);
        $contents = $view->render();

        // Prepare the JSON response
        $response['contents'] = $contents;
        $response['status'] = true;

        return response()->json($response);
    }

    public function createPaymentLink(Request $request)
    {
        $response = $this->invoiceService->createPaymentLink($request);
        return response()->json($response);
    }

    public function downloadInvoicePDF($invoice_id)
    {
        return $this->invoiceService->downloadInvoicePDF($invoice_id);
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
        $view = View::make('admin-panel.09-utilities.invoices.copy-link', $viewData);
        $contents = $view->render();
        
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);

    }

    public function professionalGlobalInitiative(Request $request,$id)
    {
       
        $viewData['pageTitle'] = "Support Our Intitiative";
        if (\Session::has("payment_success")) {
            return view('admin-panel.09-utilities.transactions.payment.thank-you-support', $viewData);
        }
        \Session::put("back_to_support",url()->current());
        $record = array();
        $user = array();
        if(auth()->check()){
            $record = Invoice::where("unique_id",$id)->first();
            $user = User::find(auth()->user()->id);
        }else{
            
            if($request->has('ref') & $request->has('token')){
                $user = User::where('unique_id',decryptVal($request->get('ref')))->first();
                $current_time = Carbon::now();
                $remeberToken = RememberToken::where('user_id',$user->id)->where('unique_id',decryptVal($request->get('token')))->first();
                if(empty($remeberToken)){
                    return redirect('professional/support');
                }else{
                    if($current_time > $remeberToken->expiry_time){
                        return redirect('professional/support');
                    }
                    \Session::put('support_ref_id',$remeberToken->unique_id);
                    \Session::put('support_user_id',$user->id);
                }
                $record = Invoice::where("user_id",$user->id)->where("invoice_type","support")->latest()->first();
            }
        }
        $viewData['record'] = $record;
        $viewData['user'] = $user;
        
        Stripe::setApiKey(apiKeys('STRIPE_SECRET'));

        $viewData['intent'] = \Stripe\SetupIntent::create([
           'usage' => 'off_session'  // Ensure that the user has a `stripe_id`
        ]);

        $viewData['adddress'] = CompanyLocations::where('user_id',auth()->user()->id)->where('type_label','personal')->first();
        return view('admin-panel.09-utilities.invoices.payment.professional-support', $viewData);
    }

    public function processSupportPayment(Request $request)
    {
        $response = $this->invoiceService->processSupportPayment($request);
        return response()->json($response);
    }

    public function processingPayment(Request $request){
        $viewData = array();
        $view = view("components.payment-processing-modal",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;
        return response()->json($response);
    }

    public function completePaymentAction(Request $request)
    {
        $response = $this->invoiceService->completePaymentAction($request);
        return response()->json($response);
    }
    
}
