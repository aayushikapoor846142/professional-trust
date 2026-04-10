<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Models\InvoicePaymentLink;
use App\Models\PaymentLinkParameter;
use App\Models\SupportByUser;
use App\Models\UserSubscriptionHistory;
use App\Models\SubscriptionInvoiceHistory;
use App\Models\StripeErrorLogs;
use App\Models\PaymentTransaction;
use App\Models\PointEarn;
use App\Models\CompanyLocations;
use App\Models\CaseWithProfessionals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Subscription;
use Razorpay\Api\Api;
use Carbon\Carbon;

class InvoiceService
{
    protected $commonInvoiceService;

    public function __construct(CommonInvoiceService $commonInvoiceService)
    {
        $this->commonInvoiceService = $commonInvoiceService;
    }

    // Save a new invoice and related items
    public function saveInvoice(Request $request)
    {
        $response = [];
        try {
            $validator = \Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string|max:255',
                'items.*.amount' => 'required|numeric|min:1',
                'user_id' => 'required',
                'invoice_date' => 'required',
                'due_date' => 'required',
                'bill_to' => 'required',
                'bill_from' => 'required',
                'currency' => 'required',
                'total_amount' => 'required|numeric|min:1'],
                [
                    'items.required' => 'At least one item is required.',
                    'items.array' => 'Invalid item format.',
                    'items.min' => 'You must add at least one item.',
                    'items.*.name.required' => 'The item name is required.',
                    'items.*.name.string' => 'The item name must be a valid text.',
                    'items.*.name.max' => 'The item name should not exceed 255 characters.',
                    'items.*.amount.required' => 'The item amount is required.',
                    'items.*.amount.numeric' => 'The item amount must be a number.',
                    'items.*.amount.min' => 'The item amount must be at least 1.',
                ]);

            if ($validator->fails()) {
                $response['status'] = false;
                $error = $validator->errors()->toArray();
                $errMsg = array();
                foreach ($error as $key => $err) {
                    $errMsg[$key] = $err[0];
                }
                $response['message'] = $errMsg;
                return $response;
            }

            $latest = \App\Models\Invoice::latest()->first();
            $user = \App\Models\User::where('id', $request->user_id)->first();

            $invoice = new \App\Models\Invoice();
            $invoice->unique_id = randomNumber();
            $invoice->invoice_number = $latest ? $latest->invoice_number + 1 : 1;
            $invoice->tax = $request->tax;
            $invoice->sub_total = $request->sub_total;
            $invoice->total_amount = $request->total_amount;
            $invoice->currency = $request->currency;
            $invoice->user_id = $request->user_id;
            $invoice->first_name = $user->first_name;
            $invoice->last_name = $user->last_name;
            $invoice->email = $user->email;
            $invoice->country_code = $user->country_code;
            $invoice->phone_no = $user->phone_no;
            $invoice->invoice_type = 'global';
            $invoice->payment_status = 'pending';
            $invoice->added_by = auth()->user()->id;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->due_date = $request->due_date;
            $invoice->notes = $request->note_terms;
            $invoice->bill_to = $request->bill_to;
            $invoice->bill_from = $request->bill_from;
            $invoice->discount = $request->total_discount ?? 0;
            $invoice->discount_type = $request->total_discount_type;
            $invoice->save();

