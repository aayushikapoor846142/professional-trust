<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\MembershipPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentTransaction;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Checkout\Session;
use App\Models\User;
use App\Models\UserSubscriptionHistory;
use Carbon\Carbon;
use App\Models\SubscriptionInvoiceHistory;
use Illuminate\Support\Facades\Log;

class MembershipPlanController extends Controller
{

    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    /**
     * Display the list of "Membership Plan.
     *
     * @return \Illuminate\View\View
     */
    public function index($type = "subscription")
    {
        try {
            $viewData['pageTitle'] = "Membership Plan List";
            $viewData['type'] = $type;
            return view('admin-panel.04-profile.membership-plans.lists', $viewData);
        } catch (\Exception $e) {
            \Log::error('Error in ' . __METHOD__ . ': ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Retrieves the list of membership plans and returns it as an AJAX response.
     *
     * This method is responsible for fetching all the membership plans from the database,
     * ordering them by ID in descending order, and then rendering the view for the
     * AJAX-based list of membership plans. The rendered view content is then returned
     * as a JSON response.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the rendered view content.
     */
    public function getAjaxList(Request $request)
    {
        try {

         
            $records = MembershipPlan::where('payment_type', $request->type)
                ->with(['activeFeatures.feature'])
                ->orderBy('id', "desc");
                if(getSetting('STRIPE_MODE') == 'TEST'){
                    $records->where('plan_mode','TEST');
                }else{
                     $records->where('plan_mode','LIVE');
                }
                $records = $records->where('status','active')->get();

            $userSubscriptionHistory  = UserSubscriptionHistory::where('user_id', \Auth::user()->id)
                ->where('subscription_status', 'active')
                ->orderBy('id', 'desc')
                ->first();

            $viewData['records'] = $records;
            $viewData['userSubscriptionHistory'] = $userSubscriptionHistory;
            $view = View::make('admin-panel.04-profile.membership-plans.ajax-list', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => \Auth::id(),
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    /**
     * Handles the subscription process for a membership plan.
     *
     * This method is responsible for creating a Stripe customer, attaching a payment method,
     * and creating a subscription for the user. It also saves the subscription history,
     * invoice history, and generates an invoice PDF. Finally, it sends an email with the
     * invoice details to the user.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing the subscription details.
     * @return \Illuminate\Http\RedirectResponse Redirects back to the previous page with a success or error message.
     */

     public function show(Request $request, $id)
    {
        try {
            $plan = MembershipPlan::where('unique_id', $id)->first();
            if ($plan->payment_type == "onetime") {
                $viewData['plan'] = $plan;
                return view('admin-panel.04-profile.membership-plans.onetime', $viewData);
            } else {
                \Stripe\Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
                $user = auth()->user();
                
                // Create customer only if stripe_id doesn't exist
                if (empty($user->stripe_id)) {
                    try {
                        $customer = \Stripe\Customer::create([
                            'email' => $user->email,
                            'name' => $user->name,
                        ]);
                        $user->stripe_id = $customer->id;
                        $user->save();
                    } catch (\Exception $e) {
                        \Log::error('Error creating Stripe customer in ' . __METHOD__ . ': ' . $e->getMessage(), [
                            'user_id' => $user->id,
                        ]);
                    }
                }
                
                $viewData['intent'] = \Stripe\SetupIntent::create([
                    'customer' => $user->stripe_id,
                ]);
                $viewData['pageTitle'] = "Membership Plan";
                $viewData['plan'] = $plan;
                return view('admin-panel.04-profile.membership-plans.subscription', $viewData);
            }
        } catch (\Exception $e) {
            \Log::error('Error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => \Auth::id(),
                'request' => $request->all(),
            ]);
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }
    // public function subscription(Request $request)
    // {
    //     set_time_limit(300);
    //     \DB::beginTransaction();

    //     try {
    //         $user = auth()->user();
    //         $plan = MembershipPlan::find($request->plan);
    //         $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
    //         $customer_id = "";

    //         // Create customer if not exists
    //         if (empty($user->stripe_id)) {
    //             $customer = $stripe->customers->create([
    //                 'name' => $user->name,
    //                 'email' => $user->email,
    //             ]);
    //             $user->stripe_id = $customer->id;
    //             $user->save();
    //             $customer_id = $customer->id;
    //         } else {
    //             $customer = $stripe->customers->retrieve($user->stripe_id);
    //             $customer_id = $customer->id;
    //         }

    //         // Retrieve payment method
    //         $paymentMethod = $stripe->paymentMethods->retrieve($request->payment_method);

    //         // Attach if not already attached
    //         if ($paymentMethod->customer !== $customer_id) {
    //             $stripe->paymentMethods->attach(
    //                 $request->payment_method,
    //                 ['customer' => $customer_id]
    //             );
    //         }

    //         // Set as default
    //         $stripe->customers->update(
    //             $customer_id,
    //             ['invoice_settings' => ['default_payment_method' => $request->payment_method]]
    //         );

    //         // Save user payment info
    //         $user->pm_type = $paymentMethod->card->brand;
    //         $user->pm_last_four = encryptVal($paymentMethod->card->last4);
    //         $user->save();

    //         // Check active subscriptions
    //         $existingSubscription = $stripe->subscriptions->all([
    //             'customer' => $customer_id,
    //             'status' => 'active',
    //         ]);

    //         if (!empty($existingSubscription->data)) {
    //             $subscriptionItemId = $existingSubscription->data[0]->id;

    //             $subscription = $stripe->subscriptions->update(
    //                 $subscriptionItemId,
    //                 [
    //                     'items' => [
    //                         ['price' => $plan->stripe_price],
    //                     ],
    //                     'metadata' => [
    //                         'order_id' => $request->order_id ?? $user->id,
    //                     ],
    //                 ]
    //             );

    //             $invoice = $stripe->invoices->retrieve($subscription->latest_invoice, []);

    //             if ($invoice->status == "paid") {
    //                 $subscriptionHistory = new UserSubscriptionHistory();
    //                 $subscriptionHistory->membership_plans_plan_id = $plan->id;
    //                 $subscriptionHistory->stripe_subscription_id = $subscription->id;
    //                 $subscriptionHistory->subscription_status = $subscription->status;
    //                 $subscriptionHistory->user_id = $user->id;
    //                 $subscriptionHistory->subscription_type = 'membership';
    //                 $subscriptionHistory->save();

    //                 $invoicehistory = new SubscriptionInvoiceHistory();
    //                 $invoicehistory->user_id = $user->id;
    //                 $invoicehistory->subscription_history_id = $subscriptionHistory->id;
    //                 $invoicehistory->stripe_subscription_id = $subscription->id;
    //                 $invoicehistory->stripe_invoice_number = $subscription->latest_invoice;
    //                 $invoicehistory->next_invoice_date = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->toDateTimeString();
    //                 $invoicehistory->stripe_invoice_status = $invoice->status;
    //                 $invoicehistory->save();

    //                 $object = new PaymentTransaction();
    //                 $object->amount = $plan->amount;
    //                 $object->payment_method_id = $request->payment_method;
    //                 $object->status = $invoice->status;
    //                 $object->response = json_encode($subscription);
    //                 $object->user_id = $user->id;
    //                 $object->payment_gateway = 'Stripe';
    //                 $object->transaction_for = 'Subscription';
    //                 $object->other_details = "Subscribe for membership plan $plan->plan_title/$plan->amount";
    //                 $object->save();

    //                 $invoice = new Invoice();
    //                 $invoice->currency = $plan->currency;
    //                 $invoice->tax = 0;
    //                 $invoice->sub_total = $plan->amount;
    //                 $invoice->total_amount = $plan->amount;
    //                 $invoice->discount = 0.00;
    //                 $invoice->payment_status = $object->status;
    //                 $invoice->paid_date = \Carbon\Carbon::createFromTimestamp($subscription->created)->toDateTimeString();
    //                 $invoice->transaction_id = $object->id;
    //                 $invoice->invoice_type = 'subscription';
    //                 $invoice->user_id = $user->id;
    //                 $invoice->save();

    //                 $invoice_id = $invoice->id;
    //                 $invoice_item = new InvoiceItem();
    //                 $invoice_item->invoice_id = $invoice_id;
    //                 $invoice_item->particular = "Subscribe for membership plan $plan->plan_title/$plan->amount";
    //                 $invoice_item->amount = $plan->amount;
    //                 $invoice_item->discount = 0.00;
    //                 $invoice_item->save();

    //                 $invoice_number = $invoice->invoice_number;
    //                 $invoice_items = InvoiceItem::where("invoice_id", $invoice_id)->get();
    //                 $invoice = Invoice::where("id", $invoice_id)->first();

    //                 $pdfData = ['invoice_number' => $invoice_number, "invoice_items" => $invoice_items, "invoice" => $invoice];
    //                 $pdf = Pdf::loadView('pdf.subscription', $pdfData);
    //                 $invoice_folder = storage_path("app/public/invoices");
    //                 if (!is_dir($invoice_folder)) {
    //                     mkdir($invoice_folder, 0777, true);
    //                 }
    //                 $filePath = storage_path('app/public/invoices/invoice_' . $invoice_number . '.pdf');
    //                 file_put_contents($filePath, $pdf->output());

    //                 $mailData = array();
    //                 $view = \View::make('emails.subscription-invoice', $mailData);
    //                 $message = $view->render();

    //                 $parameter = [
    //                     'to' => $user->email,
    //                     'to_name' => $user->first_name . ' ' . $user->last_name,
    //                     'message' => $message,
    //                     'subject' => siteSetting("company_name") . ": Generated Invoice of Subscription Purchase ",
    //                     'view' => 'emails.subscription-invoice',
    //                     'data' => $mailData,
    //                     'invoice_pdf' => $filePath,
    //                 ];
    //                 sendMail($parameter);

    //                 \DB::commit();
    //                 return redirect()->to(baseUrl('my-membership-plans'))->with("success", "Subscription Updated Success");
    //             }

    //             \DB::rollback();
    //             return back()->with('error', 'Payment failed. Please try again.');
    //         } else {
    //             $subscription = $stripe->subscriptions->create([
    //                 'customer' => $customer_id,
    //                 'items' => [
    //                     ['price' => $plan->stripe_price],
    //                 ],
    //                 'default_payment_method' => $request->payment_method,
    //                 'expand' => ['latest_invoice.payment_intent'],
    //             ]);

    //             if ($subscription->latest_invoice->payment_intent->status == "succeeded") {
    //                 $subscriptionHistory = new UserSubscriptionHistory();
    //                 $subscriptionHistory->membership_plans_plan_id = $plan->id;
    //                 $subscriptionHistory->stripe_subscription_id = $subscription->id;
    //                 $subscriptionHistory->subscription_status = $subscription->status;
    //                 $subscriptionHistory->user_id = $user->id;
    //                 $subscriptionHistory->subscription_type = 'membership';
    //                 $subscriptionHistory->save();

    //                 $invoicehistory = new SubscriptionInvoiceHistory();
    //                 $invoicehistory->user_id = $user->id;
    //                 $invoicehistory->subscription_history_id = $subscriptionHistory->id;
    //                 $invoicehistory->stripe_subscription_id = $subscription->id;
    //                 $invoicehistory->stripe_invoice_number = $subscription->latest_invoice->id;
    //                 $invoicehistory->next_invoice_date = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->toDateTimeString();
    //                 $invoicehistory->stripe_invoice_status = $subscription->latest_invoice->status;
    //                 $invoicehistory->save();

    //                 $object = new PaymentTransaction();
    //                 $object->amount = $plan->amount;
    //                 $object->payment_method_id = $request->payment_method;
    //                 $object->status = $subscription->latest_invoice->status;
    //                 $object->response = json_encode($subscription);
    //                 $object->user_id = $user->id;
    //                 $object->payment_gateway = 'Stripe';
    //                 $object->transaction_for = 'Subscription';
    //                 $object->other_details = "Subscribe for membership plan $plan->plan_title/$plan->amount";
    //                 $object->save();

    //                 $invoice = new Invoice();
    //                 $invoice->currency = $plan->currency;
    //                 $invoice->tax = 0;
    //                 $invoice->sub_total = $plan->amount;
    //                 $invoice->total_amount = $plan->amount;
    //                 $invoice->discount = 0.00;
    //                 $invoice->payment_status = $subscription->latest_invoice->status;
    //                 $invoice->paid_date = \Carbon\Carbon::createFromTimestamp($subscription->created)->toDateTimeString();
    //                 $invoice->transaction_id = $object->id;
    //                 $invoice->invoice_type = 'subscription';
    //                 $invoice->user_id = $user->id;
    //                 $invoice->save();

    //                 $invoice_id = $invoice->id;
    //                 $invoice_item = new InvoiceItem();
    //                 $invoice_item->invoice_id = $invoice_id;
    //                 $invoice_item->particular = "Subscribe for membership plan $plan->plan_title/$plan->amount";
    //                 $invoice_item->amount = $plan->amount;
    //                 $invoice_item->discount = 0.00;
    //                 $invoice_item->save();

    //                 $invoice_number = $invoice->invoice_number;
    //                 $invoice_items = InvoiceItem::where("invoice_id", $invoice_id)->get();
    //                 $invoice = Invoice::where("id", $invoice_id)->first();

    //                 $pdfData = ['invoice_number' => $invoice_number, "invoice_items" => $invoice_items, "invoice" => $invoice];
    //                 $pdf = Pdf::loadView('pdf.subscription', $pdfData);
    //                 $invoice_folder = storage_path("app/public/invoices");
    //                 if (!is_dir($invoice_folder)) {
    //                     mkdir($invoice_folder, 0777, true);
    //                 }
    //                 $filePath = storage_path('app/public/invoices/invoice_' . $invoice_number . '.pdf');
    //                 file_put_contents($filePath, $pdf->output());

    //                 $mailData = array();
    //                 $view = \View::make('emails.subscription-invoice', $mailData);
    //                 $message = $view->render();

    //                 $parameter = [
    //                     'to' => $user->email,
    //                     'to_name' => $user->first_name . ' ' . $user->last_name,
    //                     'message' => $message,
    //                     'subject' => siteSetting("company_name") . ": Generated Invoice of Subscription Purchase ",
    //                     'view' => 'emails.subscription-invoice',
    //                     'data' => $mailData,
    //                     'invoice_pdf' => $filePath,
    //                 ];
    //                 sendMail($parameter);

    //                 \DB::commit();
    //                 return redirect()->to(baseUrl('my-membership-plans'))->with("success", "Payment Success");
    //             }

    //             \DB::rollback();
    //             return back()->with('error', 'Payment failed. Please try again.');
    //         }

    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //         \DB::rollBack();
    //         \Log::error('Stripe API error in ' . __METHOD__ . ': ' . $e->getMessage(), [
    //             'user_id' => auth()->id(),
    //             'request' => $request->all(),
    //         ]);
    //         return back()->with('error', 'There was a problem processing your payment. Please try again or contact support.');
    //     } catch (\Exception $e) {
    //         \DB::rollBack();
    //         \Log::error('General error in ' . __METHOD__ . ': ' . $e->getMessage(), [
    //             'user_id' => auth()->id(),
    //             'request' => $request->all(),
    //         ]);
    //         return back()->with('error', 'An unexpected error occurred. Please try again later.');
    //     }
    // }

    // public function subscription(Request $request)
    // {
    //     set_time_limit(300);
    //     \DB::beginTransaction();
    //     try {
    //         $user = auth()->user();
    //         $plan = MembershipPlan::find($request->plan);
    //         $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
            
    //         if (!$user->stripe_id) {
    //             $customer = $stripe->customers->create([
    //                 'name' => $user->name,
    //                 'email' => $user->email,
    //             ]);
    //             $user->stripe_id = $customer->id;
    //             $user->save();
    //         } else {
    //             $customer = $stripe->customers->retrieve($user->stripe_id);
    //         }
            
    //         $stripe->paymentMethods->attach(
    //             $request->payment_method,
    //             ['customer' => $customer->id]
    //         );
    //         $stripe->customers->update(
    //             $customer->id,
    //             ['invoice_settings' => ['default_payment_method' => $request->payment_method]]
    //         );
            
    //         $paymentMethod = $stripe->paymentMethods->retrieve($request->payment_method);
    //         $user->pm_type = $paymentMethod->card->brand;
    //         $user->pm_last_four = encryptVal($paymentMethod->card->last4);
    //         $user->save();
            
    //         // Get all active subscriptions for this customer
    //         $existingSubscriptions = $stripe->subscriptions->all([
    //             'customer' => $customer->id,
    //             'status' => 'active',
    //         ]);
    //         return $existingSubscriptions;
    //         if ($existingSubscriptions->data) {
    //             \Log::info('Found ' . count($existingSubscriptions->data) . ' active subscriptions for customer ' . $customer->id);
                
    //             // Cancel all existing active subscriptions first
    //             foreach ($existingSubscriptions->data as $existingSubscription) {
    //                 try {
    //                     \Log::info('Canceling subscription: ' . $existingSubscription->id);
                        
    //                     $stripe->subscriptions->cancel($existingSubscription->id, [
    //                         'proration_behavior' => 'none'
    //                     ]);
                        
    //                     // Update local subscription history to canceled
    //                     UserSubscriptionHistory::where('user_id', \Auth::user()->id)
    //                         ->where('stripe_subscription_id', $existingSubscription->id)
    //                         ->where('subscription_status', 'active')
    //                         ->update(['subscription_status' => 'canceled']);
                            
    //                     \Log::info('Successfully canceled subscription: ' . $existingSubscription->id);
                            
    //                 } catch (\Exception $e) {
    //                     \Log::error('Error canceling subscription ' . $existingSubscription->id . ': ' . $e->getMessage());
    //                     // Continue with other subscriptions even if one fails
    //                 }
    //             }
                
    //             // Wait longer for Stripe to process the cancellations
    //             sleep(3);
                
    //             // Double-check that all subscriptions are canceled
    //             $remainingSubscriptions = $stripe->subscriptions->all([
    //                 'customer' => $customer->id,
    //                 'status' => 'active',
    //             ]);
                
    //             if ($remainingSubscriptions->data) {
    //                 \Log::warning('Still found ' . count($remainingSubscriptions->data) . ' active subscriptions after cancellation attempt');
    //                 // Force cancel any remaining active subscriptions
    //                 foreach ($remainingSubscriptions->data as $remainingSubscription) {
    //                     try {
    //                         $stripe->subscriptions->cancel($remainingSubscription->id, [
    //                             'proration_behavior' => 'none'
    //                         ]);
    //                         \Log::info('Force canceled remaining subscription: ' . $remainingSubscription->id);
    //                     } catch (\Exception $e) {
    //                         \Log::error('Error force canceling subscription ' . $remainingSubscription->id . ': ' . $e->getMessage());
    //                     }
    //                 }
    //                 sleep(2);
    //             }
    //         }
            
    //         // Final check - ensure no active subscriptions remain
    //         $finalCheck = $stripe->subscriptions->all([
    //             'customer' => $customer->id,
    //             'status' => 'active',
    //         ]);
            
    //         if ($finalCheck->data) {
    //             \Log::error('Cannot create new subscription - still have active subscriptions: ' . count($finalCheck->data));
    //             \DB::rollback();
    //             return back()->with('error', 'Unable to process subscription. Please try again or contact support.');
    //         }
            
    //         // Create a new subscription
    //         $subscription = $stripe->subscriptions->create([
    //             'customer' => $customer->id,
    //             'items' => [
    //                 ['price' => $plan->stripe_price],
    //             ],
    //             'default_payment_method' => $request->payment_method,
    //             'expand' => ['latest_invoice.payment_intent'],
    //             'metadata' => [
    //                 'order_id' => $request->order_id ?? $user->id,
    //                 'plan_title' => $plan->plan_title,
    //                 'user_id' => $user->id,
    //             ],
    //         ]);

    //         if ($subscription->latest_invoice->payment_intent->status == "succeeded") {
    //             // Create subscription history
    //             $subscriptionHistory = new UserSubscriptionHistory();
    //             $subscriptionHistory->membership_plans_plan_id = $plan->id;
    //             $subscriptionHistory->stripe_subscription_id = $subscription->id;
    //             $subscriptionHistory->subscription_status = $subscription->status;
    //             $subscriptionHistory->user_id = \Auth::user()->id;
    //             $subscriptionHistory->subscription_type = 'membership';
    //             $subscriptionHistory->save();

    //             // Create invoice history
    //             $invoicehistory = new SubscriptionInvoiceHistory();
    //             $invoicehistory->user_id = \Auth::user()->id;
    //             $invoicehistory->subscription_history_id = $subscriptionHistory->id;
    //             $invoicehistory->stripe_subscription_id = $subscription->id;
    //             $invoicehistory->stripe_invoice_number = $subscription->latest_invoice->id;
    //             $invoicehistory->next_invoice_date = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->toDateTimeString();
    //             $invoicehistory->stripe_invoice_status = $subscription->latest_invoice->status;
    //             $invoicehistory->save();

    //             // Create payment transaction
    //             $object = new PaymentTransaction();
    //             $object->amount = $plan->amount;
    //             $object->payment_method_id = $request->payment_method;
    //             $object->status = $subscription->latest_invoice->status;
    //             $object->response = json_encode($subscription);
    //             $object->user_id = \Auth::user()->id;
    //             $object->payment_gateway = 'Stripe';
    //             $object->transaction_for = 'Subscription';
    //             $object->other_details = "Subscribe for membership plan $plan->plan_title/$plan->amount";
    //             $object->save();

    //             // Create invoice
    //             $invoice = new Invoice();
    //             $invoice->currency = $plan->currency;
    //             $invoice->tax = 0;
    //             $invoice->sub_total = $plan->amount;
    //             $invoice->total_amount = $plan->amount;
    //             $invoice->discount = 0.00;
    //             $invoice->payment_status = $subscription->latest_invoice->status;
    //             $invoice->paid_date = \Carbon\Carbon::createFromTimestamp($subscription->created)->toDateTimeString();
    //             $invoice->transaction_id = $object->id;
    //             $invoice->invoice_type = 'subscription';
    //             $invoice->user_id = \Auth::user()->id;
    //             $invoice->save();

    //             $invoice_id = $invoice->id;
    //             $invoice_item = new InvoiceItem();
    //             $invoice_item->invoice_id = $invoice_id;
    //             $invoice_item->particular = "Subscribe for membership plan $plan->plan_title/$plan->amount";
    //             $invoice_item->amount = $plan->amount;
    //             $invoice_item->discount = 0.00;
    //             $invoice_item->save();

    //             $invoice_number = $invoice->invoice_number;
    //             $invoice_items = InvoiceItem::where("invoice_id", $invoice_id)->get();
    //             $invoice = Invoice::where("id", $invoice_id)->first();
    //             $pdfData = ['invoice_number' => $invoice_number, "invoice_items" => $invoice_items, "invoice" => $invoice];

    //             $pdf = Pdf::loadView('pdf.subscription', $pdfData);
    //             $invoice_folder = storage_path("app/public/invoices");
    //             if (!is_dir($invoice_folder)) {
    //                 mkdir($invoice_folder, 0777, true);
    //             }
    //             $filePath = storage_path('app/public/invoices/invoice_' . $invoice_number . '.pdf');
    //             file_put_contents($filePath, $pdf->output());

    //             $mailData = array();
    //             $view = \View::make('emails.subscription-invoice', $mailData);
    //             $message = $view->render();

    //             $parameter = [
    //                 'to' => $user->email,
    //                 'to_name' => $user->first_name . ' ' . $user->last_name,
    //                 'message' => $message,
    //                 'subject' => siteSetting("company_name") . ": Generated Invoice of Subscription Purchase ",
    //                 'view' => 'emails.subscription-invoice',
    //                 'data' => $mailData,
    //                 'invoice_pdf' => $filePath,
    //             ];
    //             sendMail($parameter);

    //             \DB::commit();
                
    //             $message = $existingSubscriptions->data ? "Subscription Updated Success" : "Payment Success";
    //             return redirect()->to(baseUrl('my-membership-plans'))->with("success", $message);
    //         } else {
    //             \DB::rollback();
    //             return back()->with('error', 'Payment failed. Please try again.');
    //         }

    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //         \DB::rollBack();
    //         \Log::error('Stripe API error in ' . __METHOD__ . ': ' . $e->getMessage(), [
    //             'user_id' => auth()->id(),
    //             'request' => $request->all(),
    //         ]);
    //         return back()->with('error', 'There was a problem processing your payment. Please try again or contact support.');
    //     } catch (\Exception $e) {
    //         \DB::rollBack();
    //         \Log::error('General error in ' . __METHOD__ . ': ' . $e->getMessage(), [
    //             'user_id' => auth()->id(),
    //             'request' => $request->all(),
    //         ]);
    //         return back()->with('error', 'An unexpected error occurred. Please try again later.');
    //     }
    // }
    public function subscription(Request $request)
{
    set_time_limit(300);
    \DB::beginTransaction();
    try {
        $user = auth()->user();
        $plan = MembershipPlan::findOrFail($request->plan);
        $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));

        // Create customer if not exists
        if (empty($user->stripe_id)) {
            $customer = $stripe->customers->create([
                'name' => $user->name,
                'email' => $user->email,
            ]);
            $user->stripe_id = $customer->id;
            $user->save();
        }

        $customer = $stripe->customers->retrieve($user->stripe_id);

        // Attach payment method
        $stripe->paymentMethods->attach(
            $request->payment_method,
            ['customer' => $customer->id]
        );

        // Update customer's default payment method
        $stripe->customers->update(
            $customer->id,
            ['invoice_settings' => ['default_payment_method' => $request->payment_method]]
        );

        // Save payment method details
        $paymentMethod = $stripe->paymentMethods->retrieve($request->payment_method);
        $user->pm_type = $paymentMethod->card->brand;
        $user->pm_last_four = encryptVal($paymentMethod->card->last4);
        $user->save();

        // Check for existing subscriptions
        $existingSubscriptions = $stripe->subscriptions->all([
            'customer' => $customer->id,
            'status' => 'active',
        ]);

        if (!empty($existingSubscriptions->data)) {
            // Get the current subscription
            $currentSubscription = $existingSubscriptions->data[0];
            
            // Debug: Log current subscription state
            \Log::info('Current subscription state before update:', [
                'subscription_id' => $currentSubscription->id,
                'status' => $currentSubscription->status,
                'items_count' => count($currentSubscription->items->data),
                'items' => array_map(function($item) {
                    return [
                        'id' => $item->id,
                        'price_id' => $item->price->id,
                        'quantity' => $item->quantity
                    ];
                }, $currentSubscription->items->data)
            ]);
            
            // Get current plan details for comparison
            $currentPlan = null;
            if (!empty($currentSubscription->items->data)) {
                $currentPriceId = $currentSubscription->items->data[0]->price->id;
                $currentPlan = MembershipPlan::where('stripe_price', $currentPriceId)->first();
            }
            
            // Check if user is trying to "upgrade" to the same plan
            if ($currentPlan && $currentPlan->stripe_price === $plan->stripe_price) {
                \DB::rollback();
                return back()->with('error', 'You are already subscribed to this plan.');
            }
            
            // Log proration details for transparency
            $this->logProrationDetails($currentSubscription, $plan, $user);
            
            // Get the first subscription item (there should only be one for membership plans)
            $currentItem = $currentSubscription->items->data[0];
            
            // Update the subscription item to the new price - this replaces the old item
            \Log::info('Updating subscription item:', [
                'subscription_id' => $currentSubscription->id,
                'item_id' => $currentItem->id,
                'from_price' => $currentItem->price->id,
                'to_price' => $plan->stripe_price,
                'from_plan' => $currentPlan ? $currentPlan->plan_title : 'Unknown',
                'to_plan' => $plan->plan_title
            ]);
            
            // Update subscription with immediate proration invoice
            $subscription = $stripe->subscriptions->update(
                $currentSubscription->id,
                [
                    'items' => [
                        [
                            'id' => $currentItem->id,
                            'price' => $plan->stripe_price,
                        ],
                    ],
                    'proration_behavior' => 'always_invoice', // Force immediate invoice generation
                    'metadata' => [
                        'order_id' => $request->order_id ?? $user->id,
                        'active_plan' => $plan->plan_title,
                        'product_name' => $plan->plan_title,
                        'active_product' => $plan->plan_title,
                        'upgraded_from' => $currentPlan ? $currentPlan->plan_title : 'Unknown',
                        'upgrade_date' => now()->toISOString(),
                    ],
                ]
            );
            
            // Update customer metadata
            $stripe->customers->update(
                $customer->id,
                [
                    'metadata' => [
                        'active_product' => $plan->plan_title,
                        'current_plan' => $plan->plan_title,
                    ],
                ]
            );
            
            // Retrieve the updated subscription to get the latest invoice
            $updatedSubscription = $stripe->subscriptions->retrieve($subscription->id);
            
            \Log::info('Subscription updated with always_invoice:', [
                'subscription_id' => $updatedSubscription->id,
                'items_count' => count($updatedSubscription->items->data),
                'items' => array_map(function($item) {
                    return [
                        'id' => $item->id,
                        'price_id' => $item->price->id,
                        'quantity' => $item->quantity
                    ];
                }, $updatedSubscription->items->data),
                'latest_invoice' => $updatedSubscription->latest_invoice,
                'proration_behavior' => 'always_invoice'
            ]);

            // Handle any immediate invoice for proration
            if ($updatedSubscription->latest_invoice) {
                $invoice = $stripe->invoices->retrieve($updatedSubscription->latest_invoice);
                
                \Log::info('Latest invoice details:', [
                    'invoice_id' => $invoice->id,
                    'status' => $invoice->status,
                    'amount_due' => $invoice->amount_due,
                    'amount_paid' => $invoice->amount_paid,
                    'total' => $invoice->total
                ]);
                
                if ($invoice->status == "paid") {
                    // Invoice is already paid (proration handled automatically)
                    $this->saveSubscriptionDetails($user, $plan, $updatedSubscription, $invoice, $request->payment_method);
                    \DB::commit();
                    return redirect()->to(baseUrl('my-membership-plans'))->with("success", "Subscription Updated Successfully with Proration");
                } else if ($invoice->status == "open" && $invoice->amount_due > 0) {
                    // If there's an open invoice with amount due, pay it
                    try {
                        $paidInvoice = $stripe->invoices->pay($invoice->id);
                        $this->saveSubscriptionDetails($user, $plan, $updatedSubscription, $paidInvoice, $request->payment_method);
                        \DB::commit();
                        return redirect()->to(baseUrl('my-membership-plans'))->with("success", "Subscription Updated Successfully with Proration Payment");
                    } catch (\Exception $e) {
                        \DB::rollback();
                        \Log::error('Failed to pay proration invoice: ' . $e->getMessage());
                        return back()->with('error', 'Failed to process proration payment. Please try again.');
                    }
                } else {
                    // No additional payment needed (credit applied or no proration)
                    $this->saveSubscriptionDetails($user, $plan, $updatedSubscription, $invoice, $request->payment_method);
                    \DB::commit();
                    return redirect()->to(baseUrl('my-membership-plans'))->with("success", "Subscription Updated Successfully - Proration Applied as Credit");
                }
            } else {
                // No invoice created (shouldn't happen with proration, but handle gracefully)
                $this->saveSubscriptionDetails($user, $plan, $updatedSubscription, null, $request->payment_method);
                \DB::commit();
                return redirect()->to(baseUrl('my-membership-plans'))->with("success", "Subscription Updated Successfully");
            }
            
        } else {
            // Create a new subscription
            $subscription = $stripe->subscriptions->create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $plan->stripe_price],
                ],
                'default_payment_method' => $request->payment_method,
                'expand' => ['latest_invoice.payment_intent'],
                'metadata' => [
                    'order_id' => $request->order_id ?? $user->id,
                    'active_plan' => $plan->plan_title,
                    'product_name' => $plan->plan_title,
                    'active_product' => $plan->plan_title,
                    'created_date' => now()->toISOString(),
                ],
            ]);

            if ($subscription->latest_invoice->payment_intent->status == "succeeded") {
                $this->saveSubscriptionDetails($user, $plan, $subscription, $subscription->latest_invoice, $request->payment_method);
                \DB::commit();
                return redirect()->to(baseUrl('my-membership-plans'))->with("success", "Payment Success");
            }

            \DB::rollback();
            return back()->with('error', 'Payment failed for new subscription');
        }

    } catch (\Stripe\Exception\ApiErrorException $e) {
        \DB::rollBack();
        \Log::error('Stripe API error in subscription: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'request' => $request->all(),
            'stripe_error_type' => get_class($e),
            'stripe_error_code' => $e->getStripeCode(),
        ]);
        return back()->with('error', 'There was a problem processing your payment. Please try again or contact support.');
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('General error in subscription: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'request' => $request->all(),
            'error_line' => $e->getLine(),
            'error_file' => $e->getFile(),
        ]);
        return back()->with('error', 'An unexpected error occurred. Please try again later.');
    }
}
    /**
     * Calculate and log proration details for subscription upgrades
     */
    protected function logProrationDetails($currentSubscription, $newPlan, $user)
    {
        try {
            $currentPeriodStart = $currentSubscription->current_period_start;
            $currentPeriodEnd = $currentSubscription->current_period_end;
            $now = time();
            
            // Calculate remaining days in current period
            $totalPeriodDays = ($currentPeriodEnd - $currentPeriodStart) / (24 * 60 * 60);
            $elapsedDays = ($now - $currentPeriodStart) / (24 * 60 * 60);
            $remainingDays = $totalPeriodDays - $elapsedDays;
            
            // Get current plan details
            $currentPriceId = $currentSubscription->items->data[0]->price->id;
            $currentPlan = MembershipPlan::where('stripe_price', $currentPriceId)->first();
            
            \Log::info('Proration calculation details:', [
                'user_id' => $user->id,
                'current_plan' => $currentPlan ? $currentPlan->plan_title : 'Unknown',
                'new_plan' => $newPlan->plan_title,
                'current_plan_amount' => $currentPlan ? $currentPlan->amount : 0,
                'new_plan_amount' => $newPlan->amount,
                'total_period_days' => round($totalPeriodDays, 2),
                'elapsed_days' => round($elapsedDays, 2),
                'remaining_days' => round($remainingDays, 2),
                'proration_percentage' => round(($remainingDays / $totalPeriodDays) * 100, 2) . '%',
                'current_period_start' => date('Y-m-d H:i:s', $currentPeriodStart),
                'current_period_end' => date('Y-m-d H:i:s', $currentPeriodEnd),
                'upgrade_date' => date('Y-m-d H:i:s', $now),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error calculating proration details: ' . $e->getMessage());
        }
    }

    protected function saveSubscriptionDetails($user, $plan, $subscription, $invoice, $paymentMethodId)
    {
        // First, deactivate any existing active subscriptions for this user
        UserSubscriptionHistory::where('user_id', $user->id)
            ->where('subscription_type', 'membership')
            ->where('subscription_status', 'active')
            ->update(['subscription_status' => 'inactive']);
        
        // Save subscription history
        $subscriptionHistory = new UserSubscriptionHistory();
        $subscriptionHistory->membership_plans_plan_id = $plan->id;
        $subscriptionHistory->stripe_subscription_id = $subscription->id;
        $subscriptionHistory->subscription_status = $subscription->status;
        $subscriptionHistory->user_id = $user->id;
        $subscriptionHistory->subscription_type = 'membership';
        $subscriptionHistory->save();

        // Determine if this is an upgrade scenario
        $isUpgrade = false;
        $upgradedFrom = null;
        if ($subscription->metadata && isset($subscription->metadata->upgraded_from)) {
            $isUpgrade = true;
            $upgradedFrom = $subscription->metadata->upgraded_from;
        }

        // Save invoice history
        $invoicehistory = new SubscriptionInvoiceHistory();
        $invoicehistory->user_id = $user->id;
        $invoicehistory->subscription_history_id = $subscriptionHistory->id;
        $invoicehistory->stripe_subscription_id = $subscription->id;
        $invoicehistory->stripe_invoice_number = is_object($invoice) ? $invoice->id : ($invoice ?? 'proration_credit');
        $invoicehistory->next_invoice_date = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->toDateTimeString();
        $invoicehistory->stripe_invoice_status = is_object($invoice) ? $invoice->status : 'paid';
        $invoicehistory->save();

        // Calculate transaction amount based on scenario
        $transactionAmount = $plan->amount;
        $transactionDetails = "Subscribe for membership plan $plan->plan_title/$plan->amount";
        
        if ($isUpgrade && is_object($invoice)) {
            // For upgrades with immediate proration payment
            $transactionAmount = $invoice->amount_paid / 100; // Convert from cents
            $transactionDetails = "Upgrade from $upgradedFrom to $plan->plan_title (Proration: $" . number_format($transactionAmount, 2) . ")";
        } elseif ($isUpgrade && !is_object($invoice)) {
            // For upgrades with proration credit (no immediate payment)
            $transactionAmount = 0;
            $transactionDetails = "Upgrade from $upgradedFrom to $plan->plan_title (Proration applied as credit)";
        }

        // Save payment transaction
        $object = new PaymentTransaction();
        $object->amount = $transactionAmount;
        $object->payment_method_id = $paymentMethodId;
        $object->status = is_object($invoice) ? $invoice->status : 'paid';
        $object->response = json_encode($subscription);
        $object->user_id = $user->id;
        $object->payment_gateway = 'Stripe';
        $object->transaction_for = $isUpgrade ? 'Subscription Upgrade' : 'Subscription';
        $object->other_details = $transactionDetails;
        $object->save();

        // Create invoice only if there's an actual payment
        if ($transactionAmount > 0 || is_object($invoice)) {
            $invoice = new Invoice();
            $invoice->currency = $plan->currency;
            $invoice->tax = 0;
            $invoice->sub_total = $transactionAmount;
            $invoice->total_amount = $transactionAmount;
            $invoice->discount = 0.00;
            $invoice->payment_status = $object->status;
            $invoice->paid_date = \Carbon\Carbon::createFromTimestamp($subscription->created)->toDateTimeString();
            $invoice->transaction_id = $object->id;
            $invoice->invoice_type = $isUpgrade ? 'subscription_upgrade' : 'subscription';
            $invoice->user_id = $user->id;
            $invoice->save();

            // Create invoice item
            $invoice_item = new InvoiceItem();
            $invoice_item->invoice_id = $invoice->id;
            $invoice_item->particular = $transactionDetails;
            $invoice_item->amount = $transactionAmount;
            $invoice_item->discount = 0.00;
            $invoice_item->save();

            // Generate and save PDF invoice
            $invoice_number = $invoice->invoice_number;
            $invoice_items = InvoiceItem::where("invoice_id", $invoice->id)->get();
            $invoice = Invoice::where("id", $invoice->id)->first();
            $pdfData = ['invoice_number' => $invoice_number, "invoice_items" => $invoice_items, "invoice" => $invoice];

            $pdf = Pdf::loadView('pdf.subscription', $pdfData);
            $invoice_folder = storage_path("app/public/invoices");
            if (!is_dir($invoice_folder)) {
                mkdir($invoice_folder, 0777, true);
            }
            $filePath = storage_path('app/public/invoices/invoice_' . $invoice_number . '.pdf');
            file_put_contents($filePath, $pdf->output());

            // Send email with invoice
            $mailData = array();
            $view = \View::make('emails.subscription-invoice', $mailData);
            $message = $view->render();

            $parameter = [
                'to' => $user->email,
                'to_name' => $user->first_name . ' ' . $user->last_name,
                'message' => $message,
                'subject' => siteSetting("company_name") . ": Generated Invoice of " . ($isUpgrade ? "Subscription Upgrade" : "Subscription Purchase"),
                'view' => 'emails.subscription-invoice',
                'data' => $mailData,
                'invoice_pdf' => $filePath,
            ];
            sendMail($parameter);
        }
    }
    /**
     * Retrieves the user's membership plan information and renders the corresponding view.
     *
     * This method is used to fetch the user's current membership plan details from the database and
     * render the 'admin-panel.04-profile.user-membership-plans.lists' view with the retrieved data.
     *
     * @return \Illuminate\Contracts\View\View The rendered view containing the user's membership plan information.
     */
    public function userMembershipIndex()
    {
        try {
            $viewData['pageTitle'] = "My Membership Plan";
            $viewData['professional_id'] =\Auth::user()->unique_id;
            return view('admin-panel.04-profile.user-membership-plans.lists', $viewData);
        } catch (\Exception $e) {
            \Log::error('Error in ' . __METHOD__ . ': ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Retrieves the user's subscription history and renders the corresponding view.
     *
     * This method is used to fetch the user's subscription history from the database and
     * render the 'admin-panel.04-profile.user-membership-plans.ajax-list' view with the retrieved data.
     * The method is designed to be called via AJAX to update the user's membership plan
     * information on the front-end.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the rendered view contents.
     */

    public function userMembershipAjaxList(Request $request)
    {
        try {
             $user = User::where('unique_id', $request->input('professional_id'))
                ->first();
                $records  = UserSubscriptionHistory::where('user_id', $user->id)
                ->where('subscription_type' ,'membership')
                ->orderBy('id', 'desc')
                ->first();

            $viewData['record'] = $records;
            $view = View::make('admin-panel.04-profile.user-membership-plans.ajax-list', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => \Auth::id(),
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

     public function subscriptionUpcomingInvoiceAjax(Request $request)
    {
    try {
        $user = User::where('unique_id', $request->input('professional_id'))->first();

        $record = UserSubscriptionHistory::where('user_id', $user->id)
            ->where('subscription_type', 'membership')
            ->orderBy('id', 'desc')
            ->first();
         $currentSubscription = null;
        $nextInvoiceData = null;
        if ($record) {
            $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
            try {
                $currentSubscription = $stripe->subscriptions->retrieve($record->stripe_subscription_id, []);
                $nextInvoiceData = $stripe->invoices->upcoming([
                    'customer'     => $user->stripe_id,
                    'subscription' => $record->stripe_subscription_id,
                ]);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                \Log::error('Stripe Error: ' . $e->getMessage());
            }
        }
        $viewData['currentSubscription'] = $currentSubscription;
        $viewData['nextInvoiceData'] = $nextInvoiceData;
        $viewData['record'] = $record;

        $view = View::make('admin-panel.04-profile.user-membership-plans.subscription-upcoming-invoice', $viewData);
        return response()->json(['contents' => $view->render()]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Something went wrong'], 500);
    }
    }


    /**
     * Cancels the user's subscription.
     *
     * This method is used to cancel the user's active subscription with Stripe. It retrieves the
     * subscription ID from the provided request parameter, and then uses the Stripe API to cancel
     * the subscription. If the cancellation is successful, it redirects the user back with a
     * success message. Otherwise, it redirects with an error message.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @param string $id The subscription ID to be canceled.
     * @return \Illuminate\Http\RedirectResponse The redirect response with the appropriate success or error message.
     */
    public function cancelSubscription(Request $request, $id)
    {
        try {
            $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
            $cancelSubscription = $stripe->subscriptions->cancel($id, []);
            $Subscription = UserSubscriptionHistory::where("stripe_subscription_id", $id)->first();

            if ($cancelSubscription->status == 'canceled') {
                $Subscription->update([
                    'subscription_status' => 'cancelled',
                ]);
                \Auth::user()->update(['stripe_id' => null]);

                return redirect()->back()->with('success', 'Subscription canceled successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to cancel subscription.');
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => \Auth::id(),
                'request' => $request->all(),
            ]);
            return redirect()->back()->with('error', 'There was a problem cancelling your subscription. Please try again or contact support.');
        } catch (\Exception $e) {
            \Log::error('General error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => \Auth::id(),
                'request' => $request->all(),
            ]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function addCardDetails(Request $request, $id)
    {
        try {
            $user = auth()->user();
            \Stripe\Stripe::setApiKey(apiKeys('STRIPE_SECRET'));
            $viewData['intent'] = \Stripe\SetupIntent::create([
                'customer' => $user->stripe_id,
            ]);
            $viewData['record'] = UserSubscriptionHistory::where('unique_id', $id)->first();
            $viewData['user'] = $user;
            $viewData['pageTitle'] = "Add Card Details";
            return view('admin-panel.04-profile.user-membership-plans.cards.add', $viewData);
        } catch (\Exception $e) {
            \Log::error('Error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request' => $request->all(),
            ]);
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function saveCardDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cardholder' => 'required|max:255',
        ], [
            'cardholder.required' => 'The name on the card is required.',
            'cardholder.max' => 'The name on the card must not exceed 255 characters.',
        ]);
        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = [];
            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }
        try {
            $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
            $paymentMethod = $stripe->paymentMethods->retrieve($request->payment_method, []);
            $response = $paymentMethod->attach(['customer' => $request->customer_id]);
            $customer = $stripe->customers->update(
                $request->customer_id,
                params: [
                    'invoice_settings' => [
                        'default_payment_method' => $request->payment_method,
                    ],
                ]
            );
            $stripe->subscriptions->update(
                $request->subscription_id,
                [
                    'default_payment_method' => $request->payment_method,
                ]
            );
            $response['status'] = true;
            $response['redirect_back'] = baseUrl('my-membership-plans/cards');
            $response['message'] = "Record updated successfully";
            return response()->json($response);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request' => $request->all(),
            ]);
            return response()->json(['status' => false, 'message' => 'There was a problem saving your card details. Please try again or contact support.'], 500);
        } catch (\Exception $e) {
            \Log::error('General error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request' => $request->all(),
            ]);
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    /**
     * Retrieves the list of payment methods for the authenticated user and renders the view for the payment method list page.
     *
     * This method is responsible for fetching the authenticated user and passing the necessary data to the view for rendering the payment method list page.
     *
     * @return \Illuminate\View\View The view for the payment method list page.
     */
  
    public function userCardList()
    {
        try {
            $user = auth()->user();
            $viewData['record'] = UserSubscriptionHistory::where('user_id', $user->id)->first();
            $viewData['pageTitle'] = "Payment Method List";
            $viewData['user'] = $user;
            return view('admin-panel.04-profile.user-membership-plans.cards.lists', $viewData);
        } catch (\Exception $e) {
            \Log::error('Error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Retrieves the list of membership plans and returns it as an AJAX response.
     *
     * This method is responsible for fetching all the membership plans from the database,
     * ordering them by ID in descending order, and then rendering the view for the
     * AJAX-based list of membership plans. The rendered view content is then returned
     * as a JSON response.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the rendered view content.
     */
    public function useCardAjaxList(Request $request)
    {
        try {
            $user = auth()->user();
            $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
            $customer = $stripe->customers->retrieve($user->stripe_id);
            if (!empty($customer->invoice_settings->default_payment_method)) {
                $defaultPaymentMethod = $stripe->paymentMethods->retrieve(
                    $customer->invoice_settings->default_payment_method
                );
            } else {
                $defaultPaymentMethod = null;
            }
            $perPage = 10; 
            $page = request('page', 1);
            $offset = ($page - 1) * $perPage;
            $allRecords = $stripe->customers->allPaymentMethods($user->stripe_id);
            $totalRecords = count($allRecords->data);
            $lastPage = ceil($totalRecords / $perPage);
            $records = array_slice($allRecords->data, $offset, $perPage);
            $viewData['records'] = $records;
            $viewData['defaultPaymentMethod'] = $defaultPaymentMethod;
            $view = View::make('admin-panel.04-profile.user-membership-plans.cards.ajax-list', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['current_page'] = $page;
            $response['last_page'] = $lastPage;
            $response['total_records'] = $totalRecords;
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function removeCardDetails($id)
    {
        try {
            $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
            $stripe->paymentMethods->detach($id, []);
            return redirect()->back()->with("success", "Payment Method Removed successfully");
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return redirect()->back()->withErrors(["error" => "There was a problem removing your payment method. Please try again or contact support."]);
        } catch (\Exception $e) {
            \Log::error('General error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return redirect()->back()->withErrors(["error" => "An unexpected error occurred. Please try again later."]);
        }
    }

    /**
     * Sets the default payment method for the authenticated user.
     *
     * This method retrieves the specified payment method from Stripe, attaches it to the
     * user's Stripe customer account, and updates the customer's default payment method.
     * If the Stripe API operation is successful, the user is redirected back with a
     * success message. If there is a Stripe API error, the user is redirected back with
     * an error message.
     *
     * @param string $id The ID of the payment method to set as the default.
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function makeDefaultCard($id)
    {
        try {
            $user = auth()->user();
            $stripe = new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
            $paymentMethod = $stripe->paymentMethods->retrieve($id, []);
            $paymentMethod->attach(['customer' => $user->stripe_id]);
            $stripe->customers->update(
                $user->stripe_id,
                params: [
                    'invoice_settings' => [
                        'default_payment_method' => $id,
                    ],
                ]
            );
            return redirect()->back()->with("success", "Default Payment Method Set successfully");
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return redirect()->back()->withErrors(["error" => "There was a problem setting your default payment method. Please try again or contact support."]);
        } catch (\Exception $e) {
            \Log::error('General error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return redirect()->back()->withErrors(["error" => "An unexpected error occurred. Please try again later."]);
        }
    }
}
