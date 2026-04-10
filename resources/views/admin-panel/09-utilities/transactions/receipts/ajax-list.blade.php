@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Name / Email">
        <div class="cds-name-email-bx text-end text-md-start">
            {{$record->user->first_name ?? ''}} {{$record->user->last_name ?? ''}}
            <a href="mailto:{{$record->user->email ?? ''}}" class="email-link">
                {{$record->user->email ?? ''}}
            </a>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Amount">{{currencySymbol($record->currency)}}{{$record->sub_total ?? '0'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Tax">{{currencySymbol($record->currency)}}{{calculateTax($record->tax,$record->sub_total) ?? '0'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Created Date">{{dateFormat($record->created_at)}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Paid Date">{{$record->paid_date != '' ? dateFormat($record->paid_date) : '-'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">
        <span class="cds-status-badge status-{{ strtolower($record->payment_status) }}">
            {{ $record->payment_status }}
        </span>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Type">{{$record->invoice_type}}</div><div class="cdsTYDashboard-table-cell cdsTYDashboard-table-cell-action" data-label="Action">
    @if($canAddTransactions)
   
        @php $downloadPath = url('download-from-storage?file_path=invoices&file=invoice_' . $record->invoice_number . '.pdf'); @endphp

        <div class="cds-download-btn-group">
            <a class="CdsTYButton-btn-primary CdsTYButton-border-thick CdsTYButton-size-sm"  href="{{ $downloadPath }}" download="invoice_{{ $record->invoice_number }}.pdf">Download</a>
        </div>
  
    @endif
    
        @if(checkPrivilege([ 'route_prefix' => 'panel.transactions-receipts', 'module' => 'professional-transactions-receipts', 'action' => 'view' ]))
        <a class="CdsTYButton-btn-secondary-outline CdsTYButton-size-sm" href="{{ baseUrl('transactions/receipts/view/'.$record->unique_id) }}"> <i class="fa-eye fa-solid me-1" aria-hidden="true"></i> View </a>
        @else - @endif
    </div>
</div>
@endforeach
