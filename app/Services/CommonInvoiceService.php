<?php

namespace App\Services;

use Illuminate\Http\Request;
use Stripe\Customer;
use Stripe\PaymentIntent;

class CommonInvoiceService
{
    public function extractUserData(Request $request)
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

    public function createOrUpdateStripeCustomer($user_data, $request, $paymentMethodId)
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

    public function attachPaymentMethod($paymentMethodId, $stripe_customer_id)
    {
        $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
        $paymentMethod->attach(['customer' => $stripe_customer_id]);
        \Stripe\Customer::update($stripe_customer_id, [
            'invoice_settings' => [
                'default_payment_method' => $paymentMethodId
            ]
        ]);
    }

    public function createPaymentIntent($new_customer, $request, $totalAmount, $amount, $taxAmount, $currency, $supportTax)
    {
        return PaymentIntent::create([
            'amount' => $totalAmount * 100, // Convert to cents
            'currency' => $currency,
            'payment_method' => $request->payment_method_id,
            'confirm' => true,
            'customer' => $new_customer->stripe_id,
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
    }
} 