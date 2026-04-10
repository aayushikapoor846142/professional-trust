@extends('pdf.global-pdf-master')
@section('content')
<tr class="heading">
    <td>Particular</td>
    <td>Amount</td>
    <td>Discount</td>
    <td>Sub Total</td>
</tr>
@php
    $totalSum = 0;
@endphp
@foreach($invoice_items  as $item)
<tr class="details">
    <td>{!! $item['particular'] !!}</td>
    <td>{{currencySymbol($invoice['currency'] ?? '')}}{{$item['amount']}}</td>
    <td> {{$item['discount_type'] == 'amount' ? '$' :''}}{{$item['discount']}} {{$item['discount_type'] == 'per' ? '%' :''}}</td>
    <td>{{currencySymbol($invoice['currency'] ?? '')}}{{invoiceItemSubTotal($item['amount'],$item['discount_type'],$item['discount'])}}</td>
    @php
        $subtotal = invoiceItemSubTotal($item['amount'], $item['discount_type'], $item['discount']);
        $totalSum += $subtotal;
    @endphp
</tr>
@endforeach
<tr class="total">
    <td style="text-align:right"><b>SubTotal:</b></td>
    <td>{{currencySymbol($invoice['currency'] ?? '')}}{{$totalSum}}</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Additional Discount :@if($invoice['discount_type'] == 'per')
            [{{$invoice['discount']}}%] @endif</b></td>
    <td>@if($invoice['discount_type'] != ''){{invoiceAdditionalDiscount($totalSum,$invoice['discount'],$invoice['discount_type'])}} @else -  @endif</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Taxable Amount:</b></td>
    <!-- <td>{{$invoice['currency']}}{{$totalSum - $invoice['discount']}}</td> -->
    <td>{{currencySymbol($invoice['currency'] ?? '')}}{{$totalSum - invoiceAdditionalDiscount($totalSum,$invoice['discount'],$invoice['discount_type'])}}</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Tax:[{{$invoice['tax']}} %]</b></td>
    @php 
    $taxable_amount  = $totalSum - invoiceAdditionalDiscount($totalSum,$invoice['discount'],$invoice['discount_type']);

    @endphp
    <td>{{currencySymbol($invoice['currency'] ?? '')}}{{invoiceTaxableAmount($taxable_amount,$invoice['tax'])}}</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Total:</b></td>
    <td>{{currencySymbol($invoice['currency'] ?? '')}}{{$taxable_amount + invoiceTaxableAmount($taxable_amount,$invoice['tax'])}}</td>
</tr>
<tr>
    <td colspan="2">
        <div style=" background-color: #EEE; padding: 10px 20px; margin-top: 20px; "><b>Disclaimer: </b>All payments made are non-refundable unless otherwise stated as in a reconsideration policy.</div>
    </td>
</tr>
@endsection
