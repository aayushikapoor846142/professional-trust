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
use App\Services\CommonInvoiceService;

class CommonInvoiceController extends Controller
{
    protected $commonInvoiceService;

    public function __construct(CommonInvoiceService $commonInvoiceService)
    {
        $this->commonInvoiceService = $commonInvoiceService;
    }

    // Private helper to extract user data from request
    private function extractUserData(Request $request)
    {
        return [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone_no' => $request->input('phone'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
            'b_address' => $request->input('address'),
            'b_city' => $request->input('city'),
            'b_state' => $request->input('state'),
            'b_zip' => $request->input('zip'),
            'b_country' => $request->input('country'),
        ];
    }

    // Private helper to create or update Stripe customer
    private function createOrUpdateStripeCustomer($user_data, $request, $paymentMethodId)
    {
        if ($user_data->stripe_id != '') {
            Customer::update($user_data->stripe_id, [
                'shipping' => [
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'address' => [
                        'line1' => $request->address,
                        'city' => $request->city,
                        'state' => $request->state,
                        'postal_code' => $request->zip,
                    ],
                ],
            ]);
            return $user_data->stripe_id;
        } else {
            $customer = Customer::create([
                'email' => $request->email,
                'name' => $request->first_name . ' ' . $request->last_name,
                'payment_method' => $paymentMethodId,
                'invoice_settings' => ['default_payment_method' => $paymentMethodId],
                'shipping' => [
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'address' => [
                        'line1' => $request->address_line1,
                        'city' => $request->city,
                        'state' => $request->state,
                        'postal_code' => $request->postal_code,
                    ]
                ]
            ]);
            $user_data->stripe_id = $customer->id;
            $user_data->save();
            return $customer->id;
        }
    }

    // Private helper to attach payment method
    private function attachPaymentMethod($paymentMethodId, $stripe_customer_id)
    {
        $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
        $paymentMethod->attach(['customer' => $stripe_customer_id]);
        \Stripe\Customer::update($stripe_customer_id, [
            'invoice_settings' => [
                'default_payment_method' => $paymentMethodId
            ]
        ]);
    }

    public function index(Request $request,$id)
    {
        $utk = request()->query('utk');
        $rtk = request()->query('rtk');
     
        $viewData['pageTitle'] = "Support Our Intitiative";
        $record = Invoice::with(['invoiceItems'])->where("id",decryptVal($id))->first();
        $user = User::where('id',$record->user_id)->where('token',decryptVal($utk))->first();
        if(empty($user)){
            return redirect()->route('unauthorized-access')->with('error', 'You do not have permission to perform this action.');
        }
        
        $viewData['record'] = $record;
        $viewData['user'] = $user;
        
        Stripe::setApiKey(apiKeys('STRIPE_SECRET'));

        $viewData['intent'] = \Stripe\SetupIntent::create([
           'usage' => 'off_session'  // Ensure that the user has a `stripe_id`
        ]);

        $viewData['adddress'] = CompanyLocations::where('user_id',auth()->user()->id)->where('type_label','personal')->first();
        return view('components.invoice-payment.professional-support', $viewData);
    }

