
@extends('admin-panel.layouts.app')

@section('content')

<section class="cdsTYSupportDashboard-amount-contributed">
    <div class="cdsTYSupportDashboard-amount-contributed-container">
        <div class="cdsTYSupportDashboard-amount-contributed-container-header"></div>
            <div class="cdsTYSupportDashboard-amount-contributed-container-body">
                <div class="cdsTYSupportDashboard-amount-contributed-container-body-list">
                    <div class="cdsTYSupportDashboard-amount-contributed-container-body-list-segment cds-ty-dashboard-segments">
                        <div class="cdsTYSupportDashboard-amount-contributed-container-body-list-segment-header"> <div class="membership-heading">
                            <span>Current Subscription</span>
                            <p class="price">Amount:   {{currencySymbol()}} {{$record->total_amount ?? ''}}<span>/month</span></p>
                        </div>
                    </div>
                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-body">
                        <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-row"> 	
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                                <span>Customer</span>
                                {{$record->user->first_name ?? ''}} {{$record->user->last_name ?? ''}}
                            </div> 
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                                <span>Status</span>
                                <label class=" {{$record->userSubscriptionHistory->subscription_status ?? ''}}">   
                                    {{$record->userSubscriptionHistory->subscription_status ?? ''}}
                                </label>
                            </div>
	                        <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                                <span>Current Period</span>
                                @php
                                    $periodStart = \Carbon\Carbon::createFromTimestamp($nextInvoiceData?->period_start ?? 0)->format('d-M');
                                    $periodEnd = \Carbon\Carbon::createFromTimestamp($nextInvoiceData?->period_end ?? 0)->format('d-M-Y');
                                @endphp

                                @if($nextInvoiceData && $nextInvoiceData->period_start && $nextInvoiceData->period_end)
                                    {{ $periodStart }} to {{ $periodEnd }}
                                @else
                                    N/A
                                @endif                 
                            </div>
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                                <span>Created on</span>
                                {{$record->created_at ? $record->created_at : ''}}
                            </div>
	                        <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                                <span>Payment Method</span>
                                {{ $record->user->pm_type ?? 'N/A' }} ....  
                                {{ isset($record->user->pm_last_four) ? decryptVal($record->user->pm_last_four) : 'N/A' }}
                            </div>
	                        <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                                <a class="cds-membership-manage-card-button"
                                    href="{{ baseUrl('payment-methods/cards') }}">
                                    <i class="fa-light fa-credit-card fa-xl"></i> Manage Cards
                                </a>
                            </div>
	 				        <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                            </div>										
		                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                                @if(($record->userSubscriptionHistory->subscription_status??'') == 'active')
                                    <div class="membership-details-cancel-block">
                                        <a data-href="{{ baseUrl('transactions/history/cancel/'.$record->subscription_id) }}"
                                            onclick="confirmAnyAction(this)" title="Cancel Subscription"
                                            data-action="Cancel Subscription">
                                            Cancel
                                            Subscription
                                        </a>
                                    </div>
                                @endif
                            </div>	
                        </div>
                    </div>
                </div>
                <div class="cdsTYSupportDashboard-amount-contributed-container-body-list-segment">
                    <div class="cds-ty-dashboard-segments">
                        <div class="cds-ty-dashboard-segments-title">Upcoming Invoice </div>
                        <div class="upcoming-details">
                            @forelse($nextInvoiceData->lines->data ?? [] as $key => $data)
                                <div class="upcoming-heading">
                                    <h5>{{ $data->description ?? 'N/A' }}</h5>
                                    <!-- <p>Info</p> -->
                                </div>
                                <div class="upcoming-info">
                                    <h6>Description:</h6>
                                    <p>{{ $data->description ?? 'N/A' }}</p>
                                </div>
                                <div class="upcoming-info">
                                    <h6>Payment Date:</h6>
                                    <p>{{ $record->created_at ? $record->created_at->format('Y-m-d') : 'N/A' }}</p>
                                </div>
                                <div class="upcoming-info">
                                    <h6>Next Invoice Date:</h6>
                                    <p>{{ isset($data->period->end) ? \Carbon\Carbon::createFromTimestamp($nextInvoiceData->period_end ?? 0)->format('Y-m-d') : 'N/A' }}
                                    </p>
                                </div>
                                <div class="upcoming-info">
                                    <h6> Amount:</h6>
                                    <p><strong>  {{currencySymbol()}} {{ number_format(($data->amount ?? 0) / 100, 2) }}</strong></p>
                                </div>
                                <div class="upcoming-info">
                                    <h6>Total Amount Due:</h6>
                                    <p><strong>  {{currencySymbol()}} {{ number_format(($nextInvoiceData->amount_due ?? 0) / 100, 2) }}</strong></p>
                                </div>
                                @empty
                                <div class="upcoming-info">
                                    <p>No upcoming invoices available.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="cdsTYSupportDashboard-amount-contributed-container-body-list-segment">
                    <div class="cds-ty-dashboard-segments">
                        <div class="cds-ty-dashboard-segments-title">Subscription History</div>
                        <div class="upcoming-details" style="">
                            @forelse($subscriptionHistory ?? [] as $key => $data)
                                <div class="subscription-detail">
                                    <!-- Payment Date -->
                                    <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-title">
                                        <label>
                                            <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-label">Payment Date</div>
                                            <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-values">
                                                {{ $record->created_at ? $record->created_at->format('Y-m-d') : 'N/A' }}
                                            </div>
                                        </label>
                                    </div>
                    
                                    <!-- Status -->
                                    <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-title">
                                        <label>
                                            <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-label">Status</div>
                                            <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-values">
                                                {{ $data->status ?? 'N/A' }}
                                            </div>
                                        </label>
                                    </div>
    
                                    <!-- Amount -->
                                    <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-title">
                                        <label>
                                            <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-label">Amount</div>
                                            <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-values">
                                                {{ currencySymbol() }} {{ number_format(($data->plan->amount ?? 0) / 100, 2) }}
                                            </div>
                                        </label>
                                    </div>
                    
                                    <!-- Download -->
                                    <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-title">
                                        <label>
                                            <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-label">Download</div>
                                            <div class="cds-fs-form-assessment-report-panel-body-report-render-body-list-body-values">
                                                @if($record->subscription_id == $data->id)
                                                @php
                                                $downloadPath = '';
                                                if (!empty($record->invoice) && !empty($record->invoice->invoice_number)) {
                                                    $downloadPath = mainTrustvisoryUrl() . '/storage/app/public/invoices/invoice_' . $record->invoice->invoice_number . '.pdf';
                                                }
                                            @endphp
                                                    <a class="ms-auto border p-2 btn add-btn" href="{{ $downloadPath }}" download="invoice_{{ $record->invoice->invoice_number }}.pdf">
                                                        <i class="fa-solid fa-file-arrow-down me-1"></i> Download
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @empty
                                <div class="upcoming-info" style="grid-column: span 2; text-align: center;">
                                    <p>No subscription history available.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cdsTYSupportDashboard-amount-contributed-container-footer"></div>
    </div>
</section>

<!-- End Content -->
@endsection