@extends('pdf.pdf-master')
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
    <td style="text-align:right"><b>Tax:</b></td>
    <td>{{currencySymbol($invoice->currency)}}{{$invoice->tax}}</td>
</tr>
<tr class="total">
    <td style="text-align:right"><b>Total:</b></td>
    <td>{{currencySymbol($invoice->currency)}}{{$invoice->total_amount}}</td>
</tr>
@endsection