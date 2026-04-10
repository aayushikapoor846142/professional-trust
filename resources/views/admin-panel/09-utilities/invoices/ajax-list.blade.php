@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsTYDashboard-table-cell-avatar-information" data-label="Customer">
        <div class="CDSSupportPayment-avatarBox">
            <div class="CDSSupportPayment-avatar-display">
                <div class="CDSSupportPayment-avatar">
                    {{ strtoupper(substr($record->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($record->last_name ?? '', 0, 1)) }}
                </div>
                <div class="CDSSupportPayment-customer-name">{{$record->first_name ?? ''}} {{$record->last_name ?? ''}}</div>
            </div>
            <div class="CdsSendInvitation-client-info">
                <div class="CDSSupportPayment-customer-email">{{$record->email ?? ''}}</div>
            </div>
        </div>
    </div>
    
    <div class="cdsTYDashboard-table-cell" data-label="Invoice Number">#{{ $record->invoice_number }}</div>
    
    <div class="cdsTYDashboard-table-cell" data-label="Payment Gateway">{{$record->payment_gateway ?? '-'}}</div>
    
    <div class="cdsTYDashboard-table-cell" data-label="Amount Paid">
        {{currencySymbol($record->currency)}}{{number_format($record->total_amount ?? 0, 2)}}
    </div>
    
    <div class="cdsTYDashboard-table-cell" data-label="Payment Status">
        @php 
        $statusClass = ''; 
        switch(strtolower($record->payment_status)) { 
            case 'paid': 
            case 'completed': 
            case 'success': 
                $statusClass = 'CDSSupportPayment-paid'; 
                break; 
            case 'pending': 
                $statusClass = 'CDSSupportPayment-pending'; 
                break; 
            case 'failed': 
                $statusClass = 'CDSSupportPayment-failed'; 
                break; 
            default: 
                $statusClass = 'CDSSupportPayment-pending'; 
        } 
        @endphp
        <span class="CDSSupportPayment-status-pill {{ $statusClass }}">
            <span class="CDSSupportPayment-status-indicator"></span>
            {{ ucfirst($record->payment_status) }}
        </span>
    </div>
    
    <div class="cdsTYDashboard-table-cell" data-label="Created At">
        {{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}
    </div>
    
    <div class="cdsTYDashboard-table-cell cdsTYDashboard-table-cell-action" data-label="Actions">
        @if($record->payment_status != 'paid')
            <a class="CdsTYButton-btn-primary CdsTYButton-border-thick CdsTYButton-size-sm" 
               href="javascript:;" 
               onclick="showPopup('<?= baseUrl('invoices/copy-link/'.$record->unique_id) ?>')">
                Copy Link
            </a>
        @endif
        
        <!-- Smart Dropdown with Unique ID -->
        <div class="cdsTYDashboardDropdownsDropdown" data-dropdown-id="invoice-{{ $record->unique_id ?? $key }}">
            <button class="cdsTYDashboardDropdownsDropdownBtn CdsTYButton-btn-secondary-outline CdsTYButton-size-sm">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
            <div class="cdsTYDashboardDropdownsDropdownMenu">
                @if(checkPrivilege([ 'route_prefix' => 'panel.invoices', 'module' => 'professional-invoices', 'action' => 'edit' ]))
                    @if($record->payment_status != 'paid')
                    <div class="cdsTYDashboardDropdownsDropdownItem" 
                         onclick="window.location.href='{{ baseUrl('invoices/edit/'.$record->unique_id) }}'">
                        <span class="cdsTYDashboardDropdownsDropdownItemIcon">
                            <i class="fa-regular fa-pen"></i>
                        </span>
                        Edit
                    </div>
                    @endif
                @endif
                
                @if(checkPrivilege([ 'route_prefix' => 'panel.invoices', 'module' => 'professional-invoices', 'action' => 'downloadinvoicepdf' ]))
                <div class="cdsTYDashboardDropdownsDropdownItem" 
                     onclick="window.location.href='{{ baseUrl('invoices/download-invoice-pdf/'.$record->unique_id) }}'">
                    <span class="cdsTYDashboardDropdownsDropdownItemIcon">
                        <i class="fa-solid fa-download"></i>
                    </span>
                    Download
                </div>
                @endif
                
                @if(checkPrivilege([ 'route_prefix' => 'panel.invoices', 'module' => 'professional-invoices', 'action' => 'delete' ]))
                    @if($record->payment_status != 'paid')
                    <div class="cdsTYDashboardDropdownsDropdownItem cdsTYDashboardDropdownsDropdownItemDanger" 
                         onclick="confirmAction(this)" 
                         data-href="{{ baseUrl('invoices/delete/'.$record->unique_id) }}">
                        <span class="cdsTYDashboardDropdownsDropdownItemIcon">
                            <i class="fa-regular fa-trash"></i>
                        </span>
                        Delete
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach