<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SupportByUser;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\PaymentMethod;
use Stripe\PaymentIntent;
use App\Models\AutoLoginToken;
use App\Models\SubscriptionInvoiceHistory;
use App\Models\StripeErrorLogs;
use Illuminate\Support\Facades\Log;
use App\Models\UserSubscriptionHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PaymentTransaction;
use App\Models\PointEarn;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentMethodsPaymentController extends Controller
{
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    public function professionalSupportInitiative(Request $request)
    {
        $viewData['pageTitle'] = "Support Our Intitiative";
        if (\Session::has("payment_success")) {
            return view('admin-panel.09-utilities.transactions.payment.thank-you-support', $viewData);
        }
        \Session::put("back_to_support",url()->current());
        $record = array();
        $user = array();
        if(auth()->check()){
            $record = Invoice::where("user_id",auth()->user()->id)->where("invoice_type","support")->latest()->first();
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
        return view('admin-panel.09-utilities.transactions.payment.professional-support', $viewData);
    }


    public function processSupportPayment(Request $request)
    {
        // \DB::beginTransaction();
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
            // Save the new customer ID to the user

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

            // $mailData['name'] = $request->input("first_name") . " " . $request->input("last_name");
            // $mailData['email'] = $request->input("email");
            // $mailData['password'] = $password;
            // $view = \View::make('emails.supporter-welcome', $mailData);
            // $message = $view->render();
            // $parameter = [
            //     'to' => $request->input("email"),
            //     'to_name' => $request->input("first_name") . " " . $request->input("last_name"),
            //     'message' => $message,
            //     'subject' => "Welcome to " . siteSetting('company_name'),
            //     'view' => 'emails.supporter-welcome',
            //     'data' => $mailData,
            // ];
            // sendMail($parameter);

            $new_user = true;
        }
        try {

            $amount = $request->custom_amount;
            // if($request->amount ==  'other'){
            //     $amount = $request->custom_amount;
            // }else{
            //     $amount = $request->amount;
            // }
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
            $user_data = User::find($user_id);

            $paymentMethodId = $request->payment_method_id;
            if ($user_data->stripe_id != '') {
                $stripe_customer_id = $user_data->stripe_id;
                if(stripeCustomerExists($stripe_customer_id) == true){
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
                                    'line1' => $request->address_line1,
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
                            'line1' => $request->address_line1,
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


            if ($request->payment_type == 'Monthly') {
                $paymentMethodId = $request->payment_method_id;
                $user = User::find($user_id);
                $price = \Stripe\Price::create([
                    'unit_amount' => $totalAmount * 100,
                    'currency' => 'cad',
                    'recurring' => ['interval' => 'month'],
                    'product_data' => [
                        'name' => "Support Monthly Subscription - $" . $totalAmount . " of " . $name,
                    ],
                ]);
                $paymentMethodId = $request->payment_method_id;
                $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);

                $paymentMethod->attach(['customer' => $stripe_customer_id]);
                // \Stripe\PaymentMethod::attach($paymentMethodId, [
                //     'customer' => $stripe_customer_id
                // ]);

                \Stripe\Customer::update($stripe_customer_id, [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethodId
                    ]
                ]);
                $subscription = Subscription::create([
                    'customer' => $stripe_customer_id,
                    'items' => [
                        ['price' => $price->id],
                    ],
                    'expand' => ['latest_invoice.payment_intent'],
                    'metadata' => [
                        'trusted_customer' => 'true',
                        'user_id' => $user_id,
                        'source' => 'support_form',
                    ],
                ]);
                $paymentIntent = $subscription->latest_invoice->payment_intent;

                $supportByUser = new SupportByUser();
                $supportByUser->user_id = $user_id;
                $supportByUser->tax = $supportTax;
                $supportByUser->amount = $amount;
                $supportByUser->total_amount = $totalAmount;
                $supportByUser->donation_type = $request->input("donation_type");
                $supportByUser->payment_type = $request->input("payment_type");
                $supportByUser->subscription_id = $subscription->id ?? '';
                $supportByUser->subscription_data = json_encode($data);
                $supportByUser->is_anonymous = $request->input("is_anonymous") ?? 'no';
                $supportByUser->save();

                $userSubscriptionHistory = new UserSubscriptionHistory;
                $userSubscriptionHistory->unique_id = randomNumber();
                $userSubscriptionHistory->membership_plans_plan_id = 0;
                $userSubscriptionHistory->stripe_subscription_id = $subscription->id ?? '';
                $userSubscriptionHistory->subscription_status = $subscription->status;
                $userSubscriptionHistory->user_id = $user_id;
                $userSubscriptionHistory->payment_gateway = "stripe";
                $userSubscriptionHistory->subscription_type = 'support';
                $userSubscriptionHistory->save();


                $invoicehistory = new SubscriptionInvoiceHistory();
                $invoicehistory->user_id = $user_id;
                $invoicehistory->subscription_history_id = $userSubscriptionHistory->id;
                $invoicehistory->stripe_subscription_id = $subscription->id;
                $invoicehistory->stripe_invoice_number = $subscription->latest_invoice->id;
                $invoicehistory->next_invoice_date = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->toDateTimeString();
                $invoicehistory->stripe_invoice_status = $subscription->status;
                $invoicehistory->save();


            } else {
                $paymentMethodId = $request->payment_method_id;
                $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);

                $paymentMethod->attach(['customer' => $stripe_customer_id]);


                \Stripe\Customer::update($stripe_customer_id, [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethodId
                    ]
                ]);
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
                $data['tax_percent'] = $supportTax;
                $new_customer = User::where('id', $user_id)->first();

                $paymentIntent = PaymentIntent::create([
                    'amount' => $totalAmount * 100, // Convert to cents
                    'currency' => $currency,
                    'payment_method' => $request->payment_method_id,
                    'confirm' => true,
                    'customer' => $new_customer->stripe_id,
                    // 'confirmation_method' => 'automatic',
                    'receipt_email' => $request->email,
                    'description' => "Support payment for TrustVisory ",
                    'shipping' => [
                        'name' => $request->input("first_name") . " " . $request->input("last_name"),
                        'address' => [
                            'line1' => $request->input("address_line1"),
                            'line2' => $request->input("address_line2"),
                            'city' => $request->input("city"),
                            'state' => $request->input("state"),
                            'postal_code' => $request->input("postal_code"),
                            'country' => $request->input("country")
                        ]
                    ],
                    // 'return_url' => url('stripe/confirm-payment'),
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never', // Prevent redirect-based payment methods
                    ],
                    'payment_method_options' => [
                        'card' => [
                            'request_three_d_secure' => 'any', // ✅ Force 3D Secure if available
                        ],
                    ],
                    'metadata' => [
                        'subtotal' => $amount * 100,
                        'tax' => $taxAmount * 100,
                        'tax_behavior' => 'inclusive',
                        'trusted_customer' => 'true',
                        // 'form_data' => json_encode($data),
                    ]
                ]);

                $supportByUser = new SupportByUser();
                $supportByUser->user_id = $user_id;
                $supportByUser->tax = $supportTax;
                $supportByUser->amount = $amount;
                $supportByUser->total_amount = $totalAmount;
                $supportByUser->donation_type = $request->input("donation_type");
                $supportByUser->payment_type = $request->input("payment_type");
                $supportByUser->subscription_data = json_encode($data);
                $supportByUser->is_anonymous = $request->input("is_anonymous") ?? 'no';
                $supportByUser->save();
            }
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

    public function processingPayment(Request $request){
        $viewData = array();
        $view = view("components.payment-processing-modal",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;
        return response()->json($response);
    }

    public function completePaymentAction(Request $request)
    {
        Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
        $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);
        $amount = $request->custom_amount;
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

        $lastSupport = SupportByUser::where('user_id', $user_id)->latest()->first();
        ;
        // $supportByUser = new SupportByUser();
        // $supportByUser->user_id = $user_id;
        // $supportByUser->tax = $supportTax;
        // $supportByUser->amount = $amount;
        // $supportByUser->total_amount = $totalAmount;
        // $supportByUser->donation_type = $request->input("donation_type");
        // $supportByUser->payment_type = $request->input("payment_type");
        // $supportByUser->subscription_id = $subscription->id??'';
        // $supportByUser->subscription_data = json_encode($data);
        // $supportByUser->save();

        $invoice = new Invoice();
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
        $invoice->invoice_type = 'support';
        $invoice->save();

        $invoice_id = $invoice->id;
        $invoice_item = new InvoiceItem();
        $invoice_item->invoice_id = $invoice_id;
        $invoice_item->particular = "Amount paid for supporting <b>TrustVisory</b> initiative";
        $invoice_item->amount = $amount;
        $invoice_item->save();

        $invoice_number = $invoice->invoice_number;
        // if($paymentIntent->status == 'succeeded'){
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

        $points = calculatePoints($transaction_amount, "CAD");
        $totalPoints = calculateBonus($transaction_amount, "CAD", $points);

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




        // end

        // $total_amounts = Invoice::where("invoice_type","support")->where("user_id",$user_id)->sum("sub_total");

        PointEarn::create([
            'user_id' => $user_id,
            'points' => $totalPoints['points'],
            'bonus_points' => $totalPoints['bonusPoints'],
            'total_points' => $totalPoints['totalPoints'],
        ]);

        $pointData = [
            "totalPoints" => $totalPoints['totalPoints'],
            "name" => $request->first_name . ' ' . $request->last_name,
            "payment_type" => $request->input("payment_type"),
            "totalAmount" => $totalAmount,
        ];
        \Session::put("point_data", $pointData);


        // }

        $user_detail = User::where('id', $invoice->user_id)->first();

        $url = url('support/thankyou');
       
        \DB::commit();
        \Session::flash("support_success", "Amount submitted successfully. Thank you for your support");
        return response()->json([
            'status' => true,
            'message' => "Amount submitted successfully. Thank you for your support",
            "redirect_url" => $url
        ]);
    }

}
