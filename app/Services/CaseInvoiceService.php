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

class CaseInvoiceService
{
    // Save a new invoice and related items
    public function saveInvoice(Request $request)
    {
        $response = [];
        try {
            $validator = \Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string|max:255',
                'items.*.amount' => 'required|numeric|min:1',
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
            $case = CaseWithProfessionals::where('unique_id',$request->case_id)->first();
            $user = \App\Models\User::where('id', $case->client_id)->first();

            $invoice = new \App\Models\Invoice();
            $invoice->unique_id = randomNumber();
            $invoice->invoice_number = $latest ? $latest->invoice_number + 1 : 1;
            $invoice->tax = $request->tax;
            $invoice->sub_total = $request->sub_total;
            $invoice->total_amount = $request->total_amount;
            $invoice->currency = $request->currency;
            $invoice->user_id = $user->id;
            $invoice->first_name = $user->first_name;
            $invoice->last_name = $user->last_name;
            $invoice->email = $user->email;
            $invoice->country_code = $user->country_code;
            $invoice->phone_no = $user->phone_no;
            $invoice->invoice_type = 'post_case';
            $invoice->payment_status = 'pending';
            $invoice->added_by = auth()->user()->id;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->due_date = $request->due_date;
            $invoice->notes = $request->note_terms;
            $invoice->bill_to = $request->bill_to;
            $invoice->bill_from = $request->bill_from;
            $invoice->discount = $request->total_discount ?? 0;
            $invoice->discount_type = $request->total_discount_type;
            $invoice->reference_id = $case->id;
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
            $response['redirect_back'] = baseUrl('case-with-professionals/invoices/'.$request->case_id);
            $response['message'] = "Record added successfully";
        } catch (\Exception $e) {
            \Log::error('InvoiceService@saveInvoice error', ['error' => $e->getMessage()]);
            $response['status'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

      // Helper for signature generation
    private function generateSignature($params)
    {
        ksort($params);
        $string = http_build_query($params);
        return hash_hmac('sha256', $string, apiKeys('STRIPE_SECRET'));
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

            // $user = \App\Models\User::where('id', $request->user_id)->first();
            $invoice = \App\Models\Invoice::where('unique_id', $id)->first();
            $invoice->tax = $request->tax;
            $invoice->sub_total = $request->sub_total;
            $invoice->total_amount = $request->total_amount;
            $invoice->currency = $request->currency;
            // $invoice->user_id = $request->user_id;
            // $invoice->first_name = $user->first_name;
            // $invoice->last_name = $user->last_name;
            // $invoice->email = $user->email;
            // $invoice->country_code = $user->country_code;
            // $invoice->phone_no = $user->phone_no;
            // $invoice->invoice_type = 'global';
            // $invoice->payment_status = 'pending';
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

            $case = CaseWithProfessionals::where('id',$invoice->reference_id)->first();
            \App\Models\InvoiceItem::where('invoice_id', $invoice->id)->whereNotIn('id', $inv_items)->delete();
            $response['status'] = true;
            $response['redirect_back'] = baseUrl('case-with-professionals/invoices/'.$case->unique_id);
            $response['message'] = "Record added successfully";
        } catch (\Exception $e) {
            \Log::error('InvoiceService@updateInvoice error', ['error' => $e->getMessage()]);
            $response['status'] = false;
            $response['message'] = $e->getMessage();
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
} 