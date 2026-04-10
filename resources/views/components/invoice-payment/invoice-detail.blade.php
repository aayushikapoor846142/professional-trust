

<table>
<tr class="heading">
    <td>Particular</td>
    <td>Amount</td>
    <td>Discount</td>
    <td>Sub Total</td>
</tr>
@php
    $totalSum = 0;
@endphp
@foreach($record->invoiceItems  as $item)
<tr class="details">
    <td>{!! $item['particular'] !!}</td>
    <td>{{currencySymbol($record['currency'] ?? '')}}{{$item['amount']}}</td>
    <td> {{$item['discount_type'] == 'amount' ? '$' :''}}{{$item['discount']}} {{$item['discount_type'] == 'per' ? '%' :''}}</td>
    <td>{{currencySymbol($record['currency'] ?? '')}}{{invoiceItemSubTotal($item['amount'],$item['discount_type'],$item['discount'])}}</td>
    @php
        $subtotal = invoiceItemSubTotal($item['amount'], $item['discount_type'], $item['discount']);
        $totalSum += $subtotal;
    @endphp
</tr>
@endforeach

<tr class="total">
    <td style="text-align:right"><b>SubTotal:</b></td>
    <td>{{currencySymbol($record['currency'] ?? '')}}{{$totalSum}}</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Additional Discount :@if($record['discount_type'] == 'per')
            [{{$record['discount']}}%] @endif</b></td>
    <td>@if($record['discount_type'] != ''){{invoiceAdditionalDiscount($totalSum,$record['discount'],$record['discount_type'])}} @else -  @endif</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Taxable Amount:</b></td>
    <td>{{currencySymbol($record['currency'] ?? '')}}{{$totalSum - invoiceAdditionalDiscount($totalSum,$record['discount'],$record['discount_type'])}}</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Tax:[{{$record['tax']}} %]</b></td>
    @php 
    $taxable_amount  = $totalSum - invoiceAdditionalDiscount($totalSum,$record['discount'],$record['discount_type']);

    @endphp
    <td>{{currencySymbol($record['currency'] ?? '')}}{{invoiceTaxableAmount($taxable_amount,$record['tax'])}}</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Total:</b></td>
    <td>{{currencySymbol($record['currency'] ?? '')}}{{$taxable_amount + invoiceTaxableAmount($taxable_amount,$record['tax'])}}</td>
</tr>
<tr>
    <td colspan="2">
        <div style=" background-color: #EEE; padding: 10px 20px; margin-top: 20px; "><b>Disclaimer: </b>All payments made are non-refundable unless otherwise stated as in a reconsideration policy.</div>
    </td>
</tr>
</table>