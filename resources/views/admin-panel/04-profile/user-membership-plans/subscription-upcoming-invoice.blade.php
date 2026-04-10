  <div class="cds-ty-dashboard-segments">
            <div class="cds-ty-dashboard-segments-title"><i class="fa-solid fa-receipt fa-lg"></i> Upcoming Invoice </div>
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
                    <p><strong>${{ number_format(($data->amount ?? 0) / 100, 2) }}</strong></p>
                </div>
                <div class="upcoming-info">
                    <h6>Total Amount Due:</h6>
                    <p><strong>${{ number_format(($nextInvoiceData->amount_due ?? 0) / 100, 2) }}</strong></p>
                </div>
                       <div class="upcoming-info">
                    <h6>Current Period:</h6>
                    <p>  {{ $currentSubscription 
                                        ? \Carbon\Carbon::createFromTimestamp($currentSubscription->current_period_start ?? 0)->format('d-M')
                                        .' to '. 
                                        \Carbon\Carbon::createFromTimestamp($currentSubscription->current_period_end ?? 0)->format('d-M-Y') 
                                        : 'N/A' }}</p>
                </div>
                @empty
                <div class="upcoming-info">
                    <p>No upcoming invoices available.</p>
                </div>
                @endforelse
            </div>
        </div>