    public function processingPayment(Request $request){
        $viewData = array();
        $view = view("components.invoice-payment.payment-processing-modal",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;
        return response()->json($response);
    }

    public function processGlobalPayment(Request $request)
    {
        Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
        $new_user = false;
        $checkUser = User::where("email", $request->input("email"))->first();
        if (auth()->check()) {
            $user_id = auth()->user()->id;
            $user = auth()->user();
        } elseif (!empty($checkUser)) {
            $user_id = $checkUser->id;
            $user = $checkUser;
        } else {
            $password = generateRandomString(15);
            $customer = Customer::create([
                'email' => $request->input("email"),
                'name' => $request->input("first_name") . " " . $request->input("last_name"),
            ]);
            $user = new User();
            $user->first_name = $request->input("first_name");
            $user->last_name = $request->input("last_name");
            $user->email = $request->input("email");
            $user->country_code = $request->input("country_code");
            $user->phone_no = $request->input("phone");
            $user->password = bcrypt($password);
            $user->role = "supporter";
            $user->status = "active";
            $user->temporary_password = 1;
            $user->stripe_id = $customer->id;
            $user->save();
            $user_id = $user->id;
            $user_detail = new UserDetails();
            $user_detail->user_id = $user->id;
            $user_detail->save();
            $new_user = true;
        }
        try {
            $amount = $request->amount_to_pay;
            $transaction_amount = $amount;
            $currency = 'cad';
            $supportTax = siteSetting('support_tax');
            $taxAmount = ($amount * $supportTax) / 100;
            $totalAmount = $amount + $taxAmount;
            $email = $request->email;
            $name = $request->first_name . ' ' . $request->last_name;
            $user_data = User::find($user_id);
            $paymentMethodId = $request->payment_method_id;
            // Use service to create or update Stripe customer
            $stripe_customer_id = $this->commonInvoiceService->createOrUpdateStripeCustomer($user_data, $request, $paymentMethodId);
            // Attach payment method
            $this->commonInvoiceService->attachPaymentMethod($paymentMethodId, $stripe_customer_id);
            // Use service to extract user data
            $data = $this->commonInvoiceService->extractUserData($request);
            $data['tax_percent'] = $supportTax;
            $new_customer = User::where('id', $user_id)->first();
            // Use service to create payment intent
            $paymentIntent = $this->commonInvoiceService->createPaymentIntent($new_customer, $request, $totalAmount, $amount, $taxAmount, $currency, $supportTax);

            
            if ($paymentIntent->status == 'succeeded') {

                $user_detail = User::where('id', $user_id)->first();

                $url = url('support/thankyou');
              
                \DB::commit();
                \Session::flash("support_success", "Amount submitted successfully. Thank you for your support");
                return response()->json([
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'requires_action' => $paymentIntent->status === 'requires_action',
                    'status' => true,
                    'new_user' => $new_user,
                    'message' => "Amount submitted successfully. Thank you for your support",
                    "redirect_url" => $url
                ]);
            } elseif ($paymentIntent->status == 'requires_action') {
                $url = url('support/thankyou');
                $user_detail = User::where('id', $user_id)->first();
                \Session::put("support_success", "Amount submitted successfully. Thank you for your support");
                \DB::commit();
                return response()->json([
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'requires_action' => $paymentIntent->status === 'requires_action',
                    'status' => true,
                    'new_user' => $new_user,
                    // 'message' => "Amount submitted successfully. Thank you for your support",
                    "redirect_url" => $url
                ]);
            } else {
                \DB::rollback();
                $stripeErrorLogs = new StripeErrorLogs();
                $stripeErrorLogs->event = 'Internal Error';
                $stripeErrorLogs->payment_id = 0;
                $stripeErrorLogs->response = "Payment failed. Try again";
                $stripeErrorLogs->save();
                \Session::flash("error", "Payment failed. Try again");
                return response()->json(['status' => false, 'message' => "Payment failed. Try again"]);
            }
        } catch (\Exception $e) {
            \DB::rollback();
            $stripeErrorLogs = new StripeErrorLogs();
            $stripeErrorLogs->event = 'Internal Error';
            $stripeErrorLogs->payment_id = 0;
            $stripeErrorLogs->response = $e->getMessage();
            $stripeErrorLogs->save();
            \Log::info($e->getMessage());
            return response()->json(['status' => false, 'message' => $e->getMessage()]);

        }
    }


    public function completePaymentAction(Request $request)
    {
        Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
        $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);
        $amount = $request->amount_to_pay;
        $new_user = $request->new_user;
        $transaction_amount = $amount;
        $currency = 'cad';
        // if(currency() == 'CAD'){
        //     $currency = 'cad';
        // }else{
        //     $currency = 'usd';
        // }
        // Create a Payment Intent

        $supportTax = siteSetting('support_tax');
        $taxAmount = ($amount * $supportTax) / 100;
        $totalAmount = $amount + $taxAmount;

        $email = $request->email;
        $name = $request->first_name . ' ' . $request->last_name;
        $user_data = User::where('email', $email)->first();
        $user_id = $user_data->id;
        $other_details = array();
        $other_details['donation_type'] = $request->input("donation_type");
        $other_details['payment_type'] = $request->input("payment_type");
        if ($request->input("company_name")) {
            $other_details['company_name'] = $request->input("company_name");
        }
        $object = new PaymentTransaction();
        $object->amount = $amount;
        $object->user_id = $user_id;
        $object->payment_method_id = $request->input("payment_method_id");
        $object->status = $paymentIntent->status;
        $object->response = json_encode($paymentIntent);
        $object->other_details = json_encode(($other_details));
        $object->save();

        $data = [];
        $data['first_name'] = $request->input("first_name");
        $data['last_name'] = $request->input("last_name");
        $data['email'] = $request->input("email");
        $data['phone_no'] = $request->input("phone");
        $data['address'] = $request->input("address");
        $data['city'] = $request->input("city");
        $data['state'] = $request->input("state");
        $data['zip'] = $request->input("zip");
        $data['country'] = $request->input("country");
        $data['b_address'] = $request->input("address");

        $data['b_city'] = $request->input("city");
        $data['b_state'] = $request->input("state");
        $data['b_zip'] = $request->input("zip");
        $data['b_country'] = $request->input("country");


        if($request->invoice_id != ''){
            $invoice  = Invoice::where('unique_id',$request->invoice_id)->first();
        }else{
            $invoice = new Invoice();
        }
        $invoice->user_id = $user_id;
        $invoice->first_name = $request->input("first_name");
        $invoice->last_name = $request->input("last_name");
        $invoice->email = $request->input("email");
        $invoice->country_code = $request->input("country_code");
        $invoice->phone_no = $request->input("phone");
        $invoice->address = $request->input("address");
        $invoice->city = $request->input("city");
        $invoice->state = $request->input("state");
        $invoice->zip = $request->input("zip");
        $invoice->country = $request->input(key: "country");

        $invoice->b_address = $request->input("address");
        $invoice->b_city = $request->input("city");
        $invoice->b_state = $request->input("state");
        $invoice->b_zip = $request->input("zip");
        $invoice->b_country = $request->input(key: "country");
        $invoice->reference_id = $lastSupport->id ?? 0;
        $invoice->currency = 'CAD';
        $invoice->tax = $supportTax;
        $invoice->sub_total = $amount;
        $invoice->total_amount = $totalAmount;
        $invoice->payment_status = $paymentIntent->status == 'succeeded' ? 'paid' : '';
        $invoice->paid_date = $paymentIntent->status == 'succeeded' ? date('Y-m-d') : '';
        $invoice->transaction_id = $object->id;
        $invoice->save();

        $invoice_id = $invoice->id;

        $invoice_number = $invoice->invoice_number;
       
        $invoice_items = InvoiceItem::where("invoice_id", $invoice_id)->get();
        $invoice = Invoice::where("id", $invoice_id)->first();

        $pdfData = ['invoice_number' => $invoice_number, "invoice_items" => $invoice_items, "invoice" => $invoice];
        $pdf = Pdf::loadView('components.invoice', $pdfData);
        $invoice_folder = storage_path("app/public/invoices");
        if (!is_dir($invoice_folder)) {
            mkdir($invoice_folder, 0777, true);
        }// Filename (inside public/invoices)
        $filePath = storage_path('app/public/invoices/invoice_' . $invoice_number . '.pdf'); // Full path where PDF will be stored in 'public'

        // Step 3: Save the PDF file in the public folder
        file_put_contents($filePath, $pdf->output());

        if($paymentIntent->status == 'succeeded'){
            \Log::info('status succedddeddddd');
        }

        // $mailData = array();
        // $mailData = [
        //     "totalPoints" => $totalPoints['totalPoints'],
        //     "name" => $request->first_name . ' ' . $request->last_name,
        //     "payment_type" => $request->input("payment_type"),
        //     "totalAmount" => $totalAmount,
        // ];
        // $view = \View::make('emails.support-invoice', $mailData);
        // $message = $view->render();

        // $parameter = [
        //     'to' => $request->email,
        //     'to_name' => $request->first_name . ' ' . $request->last_name,
        //     'message' => $message,
        //     'subject' => siteSetting("company_name") . ": Generated invoice of support payment",
        //     'view' => 'emails.support-invoice',
        //     'data' => $mailData,
        //     'invoice_pdf' => $filePath,
        // ];

        // sendMail($parameter);


        // send email to admin

        if (!isLocal()) {
            $adminMailData = [
                "name" => $request->first_name . ' ' . $request->last_name,
                "email" => $request->email,
                "payment_type" => $lastSupport->payment_type,
                "totalAmount" => $totalAmount,
            ];
            $adminView = \View::make('emails.admin-support-invoice', $adminMailData);
            $message = $adminView->render();

            foreach (sendEmailTo() as $mail) {
                $parameter = [
                    'to' => $mail,
                    'to_name' => $request->first_name . ' ' . $request->last_name,
                    'message' => $message,
                    'subject' => siteSetting("company_name") . ": User Support",
                    'view' => 'emails.admin-support-invoice',
                    'data' => $adminMailData,
                ];
                sendMail($parameter);
            }



        }

        $user_detail = User::where('id', $invoice->user_id)->first();

        $url = baseUrl('/');
       
        \DB::commit();
        \Session::flash("support_success", "Amount submitted successfully. Thank you for your support");
        return response()->json([
            'status' => true,
            'message' => "Amount submitted successfully. Thank you for your support",
            "redirect_url" => $url
        ]);
    }
}
