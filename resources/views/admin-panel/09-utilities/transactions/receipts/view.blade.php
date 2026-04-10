@extends('admin-panel.layouts.app')
@section('styles')@section('page-submenu')
{!! pageSubMenu('transactions') !!}
@endsection
<link rel="stylesheet" href="{{ asset('assets/css/common-table-redesign.css') }}">
@endsection
<style>
  
</style>
@section('content')

<div class="container">    
    <section class="cdsTYSupportDashboard-view-invoice">
        <div class="cdsTYSupportDashboard-view-invoice-container">
            <div class="cds-ty-dashboard-breadcrumb-container-header text-center">
               
            </div>
                </div>
    </section>
</div>
<div class="cdsTYDashboard-details-container-wrapper">
 <div class="cdsTYDashboard-details-container-wrapper-header">
 <h3>Receipt</h3> @php $downloadPath = url('download-from-storage?file_path=invoices&file=invoice_' . $record->invoice_number . '.pdf'); @endphp

                <a class="CdsTYButton-btn-primary cds-view-invoice-btn" href="{{ $downloadPath }}" download="invoice_{{ $record->invoice_number }}.pdf"><i class="fa-solid fa-file-arrow-down me-1"></i> Download</a>
 </div>
                <div class="cdsTYDashboard-details-container-wrapper-body"><div class="cdsTYDashboard-invoice-box">
                             <table>
                        <tr class="top">
                            <td colspan="2">
                                <table>
                                    <tr>
                                        <td class="title">
                                            <img src="{{url('assets/images/logo-c.png') }}" alt="Logo" class="cds-invoice-logo" />
                                        </td>
                                        <td>
                                            <b>Invoice #</b>: {{$record->invoice_number}}<br />
                                            <b>Created Date:</b> {{dateFormat($record->created_at)}}<br />
                                            @if($record->payment_status == 'paid')
                                            <b>Paid Date:</b> {{dateFormat($record->paid_date)}}<br />
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr class="information">
                            <td colspan="2">
                                <table>
                                    <tr>
                                        <td width="50%" class="bill-from">
                                            <strong>Bill From:</strong><br />
                                            {{ siteSetting('company_name')}}<br />
                                            {{ siteSetting('address') }}<br />
                                            {{ siteSetting('city') }},<br />
                                            {{ siteSetting('state') }},<br />
                                            {{ siteSetting('zipcode') }},<br />
                                            {{ siteSetting('country') }}
                                        </td>
                                        <td width="50%" class="bill-to">
                                            <strong>Bill To:</strong><br />
                                            {{$record->first_name??''}} {{$record->last_name??''}},<br />
                                            {{$record->address??''}},<br />
                                            {{$record->city??''}}, {{$record->state??''}}, {{$record->zip??''}}<br />
                                            {{$record->email??''}}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr class="heading">
                            <td>Particular</td>
                            <td>Amount</td>
                        </tr>
                        @foreach($record->invoiceItems as $item)
                        <tr class="details">
                            <td>{!! $item->particular !!}</td>
                            <td>{{currencySymbol($item->currency)}}{{$item->amount}}</td>
                        </tr>
                        @endforeach
                        <tr class="total">
                            <td style="text-align: right;"><b>SubTotal:</b></td>
                            <td>{{currencySymbol($item->currency)}}{{$record->sub_total}}</td>
                        </tr>
                        <tr class="total">
                            <td style="text-align: right;"><b>Tax:</b></td>
                            <td>{{currencySymbol($item->currency)}}{{calculateTax($record->tax,$record->sub_total)}}</td>
                        </tr>
                        <tr class="total">
                            <td style="text-align: right;"><b>Total:</b></td>
                            <td>{{currencySymbol($item->currency)}}{{$record->total_amount}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="background-color: #eee; padding: 10px 20px; margin-top: 20px;"><b>Disclaimer: </b>All payments made are non-refundable unless otherwise stated as in a refund policy.</div>
                            </td>
                        </tr>
                    </table>
       
							  
							  
							  </div>  </div>
            </div>
    
@endsection