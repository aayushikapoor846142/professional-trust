@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Customer">
        <div class="cdsavatarBox">
            <div class="CDSSupportPayment-avatar">
                {{ strtoupper(substr($record->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($record->last_name ?? '', 0, 1)) }}
            </div>
            <div class="CdsSendInvitation-client-info ms-2">
                <div class="CDSSupportPayment-customer-name">{{$record->first_name ?? ''}} {{$record->last_name ?? ''}}</div>
                <div class="CDSSupportPayment-customer-email">{{$record->email ?? ''}}</div>
            </div>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Invoice Number">#{{ $record->invoice_number }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Payment Gateway">{{$record->payment_gateway ?? '-'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Copy Link">
        @if($record->payment_status != 'paid')
            <a class="CdsTYButton-btn-primary" href="javascript:;" onclick="showPopup('<?= baseUrl('case-with-professionals/invoices/copy-link/'.$record->unique_id) ?>')">Click here</a>
        @else - @endif
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Amount Paid">{{currencySymbol($record->currency)}}{{number_format($record->total_amount ?? 0, 2)}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Payment Status">
        @php $statusClass = ''; $statusIcon = ''; switch(strtolower($record->payment_status)) { case 'paid': case 'completed': case 'success': $statusClass = 'CDSSupportPayment-paid'; break; case 'pending': $statusClass =
            'CDSSupportPayment-pending'; break; case 'failed': $statusClass = 'CDSSupportPayment-failed'; break; default: $statusClass = 'CDSSupportPayment-pending'; } @endphp
        <span class="CDSSupportPayment-status-pill {{ $statusClass }}">
            <span class="CDSSupportPayment-status-indicator"></span>
            {{ ucfirst($record->payment_status) }}
        </span>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Created At">
        {{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Actions">
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                <li>
                    @if($record->payment_status != 'paid')
                    <a href="{{ baseUrl('case-with-professionals/invoices/edit/'.$record->unique_id) }}" class="dropdown-item" title="Edit">
                        <i class="fa-regular fa-pen me-1"></i>
                        Edit
                    </a>
                    @endif
                </li>
                <li>
                    <a href="{{ baseUrl('case-with-professionals/invoices/download-invoice-pdf/'.$record->unique_id) }}" download class="dropdown-item" title="Download Invoice">
                        <i class="fa-solid fa-download me-1"></i>
                        Download
                    </a>
                </li>
                <li>
                    @if($record->payment_status != 'paid')
                    <a href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('case-with-professionals/invoices/delete/'.$record->unique_id) }}" class="dropdown-item" title="Delete">
                        <i class="fa-regular fa-trash me-1"></i>
                        Delete
                    </a>
                    @endif
                </li>
            </ul>
        </div>
    </div>
</div>

{{--<div class="CDSSupportPayment-payment-item">
    <div class="CDSSupportPayment-payment-content">
        <!-- Customer Details -->
        <div class="CDSSupportPayment-customer-details">
            <div class="CDSSupportPayment-avatar">
                {{ strtoupper(substr($record->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($record->last_name ?? '', 0, 1)) }}
            </div>
            <div>
                <div class="CDSSupportPayment-customer-name">
                    {{$record->first_name ?? ''}} {{$record->last_name ?? ''}}
                </div>
                <div class="CDSSupportPayment-customer-email">
                    {{$record->email ?? ''}}
                </div>
            </div>
        </div>
        <div class="CDSSupportPayment-invoice-info">
            <div class="CDSSupportPayment-invoice-number">
                #{{ $record->invoice_number }}
            </div>
           
        </div>
        <!-- Tax -->
        <div>
            <div class="CDSSupportPayment-tax-value">
                {{$record->payment_gateway ?? '-'}}
            </div>
        </div>
        <!-- link here -->
        <div>
            <div class="CDSSupportPayment-tax-value">
                @if($record->payment_status != 'paid')
                <a class="CdsTYButton-btn-primary" href="javascript:;" onclick="showPopup('<?= baseUrl('invoices/copy-link/'.$record->unique_id) ?>')">Click here</a>
                @else - @endif
            </div>
        </div>
        <!-- end -->
        <!-- Amount -->
        <div>
            <div class="CDSSupportPayment-amount-value">
                {{currencySymbol($record->currency)}}{{number_format($record->total_amount ?? 0, 2)}}
            </div>
        </div>
        <!-- Status -->
        <div>
            @php $statusClass = ''; $statusIcon = ''; switch(strtolower($record->payment_status)) { case 'paid': case 'completed': case 'success': $statusClass = 'CDSSupportPayment-paid'; break; case 'pending': $statusClass =
            'CDSSupportPayment-pending'; break; case 'failed': $statusClass = 'CDSSupportPayment-failed'; break; default: $statusClass = 'CDSSupportPayment-pending'; } @endphp
            <span class="CDSSupportPayment-status-pill {{ $statusClass }}">
                <span class="CDSSupportPayment-status-indicator"></span>
                {{ ucfirst($record->payment_status) }}
            </span>
        </div>
        <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}</div>
        <!-- Actions -->
        <div class="CDSSupportPayment-actions">
            <div class="btn-group">
                <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                    @if(checkPrivilege([ 'route_prefix' => 'panel.invoices', 'module' => 'professional-invoices', 'action' => 'edit' ]))
                    <li>
                        @if($record->payment_status != 'paid')
                        <a href="{{ baseUrl('invoices/edit/'.$record->unique_id) }}" class="dropdown-item" title="Edit">
                            <i class="fa-regular fa-pen me-1"></i>
                            Edit
                        </a>
                        @endif
                    </li>
                    @endif @if(checkPrivilege([ 'route_prefix' => 'panel.invoices', 'module' => 'professional-invoices', 'action' => 'downloadinvoicepdf' ]))
                    <li>
                        <a href="{{ baseUrl('invoices/download-invoice-pdf/'.$record->unique_id) }}" download class="dropdown-item" title="Download Invoice">
                            <i class="fa-solid fa-download me-1"></i>
                            Download
                        </a>
                    </li>
                    @endif @if(checkPrivilege([ 'route_prefix' => 'panel.invoices', 'module' => 'professional-invoices', 'action' => 'delete' ]))
                    <li>
                        @if($record->payment_status != 'paid')
                        <a href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('invoices/delete/'.$record->unique_id) }}" class="dropdown-item" title="Delete">
                            <i class="fa-regular fa-trash me-1"></i>
                            Delete
                        </a>
                        @endif
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>--}}

@endforeach

<script type="text/javascript">
// Show more options function
function showMoreOptions(uniqueId) {
    // You can implement a dropdown or modal here
    // For now, just a placeholder
    console.log('More options for:', uniqueId);
}

// Initialize tooltips if using Bootstrap
$(document).ready(function() {
    $('[title]').tooltip();
});
</script>