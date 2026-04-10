@if(count($records) > 0) @foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Name">{{$record->user->first_name ?? ''}} {{$record->user->last_name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Amount Paid">{{ currencySymbol('CAD') }}{{$record->amount ?? '0'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Tax Amount">{{ currencySymbol('CAD') }}{{$record->tax ?? '0'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Total Amount">{{ currencySymbol('CAD') }}{{$record->total_amount ?? '0'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Created on">{{dateFormat($record->created_at ?? '', 'd M Y, h:i A')}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">

    @if(!empty($record->invoice))
      <span class="cds-status-badge status-{{ strtolower($record->invoice->payment_status ?? '') }}">
            {{ $record->invoice->payment_status ?? '' }}
        </span>
    @else
        -
    @endif
    </div>
    <div class="cdsTYDashboard-table-cell cdsTYDashboard-table-cell-action" data-label="Action" >
           @if(checkPrivilege([
                'route_prefix' => 'panel.transactions-history',
                'module' => 'professional-transactions-history',
                'action' => 'view'
            ]))
            <div class="cdsTYDashboard-action-row">
                
                <a href="javascript:;"   onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('transactions/history/onetime-quick-view/'.$record->unique_id) }}" class="CdsTYButton-btn-primary CdsTYButton-border-thick CdsTYButton-size-sm">
                    <i class="fa-eye fa-solid me-1"></i> View
                </a><a class="CdsTYButton-btn-secondary-outline CdsTYButton-size-sm" href="{{ baseUrl('transactions/history/view-details/'.$record->unique_id) }}"> Details </a>
            </div>
        @else
         - 
        @endif
    </div>
</div>
@endforeach
@else

@endif