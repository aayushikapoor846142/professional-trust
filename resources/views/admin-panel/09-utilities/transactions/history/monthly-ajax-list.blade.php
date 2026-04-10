@if(count($records) > 0)
@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Name"> {{$record->user->first_name ?? ''}} {{$record->user->last_name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Amount Paid">{{ currencySymbol('CAD') }}{{$record->amount ?? '0'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Tax Amount">{{ currencySymbol('CAD') }}{{$record->tax ?? '0'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Total Amount"> {{ currencySymbol('CAD') }}{{$record->total_amount ?? '0'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Created on">{{dateFormat($record->created_at)}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">
        @if(!empty($record->userSubscriptionHistory))
        <span class="cds-status-badge status-{{ strtolower($record->userSubscriptionHistory->subscription_status ?? '') }}">        
            {{ $record->userSubscriptionHistory->subscription_status ?? '' }}</span>
        @endif
    </div>
	<div class="cdsTYDashboard-table-cell cdsTYDashboard-table-cell-action" data-label="Status"> 

               @if(checkPrivilege([
                'route_prefix' => 'panel.transactions-history',
                'module' => 'professional-transactions-history',
                'action' => 'view'
            ]))
        <a class="CdsTYButton-btn-secondary-outline CdsTYButton-size-sm" href="{{ baseUrl('transactions/history/view/'.$record->unique_id) }}">
            View
        </a>
        <a href="javascript:;" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('transactions/history/monthly-quick-view/'.$record->unique_id) }}">
            <i class="fa-eye fa-solid me-1"></i>
        </a>
        @endif
    </div>
</div>

@endforeach

@else
  
@endif

