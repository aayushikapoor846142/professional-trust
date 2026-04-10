<b class="mt-5">Invoice</b>

<div class="cdsTYDashboard-profile-documents-container-box mt-2">
    <div class="cdsTYDashboard-profile-documents-container-box-segment">
        <div class="cdsTYDashboard-profile-documents-details-wrap">
            <div class="cdsTYDashboard-profile-documents-image">
                
            </div>
            <div class="cdsTYDashboard-profile-documents-details">
                <h3> Invoice Number : #{{ $invoice->invoice_number }}</h3>
                <span>Amount: {{currencySymbol($invoice->currency)}}{{$invoice->total_amount}}</span>
                <span>Invoice Date{{date('d M, Y',strtotime($invoice->invoice_date))}}</span>
                @if($invoice->payment_status == 'paid')
                    <span>Paid Date:{{date('d M, Y',strtotime($invoice->paid_date))}}</span>
                    <span class="badge bg-success text-white">Paid</span>
                @else
                    <span class="badge bg-danger text-white">Pending</span>
                @endif
            </div> 
        </div>  
        <div class="cdsTYDashboard-profile-documents-buttons">
            <a href="{{ baseUrl('invoices/download-invoice-pdf/'.$invoice->unique_id) }}" download class="cdsTYDashboard-button-light-outline cdsTYDashboard-button-small" title="Download Invoice">
                Download
            </a>
        </div>
    </div>
</div>

   
