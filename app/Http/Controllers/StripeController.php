<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;   
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\User;
use App\Models\CaseWithProfessionals;
use App\Models\AppointmentBooking;
use App\Models\StripeErrorLogs;
use Stripe\PaymentMethod;
use Stripe\Customer;
use Stripe\Subscription;

class StripeController extends Controller
{
    public function processSupportPayment(Request $request)
    {
        
       
        Stripe::setApiKey(apiKeys('STRIPE_SECRET'));

        try {
            if($request->amount ==  'other'){
                $amount = $request->custom_amount;
            }else{
                $amount = $request->amount;
            }
            if(currency() == 'CAD'){
                $currency = 'cad';
            }else{
                $currency = 'usd';
            }
            // Create a Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => $currency,
                'payment_method' => $request->payment_method_id,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never', // Prevent redirect-based payment methods
                ],
            ]);
            if($paymentIntent->status == 'succeeded'){
                $other_details = array();
                $other_details['donation_type'] = $request->input("donation_type");
                if($request->input("company_name")){
                    $other_details['company_name'] = $request->input("company_name");
                }
                $object = new PaymentTransaction();
                $object->amount = $amount;
                $object->payment_method_id = $request->input("payment_method_id");
                $object->status = $paymentIntent->status;
                $object->response = json_encode($paymentIntent);
                $object->save();

                $invoice = new Invoice();
                $object->other_details = json_encode(($other_details));
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

                $invoice->b_address = $request->input("billing_address");
                $invoice->b_city = $request->input("billing_city");
                $invoice->b_state = $request->input("billing_state");
                $invoice->b_zip = $request->input("billing_zip");
                $invoice->b_country = $request->input(key: "billing_country");

                $invoice->currency = currencySymbol();
                $invoice->tax = 0;
                $invoice->sub_total = $amount;
                $invoice->total_amount = $amount;
                $invoice->payment_status = $paymentIntent->status == 'succeeded'?'paid':'';
                $invoice->paid_date =  $paymentIntent->status == 'succeeded'?date('Y-m-d'):'';
                $invoice->transaction_id = $object->id;
                $invoice->invoice_type = 'support';
                $invoice->save();

                $invoice_id = $invoice->id;
                $invoice_item = new InvoiceItem();
                $invoice_item->invoice_id = $invoice_id;
                $invoice_item->particular = "Amount paid for supporting <b>TrustVisory</b> initiative";
                $invoice_item->amount = $amount;
                $invoice_item->save();  
                $invoice_number = $invoice->invoice_number;
                if($paymentIntent->status == 'succeeded'){
                    $invoice_items = InvoiceItem::where("invoice_id",$invoice_id)->get();
                    $invoice = Invoice::where("id",$invoice_id)->first();
                    $pdfData = ['invoice_number' => $invoice_number,"invoice_items"=>$invoice_items,"invoice"=>$invoice];

                    $pdf = Pdf::loadView('pdf.invoice', $pdfData);
                    $invoice_folder = storage_path("app/public/invoices"); 
                    if (!is_dir($invoice_folder)) {
                        mkdir($invoice_folder, 0777, true);
                    }// Filename (inside public/invoices)
                    $filePath = storage_path('app/public/invoices/invoice_' . $invoice_number . '.pdf'); // Full path where PDF will be stored in 'public'
                
                    // Step 3: Save the PDF file in the public folder
                    file_put_contents($filePath, $pdf->output());

                    $mailData = array();
                    $view = \View::make('emails.invoice-email', $mailData);
                    $message = $view->render();

                    $parameter = [
                        'to' => $request->email,
                        'to_name' => $request->first_name.' '.$request->last_name,
                        'message' => $message,
                        'subject' => siteSetting("company_name").": Generated invoice of support payment",
                        'view' => 'emails.invoice-email',
                        'data' => $mailData,
                        'invoice_pdf'=>$filePath,
                    ];

                    sendMail($parameter);
                }

                // \Session::flash("payment_success","Amount submitted successfully. Thank you for your support");
                // \Session::flash("success","Amount submitted successfully. Thank you for your support");
                $view = view("support.thank-you-support");
                $contents  = $view->render();
                return response()->json(['status' => true, 'message' => "Amount submitted successfully. Thank you for your support","contents"=>$contents]);
            }else{
                \Session::flash("error","Payment failed. Try again");
                return response()->json(['status' => false, 'message' => "Payment failed. Try again"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false,'message' => $e->getMessage()]);
        }
    }

    public function sendTestMail(){
        
        
        $invoice = Invoice::latest()->first();
        $invoice_items = InvoiceItem::where("invoice_id",$invoice->id)->get();
        $invoice_number = $invoice->invoice_number;
        $pdfData = ['invoice_number' => $invoice_number,"invoice_items"=>$invoice_items,"invoice"=>$invoice];
        
        $pdf = Pdf::loadView('pdf.invoice', $pdfData);
        $invoice_folder = public_path("invoices"); 
        if (!is_dir($invoice_folder)) {
            mkdir($invoice_folder, 0777, true);
        }
        $invoice_path = 'invoices/invoice_' . $invoice_number . '.pdf'; // Filename (inside public/invoices)
        $filePath = public_path($invoice_path); // Full path where PDF will be stored in 'public'
    
        // Step 3: Save the PDF file in the public folder
        // file_put_contents($filePath, $pdf->output());

        $mailData = array();
        return view('pdf.invoice', $pdfData);
        // $message = $view->render();
        // $parameter = [
        //     'to' => $invoice->email,
        //     'to_name' => $invoice->first_name.' '.$invoice->last_name,
        //     'message' => $message,
        //     'subject' => siteSetting("company_name").": Generated invoice of support payment",
        //     'view' => 'emails.invoice-email',
        //     'data' => $mailData,
        //     'invoice_pdf'=>$invoice_path,
        // ];

        // $response = sendMail($parameter);
        // pre($response);
    }

    public function supportValidation(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'email' => 'required|email',
            'amount_to_pay' => 'required',
        ], [
            'otp6.required' => 'The OTP field is required.',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $errorMessages = [
                'otp6' => 'The OTP field is required.'
            ];

            $response['message'] = $errorMessages;

            return response()->json($response);
        }
    }

    public function processReconsiderPayment(Request $request)
    {
        Stripe::setApiKey(apiKeys('STRIPE_SECRET'));

        try {
            // if($request->amount ==  'other'){
            //     $amount = $request->custom_amount;
            // }else{
            //     $amount = $request->amount;
            // }
            //     $amount = $request->amount;
            $amount = $request->amount_to_pay;
            if(currency() == 'CAD'){
                $currency = 'cad';
            }else{
                $currency = 'usd';
            }
            // Create a Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => $currency,
                'payment_method' => $request->payment_method_id,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never', // Prevent redirect-based payment methods
                ],
            ]);
            if($paymentIntent->status == 'succeeded'){
               
                $object = new PaymentTransaction();
                $object->amount = $amount;
                $object->payment_method_id = $request->input("payment_method_id");
                $object->status = $paymentIntent->status;
                $object->response = json_encode($paymentIntent);
                $object->save();

                
                $invoice = new Invoice();
                $invoice->first_name = auth()->user()->first_name;
                $invoice->last_name = auth()->user()->last_name;
                $invoice->email = auth()->user()->email;
                $invoice->phone_no = auth()->user()->phone_no;
                $invoice->address = auth()->user()->address;
                $invoice->city = auth()->user()->city_id;
                $invoice->state = auth()->user()->state_id;
                $invoice->zip = auth()->user()->zip_code;
                $invoice->country = auth()->user()->country_id;

                $invoice->b_address = $request->input("billing_address");
                $invoice->b_city = $request->input("billing_city");
                $invoice->b_state = $request->input("billing_state");
                $invoice->b_zip = $request->input("billing_zip");
                $invoice->b_country = $request->input(key: "billing_country");
                $invoice->reference_id = $request->uap_id;
                $invoice->currency = currencySymbol();
                $invoice->tax = 0;
                $invoice->sub_total = $amount;
                $invoice->total_amount = $amount;
                $invoice->payment_status = $paymentIntent->status == 'succeeded'?'paid':'';
                $invoice->paid_date =  $paymentIntent->status == 'succeeded'?date('Y-m-d'):'';
                $invoice->transaction_id = $object->id;
                $invoice->invoice_type = 'support';
                $invoice->save();

                $invoice_id = $invoice->id;
                $invoice_item = new InvoiceItem();
                $invoice_item->invoice_id = $invoice_id;
                $invoice_item->particular = "Amount paid for reconsidering <b>TrustVisory</b> profile";
                $invoice_item->amount = $amount;
                $invoice_item->save();  
                $invoice_number = $invoice->invoice_number;
                if($paymentIntent->status == 'succeeded'){
                    $invoice_items = InvoiceItem::where("invoice_id",$invoice_id)->get();
                    $invoice = Invoice::where("id",$invoice_id)->first();
                    $pdfData = ['invoice_number' => $invoice_number,"invoice_items"=>$invoice_items,"invoice"=>$invoice];

                    $pdf = Pdf::loadView('pdf.invoice', $pdfData);
                    $invoice_folder = storage_path("app/public/invoices"); 
                    if (!is_dir($invoice_folder)) {
                        mkdir($invoice_folder, 0777, true);
                    }// Filename (inside public/invoices)
                    $filePath = storage_path('app/public/invoices/invoice_' . $invoice_number . '.pdf'); // Full path where PDF will be stored in 'public'
                
                    // Step 3: Save the PDF file in the public folder
                    file_put_contents($filePath, $pdf->output());

                    
                    $mailData = array();
                    $view = \View::make('emails.invoice-email', $mailData);
                    $message = $view->render();

                    $parameter = [
                        'to' => auth()->user()->email,
                        'to_name' => auth()->user()->first_name.' '.auth()->user()->last_name,
                        'message' => $message,
                        'subject' => siteSetting("company_name").": Generated invoice of reconsider payment",
                        'view' => 'emails.invoice-email',
                        'data' => $mailData,
                        'invoice_pdf'=>$filePath,
                    ];

                    sendMail($parameter);
                }

                $apiData['reconsider_id'] = $request->reconsider_id;
                $apiData['paid_amount'] = $request->amount_to_pay;
                $apiData['paid_date'] = date('Y-m-d');
              
                $records = investgateApiCall('update-uap-reconsider-payment', $apiData);

                return response()->json(['status' => true, 'message' => "Amount submitted successfully. Thank you for your support",'redirect_back' => url('unauthorised-practitioners/'.$records['data']['uap_id'].'/'.$records['data']['slug'])]);
            }else{
                \Session::flash("error","Payment failed. Try again");
                return response()->json(['status' => false, 'message' => "Payment failed. Try again"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false,'message' => $e->getMessage()]);
        }
    }

    public function processOneTimePayment(Request $request)
    {
       
        Stripe::setApiKey(apiKeys('STRIPE_SECRET'));

        try {
            if($request->amount ==  'other'){
                $amount = $request->custom_amount;
            }else{
                $amount = $request->amount;
            }
            if(currency() == 'CAD'){
                $currency = 'cad';
            }else{
                $currency = 'usd';
            }
            // Create a Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => $currency,
                'payment_method' => $request->payment_method_id,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never', // Prevent redirect-based payment methods
                ],
            ]);
            
            if($paymentIntent->status == 'succeeded'){
                $other_details = array();
               
                $object = new PaymentTransaction();
                $object->amount = $amount;
                $object->payment_method_id = $request->input("payment_method_id");
                $object->status = $paymentIntent->status;
                $object->response = json_encode($paymentIntent);
                $object->save();

                $invoice = new Invoice();
                $object->other_details = json_encode(($other_details));
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

                $invoice->b_address = $request->input("billing_address");
                $invoice->b_city = $request->input("billing_city");
                $invoice->b_state = $request->input("billing_state");
                $invoice->b_zip = $request->input("billing_zip");
                $invoice->b_country = $request->input(key: "billing_country");

                $invoice->currency = currencySymbol();
                $invoice->tax = 0;
                $invoice->sub_total = $amount;
                $invoice->total_amount = $amount;
                $invoice->payment_status = $paymentIntent->status == 'succeeded'?'paid':'';
                $invoice->paid_date =  $paymentIntent->status == 'succeeded'?date('Y-m-d'):'';
                $invoice->transaction_id = $object->id;
                $invoice->invoice_type = 'onetime';
                $invoice->save();

                $invoice_id = $invoice->id;
                $invoice_item = new InvoiceItem();
                $invoice_item->invoice_id = $invoice_id;
                $invoice_item->particular = "Amount paid for Onetime <b>TrustVisory</b> initiative";
                $invoice_item->amount = $amount;
                $invoice_item->save();  
                $invoice_number = $invoice->invoice_number;
                if($paymentIntent->status == 'succeeded'){
                    $invoice_items = InvoiceItem::where("invoice_id",$invoice_id)->get();
                    $invoice = Invoice::where("id",$invoice_id)->first();
                    $pdfData = ['invoice_number' => $invoice_number,"invoice_items"=>$invoice_items,"invoice"=>$invoice];

                    $pdf = Pdf::loadView('pdf.invoice', $pdfData);
                    $invoice_folder = storage_path("app/public/invoices"); 
                    if (!is_dir($invoice_folder)) {
                        mkdir($invoice_folder, 0777, true);
                    }// Filename (inside public/invoices)
                    $filePath = storage_path('app/public/invoices/invoice_' . $invoice_number . '.pdf'); // Full path where PDF will be stored in 'public'
                
                    // Step 3: Save the PDF file in the public folder
                    file_put_contents($filePath, $pdf->output());

                    $mailData = array();
                    $view = \View::make('emails.invoice-email', $mailData);
                    $message = $view->render();

                    $parameter = [
                        'to' => $request->email,
                        'to_name' => $request->first_name.' '.$request->last_name,
                        'message' => $message,
                        'subject' => siteSetting("company_name").": Generated invoice of support payment",
                        'view' => 'emails.invoice-email',
                        'data' => $mailData,
                        'invoice_pdf'=>$filePath,
                    ];

                    sendMail($parameter);
                }

                // \Session::flash("payment_success","Amount submitted successfully. Thank you for your support");
                // \Session::flash("success","Amount submitted successfully. Thank you for your support");
                $url = baseUrl('membership-plans');
                return response()->json(['status' => true, 'message' => "Amount submitted successfully. Thank you for your support","url"=>$url]);
            }else{
                \Session::flash("error","Payment failed. Try again");
                return response()->json(['status' => false, 'message' => "Payment failed. Try again"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false,'message' => $e->getMessage()]);
        }
    }

    public function processAppointmentBooking(Request $request)
    {
        \DB::beginTransaction();
        Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
        $new_user = false;
        $checkUser = User::where("email", $request->input("email"))->first();
        if (auth()->check()) {
            $user_id = auth()->user()->id;
            $user = auth()->user();
        }

        try {
            $amount = $request->amount_to_pay;
            $transaction_amount = $amount;
            $currency = 'cad';

            $supportTax = 0;
            $taxAmount = ($amount * $supportTax) / 100;
            $totalAmount = $amount + $taxAmount;

            $email = $request->email;
            $name = $request->first_name . ' ' . $request->last_name;
            $user_data = User::find($user_id);

            $paymentMethodId = $request->payment_method_id;
            if ($user_data->stripe_id != '') {
                $checkStripeCustomer = stripeCustomerExists($user_data->stripe_id);
                if($checkStripeCustomer){
                    $stripe_customer_id = $user_data->stripe_id;
                    Customer::update($stripe_customer_id, [
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
                }else{
                    $customer = Customer::create([
                        'email' => $email,
                        'name' => $name,
                        'payment_method' => $paymentMethodId,
                        'invoice_settings' => ['default_payment_method' => $paymentMethodId],
                        'shipping' => [
                            'name' => $request->first_name . ' ' . $request->last_name,
                            'address' => [
                                'line1' => $request->address,
                                'city' => $request->city,
                                'state' => $request->state,
                                'postal_code' => $request->postal_code,
                            ]
                        ]
                    ]);

                    $stripe_customer_id = $customer->id;
                    $customer_user = User::where('id', $user_id)->first();
                    $customer_user->stripe_id = $stripe_customer_id;
                    $customer_user->save();
                }
                
                \Log::info($stripe_customer_id . 'update');
            } else {
                $customer = Customer::create([
                    'email' => $email,
                    'name' => $name,
                    'payment_method' => $paymentMethodId,
                    'invoice_settings' => ['default_payment_method' => $paymentMethodId],
                    'shipping' => [
                        'name' => $request->first_name . ' ' . $request->last_name,
                        'address' => [
                            'line1' => $request->address,
                            'city' => $request->city,
                            'state' => $request->state,
                            'postal_code' => $request->postal_code,
                        ]
                    ]
                ]);

                $stripe_customer_id = $customer->id;
                $customer_user = User::where('id', $user_id)->first();
                $customer_user->stripe_id = $stripe_customer_id;
                $customer_user->save();
            }

            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $stripe_customer_id]);

            \Stripe\Customer::update($stripe_customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId
                ]
            ]);

            $data = [
                'first_name' => $request->input("first_name"),
                'last_name' => $request->input("last_name"),
                'email' => $request->input("email"),
                'phone_no' => $request->input("phone"),
                'address' => $request->input("address"),
                'city' => $request->input("city"),
                'state' => $request->input("state"),
                'zip' => $request->input("zip"),
                'country' => $request->input("country"),
                'b_address' => $request->input("address"),
                'b_city' => $request->input("city"),
                'b_state' => $request->input("state"),
                'b_zip' => $request->input("zip"),
                'b_country' => $request->input("country"),
                'tax_percent' => $supportTax
            ];

            $new_customer = User::where('id', $user_id)->first();

            $paymentIntent = PaymentIntent::create([
                'amount' => $totalAmount * 100, // Convert to cents
                'currency' => $currency,
                'payment_method' => $paymentMethodId,
                'confirm' => true,
                'customer' => $new_customer->stripe_id,
                'receipt_email' => $email,
                'description' => "Appointment Booking payment for TrustVisory ",
                'shipping' => [
                    'name' => $name,
                    'address' => [
                        'line1' => $request->input("address"),
                        'line2' => $request->input("address"),
                        'city' => $request->input("city"),
                        'state' => $request->input("state"),
                        'postal_code' => $request->input("zip"),
                        'country' => $request->input("country")
                    ]
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'payment_method_options' => [
                    'card' => [
                        'request_three_d_secure' => 'any',
                    ],
                ],
                'metadata' => [
                    'subtotal' => $amount * 100,
                    'tax' => $taxAmount * 100,
                    'tax_behavior' => 'inclusive',
                    'trusted_customer' => 'true',
                ]
            ]);

            $user_detail = User::where('id', $user_id)->first();

            $url =  baseUrl('appointments/appointment-booking-success/'.$request->appointment_booking_id);

        

            \DB::commit();

            $responseData = [
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'requires_action' => $paymentIntent->status === 'requires_action',
                'status' => true,
                'new_user' => $new_user,
                'redirect_url' => $url
            ];
        
            if ($paymentIntent->status == 'succeeded') {
                \Session::flash("support_success", "Amount submitted successfully. Thank you for your support");
                $responseData['message'] = "Amount submitted successfully. Thank you for your support";
            } elseif ($paymentIntent->status == 'requires_action') {
                \Session::put("support_success", "Amount submitted successfully. Thank you for your support");
            }else {
                \DB::rollback();
                $stripeErrorLogs = new StripeErrorLogs();
                $stripeErrorLogs->event = 'Internal Error';
                $stripeErrorLogs->payment_id = 0;
                $stripeErrorLogs->response = "Payment failed. Try again";
                $stripeErrorLogs->save();
                \Session::flash("error", "Payment failed. Try again");
                return response()->json(['status' => false, 'message' => "Payment failed. Try again"]);
            }

            return response()->json($responseData);

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
    $appointment_booking_id= $request->appointment_booking_id;
    $supportTax = 0;
    $taxAmount = ($amount * $supportTax) / 100;
    $totalAmount = $amount + $taxAmount;

    $email = $request->email;
    $name = $request->first_name . ' ' . $request->last_name;
    $user_data = User::where('email', $email)->first();
    $user_id = $user_data->id;
    $other_details = array();
    $other_details['payment_type'] = 'One Time';
    $object = new PaymentTransaction();

    $object->first_name = $request->first_name;
    $object->last_name = $request->last_name;
    $object->address = $request->address;
    $object->state = $request->state;
    $object->email = $request->email;
    $object->zip = $request->zip;
    $object->country = $request->country;
    $object->payment_gateway = 'stripe';
    $object->transaction_for = 'appointment-booking';
    $object->city = $request->city;
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

    $appointmentBooking = AppointmentBooking::where('unique_id', $appointment_booking_id)->first();
    $appointmentBooking->payment_status = $paymentIntent->status == 'succeeded'?'paid':'pending';
    $appointmentBooking->completed_step = 6;
    $appointmentBooking->paid_by = auth()->user()->id;
    $appointmentBooking->status = 'approved';

    $appointmentBooking->save();

    // Save UserPlanFeatureHistory entry for appointment booking payment
    if ($paymentIntent->status == 'succeeded') {
        try {
            $featureCheckService = new \App\Services\FeatureCheckService();
            $featureCheckService->savePlanFeature(
                'appointments',
                auth()->user()->id,
                1, // action type: add
                1, // count: 1 appointment
                [
                    'appointment_id' => $appointmentBooking->id,
                    'appointment_unique_id' => $appointmentBooking->unique_id,
                    'appointment_date' => $appointmentBooking->appointment_date,
                    'appointment_status' => 'approved',
                    'payment_status' => 'paid',
                    'is_free_appointment' => false,
                    'professional_id' => $appointmentBooking->professional_id,
                    'client_id' => $appointmentBooking->user_id,
                    'payment_method' => 'stripe',
                    'transaction_id' => $object->id,
                    'amount_paid' => $amount
                ]
            );
            
        } catch (\Exception $e) {
            \Log::error('Error saving appointment payment to UserPlanFeatureHistory', [
                'appointment_id' => $appointmentBooking->id,
                'user_id' => auth()->user()->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    $invoice = new Invoice();
    $invoice->user_id = $user_id;
    $invoice->added_by = auth()->user()->id;

    $invoice->first_name = $request->input("first_name");
    $invoice->last_name = $request->input("last_name");
    $invoice->email = $request->input("email");
    $invoice->country_code = $request->input("country_code");
    $invoice->phone_no = $request->input("phone");
    $invoice->address = $request->input("address");
    $invoice->city = $request->input("city");
    $invoice->state = $request->input("state");
    $invoice->zip = $request->input("zip");
    $invoice->country = $request->input("country");
    $invoice->discount = 0;
    $invoice->b_address = $request->input("address");
    $invoice->b_city = $request->input("city");
    $invoice->b_state = $request->input("state");
    $invoice->b_zip = $request->input("zip");
    $invoice->b_country = $request->input("country");
    $invoice->reference_id = $appointmentBooking->id ?? 0;
    $invoice->currency = 'CAD';
    $invoice->tax = $supportTax;
    $invoice->sub_total = $amount;
    $invoice->total_amount = $totalAmount;
    $invoice->payment_status = $paymentIntent->status == 'succeeded' ? 'paid' : '';
    $invoice->paid_date = $paymentIntent->status == 'succeeded' ? date('Y-m-d') : '';
    $invoice->transaction_id = $object->id;
    $invoice->invoice_type = 'appointment-booking';
    $invoice->save();

    $invoice_id = $invoice->id;
    $invoice_item = new InvoiceItem();
    $invoice_item->invoice_id = $invoice_id;
    $invoice_item->discount = 0;

    $invoice_item->particular = "Amount paid for Appointment Booking <b>TrustVisory</b> initiative";
    $invoice_item->amount = $amount;
    $invoice_item->save();

    $invoice_number = $invoice->invoice_number;
    // if($paymentIntent->status == 'succeeded'){
    $invoice_items = InvoiceItem::where("invoice_id", $invoice_id)->get();
    $invoice = Invoice::where("id", $invoice_id)->first();
    $pdfData = ['invoice_number' => $invoice_number, "invoice_items" => $invoice_items, "invoice" => $invoice];
            
            $mailDataClient['appointment']=   $mailData['appointment'] = $appointmentBooking;
            $mailDataClient['professional_name'] =  $mailData['professional_name'] = $appointmentBooking->professional->first_name . " " . $appointmentBooking->professional->last_name;
            $mailDataClient['client_name'] = $mailData['client_name'] = $appointmentBooking->client->first_name . " " . $appointmentBooking->client->last_name;
            $mail_message = \View::make('emails.appointment_booking_payment', $mailData);
            $mailData['mail_message'] = $mail_message;
            $parameter['to'] =$appointmentBooking->professional->email;
            $parameter['to_name'] = $appointmentBooking->professional->first_name . " " . $appointmentBooking->professional->last_name;
            $parameter['message'] = $mail_message;
            $parameter['subject'] = "Appointment Booking Payment Done";
            $parameter['view'] = "emails.appointment_booking_payment";
            $parameter['data'] = $mailData;
            $data=sendMail($parameter);

            
            $mail_message_client = \View::make('emails.appointment_booking_client', $mailDataClient);
            $mailData['mail_message'] = $mail_message_client;
            $parameter['to'] =$appointmentBooking->client->email;
            $parameter['to_name'] = $appointmentBooking->client->first_name . " " . $appointmentBooking->client->last_name;
            $parameter['message'] = $mail_message_client;
            $parameter['subject'] = "Appointment Booking Payment Done";
            $parameter['view'] = "emails.appointment_booking_client";
            $parameter['data'] = $mailData;
            
            $dataClient=sendMail($parameter);
 
    // $pdf = Pdf::loadView('55-utilities.55-01-pdf.invoice', $pdfData);
    // $invoice_folder = storage_path("app/public/invoices");
    // if (!is_dir($invoice_folder)) {
    //     mkdir($invoice_folder, 0777, true);
    // }// Filename (inside public/invoices)
    // $filePath = storage_path('app/public/invoices/invoice_' . $invoice_number . '.pdf'); // Full path where PDF will be stored in 'public'

    // // Step 3: Save the PDF file in the public folder
    // file_put_contents($filePath, $pdf->output());

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

    // if (!isLocal()) {
    //     $adminMailData = [
    //         "name" => $request->first_name . ' ' . $request->last_name,
    //         "email" => $request->email,
    //         "payment_type" => $lastSupport->payment_type,
    //         "totalAmount" => $totalAmount,
    //     ];
    //     $adminView = \View::make('emails.admin-support-invoice', $adminMailData);
    //     $message = $adminView->render();

    //     foreach (sendEmailTo() as $mail) {
    //         $parameter = [
    //             'to' => $mail,
    //             'to_name' => $request->first_name . ' ' . $request->last_name,
    //             'message' => $message,
    //             'subject' => siteSetting("company_name") . ": User Support",
    //             'view' => 'emails.admin-support-invoice',
    //             'data' => $adminMailData,
    //         ];
    //         sendMail($parameter);
    //     }



    // }




  

    // }

    $user_detail = User::where('id', $invoice->user_id)->first();
    $url =  baseUrl('appointments/appointment-booking-success/'.$request->appointment_booking_id);

 
    \DB::commit();
    \Session::flash("support_success", "Amount submitted successfully. Thank you for your support");
    return response()->json([
        'status' => true,
        'message' => "Amount submitted successfully. Thank you for your support",
        "redirect_url" => $url
    ]);
}
    
}