            // Handle invoice items
            foreach ($request->items as $item) {
                $discount = isset($item['discount']) ? $item['discount'] : 0;
                \App\Models\InvoiceItem::create([
                    'particular' => $item['name'],
                    'amount' => $item['amount'],
                    'discount_type' => $item['discount_type'],
                    'discount' => $discount,
                    'invoice_id' => $invoice->id
                ]);
            }

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

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('invoices');
            $response['message'] = "Record added successfully";
        } catch (\Exception $e) {
            \Log::error('InvoiceService@saveInvoice error', ['error' => $e->getMessage()]);
            $response['status'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    // Update an existing invoice and its items
    public function updateInvoice($id, Request $request)
    {
        $response = [];
        try {
            $validator = \Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string|max:255',
                'items.*.amount' => 'required|numeric|min:1',
                'user_id' => 'required',
                'invoice_date' => 'required',
                'due_date' => 'required',
                'bill_to' => 'required',
                'bill_from' => 'required',
                'total_amount' => 'required|numeric|min:1'],
                [
                    'items.required' => 'At least one item is required.',
                    'items.array' => 'Invalid item format.',
                    'items.min' => 'You must add at least one item.',
                    'items.*.name.required' => 'The item name is required.',
                    'items.*.name.string' => 'The item name must be a valid text.',
                    'items.*.name.max' => 'The item name should not exceed 255 characters.',
                    'items.*.amount.required' => 'The item amount is required.',
                    'items.*.amount.numeric' => 'The item amount must be a number.',
                    'items.*.amount.min' => 'The item amount must be at least 1.',
                ]);

            if ($validator->fails()) {
                $response['status'] = false;
                $error = $validator->errors()->toArray();
                $errMsg = array();
                foreach ($error as $key => $err) {
                    $errMsg[$key] = $err[0];
                }
                $response['message'] = $errMsg;
                return $response;
            }

            $user = \App\Models\User::where('id', $request->user_id)->first();
            $invoice = \App\Models\Invoice::where('unique_id', $id)->first();
            $invoice->tax = $request->tax;
            $invoice->sub_total = $request->sub_total;
            $invoice->total_amount = $request->total_amount;
            $invoice->currency = $request->currency;
            $invoice->user_id = $request->user_id;
            $invoice->first_name = $user->first_name;
            $invoice->last_name = $user->last_name;
            $invoice->email = $user->email;
            $invoice->country_code = $user->country_code;
            $invoice->phone_no = $user->phone_no;
            $invoice->invoice_type = 'global';
            $invoice->payment_status = 'pending';
            $invoice->added_by = auth()->user()->id;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->due_date = $request->due_date;
            $invoice->notes = $request->note_terms;
            $invoice->bill_to = $request->bill_to;
            $invoice->bill_from = $request->bill_from;
            $invoice->discount = $request->total_discount;
            $invoice->discount_type = $request->total_discount_type;
            $invoice->save();
            $inv_items = array();
            foreach ($request->items as $item) {
                if (!empty($item['id'])) {
                    $invoice_item = \App\Models\InvoiceItem::where('id', $item['id'])->first();
                    $item_unique_id = $invoice_item->id;
                } else {
                    $invoice_item = new \App\Models\InvoiceItem();
                    $invoice_item->invoice_id = $invoice->id;
                    $item_unique_id = $invoice_item->id;
                }
                $discount = isset($item['discount']) ? $item['discount'] : 0;
                $invoice_item->particular = $item['name'];
                $invoice_item->amount = $item['amount'];
                $invoice_item->discount_type = $item['discount_type'];
                $invoice_item->discount = $discount;
                $invoice_item->save();
                $inv_items[] = $item_unique_id;
            }
            \App\Models\InvoiceItem::where('invoice_id', $invoice->id)->whereNotIn('id', $inv_items)->delete();
            $response['status'] = true;
            $response['redirect_back'] = baseUrl('invoices');
            $response['message'] = "Record added successfully";
        } catch (\Exception $e) {
            \Log::error('InvoiceService@updateInvoice error', ['error' => $e->getMessage()]);
            $response['status'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function createPaymentLink(Request $request)
    {
        $response = [];
        try {
            $invoice = \App\Models\Invoice::where('unique_id', $request->invoice_id)->first();
            \App\Models\InvoicePaymentLink::where('invoice_id', $invoice->id)->delete();
            if ($request->type_val == 'Stripe') {
                $invoiceRazorpayPaymentLink = \App\Models\InvoicePaymentLink::where('invoice_id', $invoice->id)->where('payment_gateway', 'RazorPay')->first();
                if (!empty($invoiceRazorpayPaymentLink)) {
                    $api = new \Razorpay\Api\Api(apiKeys('TEST_RAZORPAY_KEY_ID'), apiKeys('TEST_RAZORPAY_KEY_SECRET'));
                    $plinkId = $invoiceRazorpayPaymentLink->payment_session_id;
                    $paymentLink = $api->paymentLink->fetch($plinkId);
                    if ($paymentLink) {
                        $api->paymentLink->fetch($plinkId)->cancel();
                    }
                }
                $invoicePaymentLink = \App\Models\InvoicePaymentLink::where('invoice_id', $invoice->id)->where('payment_gateway', 'Stripe')->first();
                \Stripe\Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
                if (!empty($invoicePaymentLink)) {
                    $sessionId = $invoicePaymentLink->payment_session_id;
                    $session = \Stripe\Checkout\Session::retrieve($sessionId);
                    if ($session['status'] != 'expired') {
                        $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
                        $stripe->checkout->sessions->expire($invoicePaymentLink->payment_session_id, []);
                    }
                }
                $success_url = url('/invoice-payment-success');
                $checkout_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => $invoice->currency,
                            'product_data' => [
                                'name' => 'Payment',
                            ],
                            'unit_amount' => $invoice->total_amount * 100,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => $success_url ?? url('/invoice-payment-success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => $request->cancel_url ?? url('/payment-cancel'),
                ]);
                \App\Models\InvoicePaymentLink::create([
                    'invoice_id' => $invoice->id,
                    'payment_gateway' => $request->type_val,
                    'payment_link' => $checkout_session->url,
                    'payment_session_id' => $checkout_session->id,
                    'user_id' => $invoice->user_id,
                    'added_by' => auth()->user()->id
                ]);
                $user = \App\Models\User::where('id', $invoice->user_id)->first();
                $this->sendInvoiceEmail($user, $checkout_session->url);
                $response = [
                    'status' => true,
                    'message' => 'Link generated successfully',
                    'payment_link' => $checkout_session->url,
                    'session_id' => $checkout_session->id
                ];
            } else {
                $api = new \Razorpay\Api\Api(apiKeys('TEST_RAZORPAY_KEY_ID'), apiKeys('TEST_RAZORPAY_KEY_SECRET'));
                $success_url = url('/invoice-payment-success');
                $invoiceStripePaymentLink = \App\Models\InvoicePaymentLink::where('invoice_id', $invoice->id)->where('payment_gateway', 'Stripe')->first();
                if (!empty($invoiceStripePaymentLink)) {
                    \Stripe\Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
                    $sessionId = $invoiceStripePaymentLink->payment_session_id;
                    $session = \Stripe\Checkout\Session::retrieve($sessionId);
                    if ($session['status'] != 'expired') {
                        $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
                        $stripe->checkout->sessions->expire($invoiceStripePaymentLink->payment_session_id, []);
                    }
                }
                $invoicePaymentLink = \App\Models\InvoicePaymentLink::where('invoice_id', $invoice->id)->where('payment_gateway', 'RazorPay')->first();
                if (!empty($invoicePaymentLink)) {
                    $plinkId = $invoicePaymentLink->payment_session_id;
                    $paymentLink = $api->paymentLink->fetch($plinkId);
                    if ($paymentLink) {
                        $api->paymentLink->fetch($plinkId)->cancel();
                    }
                }
                $paymentLink = $api->paymentLink->create([
                    'amount' => $invoice->total_amount * 100,
                    'currency' => 'INR',
                    'description' => $request->description ?? 'Payment for Order',
                    'callback_url' => $success_url ?? url('/payment-success'),
                    'callback_method' => 'get',
                    'reference_id' => $request->invoice_id,
                ]);
                $url = $paymentLink['short_url'];
                $user = \App\Models\User::where('id', $invoice->user_id)->first();
                $this->sendInvoiceEmail($user, $url);
                \App\Models\InvoicePaymentLink::create([
                    'invoice_id' => $invoice->id,
                    'payment_gateway' => $request->type_val,
                    'payment_link' => $url,
                    'payment_session_id' => $paymentLink['id'],
                    'user_id' => $invoice->user_id,
                    'added_by' => auth()->user()->id
                ]);
                $response = [
                    'status' => true,
                    'message' => 'Link generated successfully',
                    'payment_link' => $url,
                    'payment_id' => $paymentLink['id']
                ];
            }
        } catch (\Exception $e) {
            \Log::error('InvoiceService@createPaymentLink error', ['error' => $e->getMessage()]);
            $response = ['status' => false, 'message' => $e->getMessage()];
        }
        return $response;
    }

    public function downloadInvoicePDF($invoice_id)
    {
        try {
            $invoice = \App\Models\Invoice::where('unique_id', $invoice_id)->first();
            $invoice_number = $invoice->invoice_number;
            $invoice_items = \App\Models\InvoiceItem::where('invoice_id', $invoice->id)->get();
            $data = [
                'invoice_number' => $invoice_number,
                'invoice' => $invoice,
                'invoice_items' => $invoice_items,
                'logo_url' => url('assets/images/logo.png')
            ];
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.global-invoice', $data);
            $name = "Invoice#" . $invoice_number . '.pdf';
            return $pdf->download($name);
        } catch (\Exception $e) {
            \Log::error('InvoiceService@downloadInvoicePDF error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function processSupportPayment(Request $request)
    {
        $response = [];
        try {
            \Stripe\Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
            $new_user = false;
            $checkUser = \App\Models\User::where('email', $request->input('email'))->first();
            if (auth()->check()) {
                $user_id = auth()->user()->id;
                $user = auth()->user();
            } elseif (!empty($checkUser)) {
                $user_id = $checkUser->id;
                $user = $checkUser;
            } else {
                $password = generateRandomString(15);
                $customer = \Stripe\Customer::create([
                    'email' => $request->input('email'),
                    'name' => $request->input('first_name') . ' ' . $request->input('last_name'),
                ]);
                $user = new \App\Models\User();
                $user->first_name = $request->input('first_name');
                $user->last_name = $request->input('last_name');
                $user->email = $request->input('email');
                $user->country_code = $request->input('country_code');
                $user->phone_no = $request->input('phone');
                $user->password = bcrypt($password);
                $user->role = 'supporter';
                $user->status = 'active';
                $user->temporary_password = 1;
                $user->stripe_id = $customer->id;
                $user->save();
                $user_id = $user->id;
                $user_detail = new \App\Models\UserDetails();
                $user_detail->user_id = $user->id;
                $user_detail->save();
                $new_user = true;
            }
            $amount = $request->custom_amount;
            $transaction_amount = $amount;
            $currency = 'cad';
            $supportTax = siteSetting('support_tax');
            $taxAmount = ($amount * $supportTax) / 100;
            $totalAmount = $amount + $taxAmount;
            $email = $request->email;
            $name = $request->first_name . ' ' . $request->last_name;
            $user_data = \App\Models\User::find($user_id);
            $paymentMethodId = $request->payment_method_id;
            if ($user_data->stripe_id != '') {
                \Stripe\Customer::update($user_data->stripe_id, [
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
            } else {
                $customer = \Stripe\Customer::create([
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
                $customer_user = \App\Models\User::where('id', $user_id)->first();
                $customer_user->stripe_id = $stripe_customer_id;
                $customer_user->save();
            }
            $data = [
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
            if ($request->payment_type == 'Monthly') {
                $price = \Stripe\Price::create([
                    'unit_amount' => $totalAmount * 100,
                    'currency' => 'cad',
                    'recurring' => ['interval' => 'month'],
                    'product_data' => [
                        'name' => "Support Monthly Subscription - $" . $totalAmount . " of " . $name,
                    ],
                ]);
                $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
                $paymentMethod->attach(['customer' => $user_data->stripe_id]);
                \Stripe\Customer::update($user_data->stripe_id, [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethodId
                    ]
                ]);
                $subscription = \Stripe\Subscription::create([
                    'customer' => $user_data->stripe_id,
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
                $supportByUser = new \App\Models\SupportByUser();
                $supportByUser->user_id = $user_id;
                $supportByUser->tax = $supportTax;
                $supportByUser->amount = $amount;
                $supportByUser->total_amount = $totalAmount;
                $supportByUser->donation_type = $request->input('donation_type');
                $supportByUser->payment_type = $request->input('payment_type');
                $supportByUser->subscription_id = $subscription->id ?? '';
                $supportByUser->subscription_data = json_encode($data);
                $supportByUser->is_anonymous = $request->input('is_anonymous') ?? 'no';
                $supportByUser->save();
                $userSubscriptionHistory = new \App\Models\UserSubscriptionHistory;
                $userSubscriptionHistory->unique_id = randomNumber();
                $userSubscriptionHistory->membership_plans_plan_id = 0;
                $userSubscriptionHistory->stripe_subscription_id = $subscription->id ?? '';
                $userSubscriptionHistory->subscription_status = $subscription->status;
                $userSubscriptionHistory->user_id = $user_id;
                $userSubscriptionHistory->payment_gateway = 'stripe';
                $userSubscriptionHistory->subscription_type = 'support';
                $userSubscriptionHistory->save();
                $invoicehistory = new \App\Models\SubscriptionInvoiceHistory();
                $invoicehistory->user_id = $user_id;
                $invoicehistory->subscription_history_id = $userSubscriptionHistory->id;
                $invoicehistory->stripe_subscription_id = $subscription->id;
                $invoicehistory->stripe_invoice_number = $subscription->latest_invoice->id;
                $invoicehistory->next_invoice_date = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->toDateTimeString();
                $invoicehistory->stripe_invoice_status = $subscription->status;
                $invoicehistory->save();
            } else {
                $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
                $paymentMethod->attach(['customer' => $user_data->stripe_id]);
                \Stripe\Customer::update($user_data->stripe_id, [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethodId
                    ]
                ]);
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $totalAmount * 100,
                    'currency' => $currency,
                    'payment_method' => $request->payment_method_id,
                    'confirm' => true,
                    'customer' => $user_data->stripe_id,
                    'receipt_email' => $request->email,
                    'description' => 'Support payment for TrustVisory ',
                    'shipping' => [
                        'name' => $request->input('first_name') . ' ' . $request->input('last_name'),
                        'address' => [
                            'line1' => $request->input('address_line1'),
                            'line2' => $request->input('address_line2'),
                            'city' => $request->input('city'),
                            'state' => $request->input('state'),
                            'postal_code' => $request->input('postal_code'),
                            'country' => $request->input('country')
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
                $supportByUser = new \App\Models\SupportByUser();
                $supportByUser->user_id = $user_id;
                $supportByUser->tax = $supportTax;
                $supportByUser->amount = $amount;
                $supportByUser->total_amount = $totalAmount;
                $supportByUser->donation_type = $request->input('donation_type');
                $supportByUser->payment_type = $request->input('payment_type');
                $supportByUser->subscription_data = json_encode($data);
                $supportByUser->is_anonymous = $request->input('is_anonymous') ?? 'no';
                $supportByUser->save();
            }
            $response['client_secret'] = $paymentIntent->client_secret ?? null;
            $response['payment_intent_id'] = $paymentIntent->id ?? null;
            $response['requires_action'] = isset($paymentIntent->status) && $paymentIntent->status === 'requires_action';
            $response['status'] = true;
            $response['new_user'] = $new_user;
            $response['message'] = 'Amount submitted successfully. Thank you for your support';
            $response['redirect_url'] = url('support/thankyou');
        } catch (\Exception $e) {
            \Log::error('InvoiceService@processSupportPayment error', ['error' => $e->getMessage()]);
            $response = ['status' => false, 'message' => $e->getMessage()];
        }
        return $response;
    }

    public function completePaymentAction(Request $request)
    {
        $response = [];
        try {
            \Stripe\Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
            $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);
            $amount = $request->custom_amount;
            $new_user = $request->new_user;
            $transaction_amount = $amount;
            $currency = 'cad';
            $supportTax = siteSetting('support_tax');
            $taxAmount = ($amount * $supportTax) / 100;
            $totalAmount = $amount + $taxAmount;
            $email = $request->email;
            $name = $request->first_name . ' ' . $request->last_name;
            $user_data = \App\Models\User::where('email', $email)->first();
            $user_id = $user_data->id;
            $other_details = [
                'donation_type' => $request->input('donation_type'),
                'payment_type' => $request->input('payment_type'),
            ];
            if ($request->input('company_name')) {
                $other_details['company_name'] = $request->input('company_name');
            }
            $object = new \App\Models\PaymentTransaction();
            $object->amount = $amount;
            $object->user_id = $user_id;
            $object->payment_method_id = $request->input('payment_method_id');
            $object->status = $paymentIntent->status;
            $object->response = json_encode($paymentIntent);
            $object->other_details = json_encode($other_details);
            $object->save();
            $data = [
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
            $lastSupport = \App\Models\SupportByUser::where('user_id', $user_id)->latest()->first();
            if ($request->invoice_id != '') {
                $invoice = \App\Models\Invoice::where('unique_id', $request->invoice_id)->first();
            } else {
                $invoice = new \App\Models\Invoice();
            }
            $invoice->user_id = $user_id;
            $invoice->first_name = $request->input('first_name');
            $invoice->last_name = $request->input('last_name');
            $invoice->email = $request->input('email');
            $invoice->country_code = $request->input('country_code');
            $invoice->phone_no = $request->input('phone');
            $invoice->address = $request->input('address');
            $invoice->city = $request->input('city');
            $invoice->state = $request->input('state');
            $invoice->zip = $request->input('zip');
            $invoice->country = $request->input('country');
            $invoice->b_address = $request->input('address');
            $invoice->b_city = $request->input('city');
            $invoice->b_state = $request->input('state');
            $invoice->b_zip = $request->input('zip');
            $invoice->b_country = $request->input('country');
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
            $invoice_items = \App\Models\InvoiceItem::where('invoice_id', $invoice_id)->get();
            $invoice = \App\Models\Invoice::where('id', $invoice_id)->first();
            $pdfData = ['invoice_number' => $invoice_number, 'invoice_items' => $invoice_items, 'invoice' => $invoice];
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('components.invoice', $pdfData);
            $invoice_folder = storage_path('app/public/invoices');
            if (!is_dir($invoice_folder)) {
                mkdir($invoice_folder, 0777, true);
            }
            $filePath = storage_path('app/public/invoices/invoice_' . $invoice_number . '.pdf');
            file_put_contents($filePath, $pdf->output());
            $points = calculatePoints($transaction_amount, 'CAD');
            $totalPoints = calculateBonus($transaction_amount, 'CAD', $points);
            \App\Models\PointEarn::create([
                'user_id' => $user_id,
                'points' => $totalPoints['points'],
                'bonus_points' => $totalPoints['bonusPoints'],
                'total_points' => $totalPoints['totalPoints'],
            ]);
            $pointData = [
                'totalPoints' => $totalPoints['totalPoints'],
                'name' => $request->first_name . ' ' . $request->last_name,
                'payment_type' => $request->input('payment_type'),
                'totalAmount' => $totalAmount,
            ];
            \Session::put('point_data', $pointData);
            $response['status'] = true;
            $response['message'] = 'Amount submitted successfully. Thank you for your support';
            $response['redirect_url'] = url('support/thankyou');
        } catch (\Exception $e) {
            \Log::error('InvoiceService@completePaymentAction error', ['error' => $e->getMessage()]);
            $response = ['status' => false, 'message' => $e->getMessage()];
        }
        return $response;
    }

    public function sendInvoiceEmail($user, $link)
    {
        try {
            $mailData = ['link' => $link];
            $view = \View::make('emails.invoice-link-email', $mailData);
            $message = $view->render();
            $parameter = [
                'to' => $user->email,
                'to_name' => $user->first_name . ' ' . $user->last_name,
                'message' => $message,
                'subject' => siteSetting('company_name') . ': Payment Link',
                'view' => 'emails.invoice-link-email',
                'data' => $mailData,
            ];
            sendMail($parameter);
        } catch (\Exception $e) {
            \Log::error('InvoiceService@sendInvoiceEmail error', ['error' => $e->getMessage()]);
        }
    }

    // Utility: handle invoice items (create/update/delete)
    public function handleInvoiceItems(Invoice $invoice, array $items)
    {
        // To be implemented: extract repeated invoice item logic
    }

    // Utility: wrap risky operations in try-catch and log errors
    protected function safeExecute(callable $callback, $context = [])
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error('InvoiceService error', array_merge(['error' => $e->getMessage()], $context));
            throw $e;
        }
    }

    // Helper for signature generation
    private function generateSignature($params)
    {
        ksort($params);
        $string = http_build_query($params);
        return hash_hmac('sha256', $string, apiKeys('STRIPE_SECRET'));
    }
} 