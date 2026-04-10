@extends('components.pdf-master')
@section('content')
<tr class="heading">
    <td>Particular</td>
    <td>Amount</td>
</tr>
@foreach($invoice_items  as $item)
<tr class="details">
    <td>{!! $item->particular !!}</td>
    <td>{{currencySymbol($invoice->currency)}}{{$item->amount}}</td>
</tr>
@endforeach
<tr class="total">
    <td style="text-align:right"><b>SubTotal:</b></td>
    <td>{{currencySymbol($invoice->currency)}}{{$invoice->sub_total}}</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Tax ({{$invoice->tax}}%):</b></td>
    <td>{{currencySymbol($invoice->currency)}}{{calculateTax($invoice->tax,$invoice->sub_total)}}</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Total:</b></td>
    <td>{{currencySymbol($invoice->currency)}}{{$invoice->total_amount}}</td>
</tr>
<tr>
    <td colspan="2">
        <div style=" background-color: #EEE; padding: 10px 20px; margin-top: 20px; "><b>Disclaimer: </b>All payments made are non-refundable unless otherwise stated as in our refund policy.</div>
    </td>
</tr>
@endsection
