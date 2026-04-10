@extends('admin-panel.layouts.app')
@section('styles')
<link href="{{ url('assets/css/11-CDS-appointment-detail.css') }}" rel="stylesheet" />
@endsection
@section('content')

<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
{{$appointment_data->unique_id ?? 'NA' }}
<div class="CdsDashboardAppointment-details-view-top-actions">
                    <button class="CdsDashboardAppointment-details-view-icon-btn" title="Print" onclick="window.print()">🖨️</button>
                    <button class="CdsDashboardAppointment-details-view-icon-btn" title="Download">📥</button>
                    <button class="CdsDashboardAppointment-details-view-icon-btn" title="Share">🔗</button>
                </div> <div class="CdsDashboardAppointment-details-view-header-top">
            <div>
                <h1 class="CdsDashboardAppointment-details-view-page-title">Appointment Overview</h1>
                <p class="CdsDashboardAppointment-details-view-page-subtitle mb-0">
                    Manage your appointment with {{ ($appointment_data->professional->first_name ?? '') .' '.($appointment_data->professional->last_name ?? '') }}
                </p>
            </div>
            <div class="CdsDashboardAppointment-details-view-appointment-badge">
                <span class="CdsDashboardAppointment-details-view-badge-dot" 
                      style="background: {{ $appointment_data->status == 'Approved' ? '#22c55e' : ($appointment_data->status == 'Pending' ? '#f59e0b' : '#ef4444') }}"></span>
                <span>{{ $appointment_data->status ?? 'Pending' }}</span>
            </div>
        </div>
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">

    <!-- Quick Stats -->
    <div class="CdsDashboardAppointment-details-view-quick-stats">
        <div class="CdsDashboardAppointment-details-view-stat-card">
            <div class="CdsDashboardAppointment-details-view-stat-icon blue">📅</div>
            <div class="CdsDashboardAppointment-details-view-stat-value">
                {{ \Carbon\Carbon::parse($appointment_data->appointment_date)->format('M d') }}
            </div>
            <div class="CdsDashboardAppointment-details-view-stat-label">Appointment Date</div>
        </div>
        <div class="CdsDashboardAppointment-details-view-stat-card">
            <div class="CdsDashboardAppointment-details-view-stat-icon green">⏰</div>
            <div class="CdsDashboardAppointment-details-view-stat-value">
                {{ date("g:i A", strtotime($appointment_data->start_time_converted)) }}
            </div>
            <div class="CdsDashboardAppointment-details-view-stat-label">Start Time</div>
        </div>
        <div class="CdsDashboardAppointment-details-view-stat-card">
            <div class="CdsDashboardAppointment-details-view-stat-icon purple">⏱️</div>
            <div class="CdsDashboardAppointment-details-view-stat-value">
                @php
                    $start = \Carbon\Carbon::parse($appointment_data->start_time_converted);
                    $end = \Carbon\Carbon::parse($appointment_data->end_time_converted);
                    $duration = $start->diff($end)->format('%H:%I');
                @endphp
                {{ $duration }}
            </div>
            <div class="CdsDashboardAppointment-details-view-stat-label">Duration</div>
        </div>
        <div class="CdsDashboardAppointment-details-view-stat-card">
            <div class="CdsDashboardAppointment-details-view-stat-icon yellow">💰</div>
            <div class="CdsDashboardAppointment-details-view-stat-value">
                {{currencySymbol($appointment_data->currency)}}{{ $appointment_data->price }}
            </div>
            <div class="CdsDashboardAppointment-details-view-stat-label">Total Cost</div>
        </div>
    </div>

    <!-- Widget Grid -->
    <div class="CdsDashboardAppointment-details-view-widget-grid">
        <!-- Main Content -->
        <div class="CdsDashboardAppointment-details-view-widget-column">
            <!-- Professional Widget -->
            <div class="CdsDashboardAppointment-details-view-widget CdsDashboardAppointment-details-view-professional-widget">
                <div class="CdsDashboardAppointment-details-view-widget-header">
                    <h2 class="CdsDashboardAppointment-details-view-widget-title">Client Details</h2>
                </div>
                <div class="CdsDashboardAppointment-details-view-widget-body">
                    <div class="CdsDashboardAppointment-details-view-professional-content">
                        <div class="CdsDashboardAppointment-details-view-professional-avatar-large">
                            {!! getProfileImage($appointment_data->client->unique_id) !!}
                        </div>
                        <div class="CdsDashboardAppointment-details-view-professional-details">
                            <h3>{{ ($appointment_data->client->first_name ?? '') .' '.($appointment_data->client->last_name ?? '') }}</h3>
                            <div class="CdsDashboardAppointment-details-view-rating">
                                {{ ($appointment_data->client->email ?? '') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Widget -->
            <div class="CdsDashboardAppointment-details-view-widget" style="margin-top: 24px;">
                <div class="CdsDashboardAppointment-details-view-widget-header">
                    <h2 class="CdsDashboardAppointment-details-view-widget-title">
                        <span>📋</span>
                        Appointment Timeline
                    </h2>
                </div>
                <div class="CdsDashboardAppointment-details-view-widget-body CdsDashboardAppointment-details-view-timeline-widget">
                    <div class="CdsDashboardAppointment-details-view-timeline-item">
                        <div class="CdsDashboardAppointment-details-view-timeline-dot"></div>
                        <div class="CdsDashboardAppointment-details-view-timeline-time">
                            {{ \Carbon\Carbon::parse($appointment_data->created_at)->format('M d, Y') }}
                        </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-title">Appointment Booked</div>
                        <div class="CdsDashboardAppointment-details-view-timeline-description">You successfully booked this appointment</div>
                    </div>
                      @foreach($getAppointmentStatus as $appStatus)
                    @if($appStatus->status == 'draft')
                    <div class="CdsDashboardAppointment-details-view-timeline-item">
                        <div class="CdsDashboardAppointment-details-view-timeline-dot"></div>
                        <div class="CdsDashboardAppointment-details-view-timeline-time">
                            {{ \Carbon\Carbon::parse($appStatus->status_date ?? $appStatus->status_date)->format('M d, Y') }}
                        </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-title">Appointment Draft </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-description">
                             Appointment Booking Started.
                        </div>
                    </div>
                    @endif
                    @if($appStatus->status == 'approved')
                    <div class="CdsDashboardAppointment-details-view-timeline-item">
                        <div class="CdsDashboardAppointment-details-view-timeline-dot"></div>
                        <div class="CdsDashboardAppointment-details-view-timeline-time">
                            {{ \Carbon\Carbon::parse($appStatus->status_date ?? $appStatus->status_date)->format('M d, Y') }}
                        </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-title">Approved </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-description">
                            {{ ($appStatus->professional->first_name ?? '') .' '.($appStatus->professional->last_name ?? '') }} accepted your booking
                        </div>
                    </div>
                   <div class="CdsDashboardAppointment-details-view-timeline-item">
                        <div class="CdsDashboardAppointment-details-view-timeline-dot"></div>
                        <div class="CdsDashboardAppointment-details-view-timeline-time">
                            {{ \Carbon\Carbon::parse($appointment_data->appointment_date)->format('M d, Y') }}
                        </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-title">Appointment Date</div>
                        <div class="CdsDashboardAppointment-details-view-timeline-description">Your scheduled appointment date.</div>
                    </div>
                    @endif
                    @if($appStatus->status == 'cancelled')
                    <div class="CdsDashboardAppointment-details-view-timeline-item">
                        <div class="CdsDashboardAppointment-details-view-timeline-dot"></div>
                        <div class="CdsDashboardAppointment-details-view-timeline-time">
                            {{ \Carbon\Carbon::parse($appStatus->status_date ?? $appStatus->status_date)->format('M d, Y') }}
                        </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-title">Appointment Cancelled </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-description">
                            Your Appointment has been {{ $appointment_data->cancelled_reason }}
                        </div>
                    </div>
                    @endif
                     @if($appStatus->status == 'archieved')
                    <div class="CdsDashboardAppointment-details-view-timeline-item">
                        <div class="CdsDashboardAppointment-details-view-timeline-dot"></div>
                        <div class="CdsDashboardAppointment-details-view-timeline-time">
                            {{ \Carbon\Carbon::parse($appStatus->status_date ?? $appStatus->status_date)->format('M d, Y') }}
                        </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-title">Appointment Archieved </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-description">
                            Your Appointment has been Archieved 
                        </div>
                    </div>
                    @endif
                     @if($appStatus->status == 'non-conducted')
                    <div class="CdsDashboardAppointment-details-view-timeline-item">
                        <div class="CdsDashboardAppointment-details-view-timeline-dot"></div>
                        <div class="CdsDashboardAppointment-details-view-timeline-time">
                            {{ \Carbon\Carbon::parse($appStatus->status_date ?? $appStatus->status_date)->format('M d, Y') }}
                        </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-title">Appointment Non-conducted </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-description">
                            Your Appointment has been marked as non-conducted by you.
                        </div>
                    </div>
                    @endif
                      @if($appStatus->status == 'completed')
                    <div class="CdsDashboardAppointment-details-view-timeline-item">
                        <div class="CdsDashboardAppointment-details-view-timeline-dot"></div>
                        <div class="CdsDashboardAppointment-details-view-timeline-time">
                            {{ \Carbon\Carbon::parse($appStatus->status_date ?? $appStatus->status_date)->format('M d, Y') }}
                        </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-title">Appointment Completed </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-description">
                            Your Appointment has been marked as completed by you.
                        </div>
                    </div>
                    @endif
                    @if($appStatus->status == 'awaiting')
                    <div class="CdsDashboardAppointment-details-view-timeline-item">
                        <div class="CdsDashboardAppointment-details-view-timeline-dot"></div>
                        <div class="CdsDashboardAppointment-details-view-timeline-time">
                            {{ \Carbon\Carbon::parse($appStatus->status_date ?? $appStatus->status_date)->format('M d, Y') }}
                        </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-title">Appointment Awaiting </div>
                        <div class="CdsDashboardAppointment-details-view-timeline-description">
                            Please pay your amount to confirm your Appointment.
                        </div>
                    </div>
                    @endif
                    @endforeach



                   
                </div>
            </div>

            <!-- Additional Info Widget -->
            @if($appointment_data->additional_info)
            <div class="CdsDashboardAppointment-details-view-widget" style="margin-top: 24px;">
                <div class="CdsDashboardAppointment-details-view-widget-header">
                    <h2 class="CdsDashboardAppointment-details-view-widget-title">
                        <span>📝</span>
                        Additional Information
                    </h2>
                </div>
                <div class="CdsDashboardAppointment-details-view-widget-body">
                    {!! html_entity_decode($appointment_data->additional_info) ?? '' !!}
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="CdsDashboardAppointment-details-view-widget-column">
            <!-- Appointment Details -->
            <div class="CdsDashboardAppointment-details-view-widget">
                <div class="CdsDashboardAppointment-details-view-widget-header">
                    <h2 class="CdsDashboardAppointment-details-view-widget-title">
                        <span>ℹ️</span>
                        Details
                    </h2>
                </div>
                <div class="CdsDashboardAppointment-details-view-widget-body">
                    <div class="CdsDashboardAppointment-details-view-info-list">
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">
                                <span>🆔</span>
                                Booking ID
                            </span>
                            <span class="CdsDashboardAppointment-details-view-info-row-value">{{ $appointment_data->unique_id ?? 'NA' }}</span>
                        </div>
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">
                                <span>👥</span>
                                Service Type
                            </span>
                            <span class="CdsDashboardAppointment-details-view-info-row-value">{{ $appointment_data->service->name ?? '' }}</span>
                        </div>
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">
                                <span>💻</span>
                                Mode
                            </span>
                            <span class="CdsDashboardAppointment-details-view-info-row-value">{{ $appointment_data->appointment_mode ?? '' }}</span>
                        </div>
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">
                                <span>💳</span>
                                Payment Status
                            </span>
                            <span class="CdsDashboardAppointment-details-view-info-row-value">{{ $appointment_data->payment_status ?? '' }}</span>
                        </div>
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">
                                <span>🌍</span>
                                Your Timezone
                            </span>
                            <span class="CdsDashboardAppointment-details-view-info-row-value">{{ $clientTz }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Slot Widget -->
            <div class="CdsDashboardAppointment-details-view-widget" style="margin-top: 24px;">
                <div class="CdsDashboardAppointment-details-view-widget-header">
                    <h2 class="CdsDashboardAppointment-details-view-widget-title">
                        <span>🕐</span>
                        Time Slot
                    </h2>
                </div>
                <div class="CdsDashboardAppointment-details-view-widget-body">
                    <div class="CdsDashboardAppointment-details-view-info-list">
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">Your Time ({{ $clientTz }})</span>
                            <span class="CdsDashboardAppointment-details-view-info-row-value">
                                {{ $appointment_data->start_time_converted }} - {{ $appointment_data->end_time_converted }}
                            </span>
                        </div>
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">Professional's Time ({{ $profTz }})</span>
                            <span class="CdsDashboardAppointment-details-view-info-row-value">
                                {{ date("h:i A", strtotime($startInProfTz)) }} - {{ date("h:i A", strtotime($endInProfTz)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Widget -->
            <div class="CdsDashboardAppointment-details-view-widget" style="margin-top: 24px;">
                <div class="CdsDashboardAppointment-details-view-widget-header">
                    <h2 class="CdsDashboardAppointment-details-view-widget-title">
                        <span>📍</span>
                        Location
                    </h2>
                </div>
                <div class="CdsDashboardAppointment-details-view-widget-body">
                    <div class="CdsDashboardAppointment-details-view-info-list">
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">Company</span>    
                            <span class="CdsDashboardAppointment-details-view-info-row-value">{{ $fetchLoctimezone->company->company_name ?? 'N/A' }}</span>
                        </div>
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">Address</span>
                            <span class="CdsDashboardAppointment-details-view-info-row-value">{{ $fetchLoctimezone->address_1 ?? '' }}</span>
                        </div>
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">City</span>
                            <span class="CdsDashboardAppointment-details-view-info-row-value">
                                {{ $fetchLoctimezone->city ?? '' }}{{ $fetchLoctimezone->state ? ', ' . $fetchLoctimezone->state : '' }}
                            </span>
                        </div>
                        <div class="CdsDashboardAppointment-details-view-info-row">
                            <span class="CdsDashboardAppointment-details-view-info-row-label">Country</span>
                            <span class="CdsDashboardAppointment-details-view-info-row-value">{{ $fetchLoctimezone->country ?? '' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Widget -->
            <div class="CdsDashboardAppointment-details-view-widget CdsDashboardAppointment-details-view-price-widget" style="margin-top: 24px;">
                <span class="CdsDashboardAppointment-details-view-price-currency">{{ currencySymbol($appointment_data->currency) }}</span>
                <div class="CdsDashboardAppointment-details-view-price-value">{{ $appointment_data->price }}</div>
                <p class="CdsDashboardAppointment-details-view-price-description">Total booking amount</p>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="CdsDashboardAppointment-details-view-action-cards">
    @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'reschedule-appointment'
                ]))
        @if((($appointment_data->status == "draft" || $appointment_data->status == "awaiting") || 
                ($appointment_data->payment_status != "paid") || 
                ($appointment_data->appointment_date > now())) && 
                ($appointment_data->status != "cancelled") && 
                ($appointment_data->status != "approved"))
        <a href="{{ baseUrl('appointment-booking/save-booking/'.$appointment_data->unique_id) }}" class="CdsDashboardAppointment-details-view-action-card"
                       >
        @else
        <a href="javascript:;" class="CdsDashboardAppointment-details-view-action-card">
        @endif
            <div class="CdsDashboardAppointment-details-view-action-card-icon">🔄</div>
            <h3 class="CdsDashboardAppointment-details-view-action-card-title">Reschedule</h3>
            <p class="CdsDashboardAppointment-details-view-action-card-description">Change your appointment time</p>
        </a>
     @endif
    
        @if($appointment_data->reminder?->user_id!=auth()->user()->id  && (($appointment_data->status == "approved" && ($appointment_data->payment_status == "paid") && ($appointment_data->appointment_date > date('Y-m-d')))))
              @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'reminder'
                ]))
        <a href="javascript:;" data-modal="setReminder" onclick="showPopup('<?php echo baseUrl('appointments/appointment-booking/set-reminder/' . $appointment_data->unique_id) ?>')" class="CdsDashboardAppointment-details-view-action-card modal-toggle">
            <div class="CdsDashboardAppointment-details-view-action-card-icon">📧</div>
            <h3 class="CdsDashboardAppointment-details-view-action-card-title">Set Reminder</h3>
            <p class="CdsDashboardAppointment-details-view-action-card-description">Get email notification</p>
        </a>
        @endif
        @elseif($appointment_data->reminder && $appointment_data->reminder?->user_id==auth()->user()->id)
        <a href="javascript:;" data-modal="setReminder"  class="CdsDashboardAppointment-details-view-action-card modal-toggle">
            <div class="CdsDashboardAppointment-details-view-action-card-icon">📧</div>
            <h3 class="CdsDashboardAppointment-details-view-action-card-title"> Reminder Set for {{$appointment_data->reminder?->reminder_date .' '.$appointment_data->reminder?->reminder_time}}</h3>
            <p class="CdsDashboardAppointment-details-view-action-card-description">Get email notification</p>
        </a>
        @endif
        @if($appointment_data->status != 'cancelled' && $appointment_data->status != 'archieved' && $appointment_data->status != 'completed' && $appointment_data->status != 'non-conducted' && $appointment_data->appointment_date >date('Y-m-d'))
              @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'cancel'
                ]))
        <a href="{{baseUrl('appointments/appointment-booking/cancel-appointment/'.$appointment_data->unique_id)}}" class="CdsDashboardAppointment-details-view-action-card danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
            <div class="CdsDashboardAppointment-details-view-action-card-icon">❌</div>
            <h3 class="CdsDashboardAppointment-details-view-action-card-title">Cancel</h3>
            <p class="CdsDashboardAppointment-details-view-action-card-description">Cancel this appointment</p>
        </a>
        @endif
        @endif
    </div>
</div>

			</div>
	
	</div>
  </div>








@endsection

@section('javascript')
<!-- Keep your existing JavaScript section as is -->
<!-- JS Implementing Plugins -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ apiKeys('GOOGLE_API_KEY') }}&libraries=places"></script>

<script>
    // google address
    google.maps.event.addDomListener(window, 'load', initGoogleAddress);

    function initGoogleAddress() {
        $(".google-address").each(function () {
            var address = $(this).attr("id");

            var autocomplete = new google.maps.places.Autocomplete(
                document.getElementById(address), {
                    types: ['geocode']
                });
            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    document.getElementById(address).textContent = "No details available for input: '" +
                        place.name + "'";
                    return;
                }

                var address = '';
                if (place.address_components) {
                    address = [
                        (place.address_components[0] && place.address_components[0].short_name ||
                            ''),
                        (place.address_components[1] && place.address_components[1].short_name ||
                            ''),
                        (place.address_components[2] && place.address_components[2].short_name ||
                            '')
                    ].join(' ');
                }

                // document.getElementById('address').textContent = 'Address: ' + place.formatted_address;
            });
        })

    }

   
    Dropzone.autoDiscover = false;
    var pfDropzone;
    var icFgDropzone;
    var lcFgDropzone;
    var pf_files_uploaded = [];
    var ic_files_uploaded = [];
    var lc_files_uploaded = [];
    var timestamp = "{{time()}}";

    $(document).ready(function () {

        // for proff of identify
        pfDropzone = new Dropzone("#pf-file-dropzone", {
            url: BASEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" +
                timestamp,
            autoProcessQueue: false, // Prevent automatic upload
            addRemoveLinks: true,
            maxFilesize: 6,
            acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
            parallelUploads: 40,
            maxFiles: 60,
            success: function (file, response) {

                pf_files_uploaded.push(response.filename);
            },
            error: function (file, errorMessage) {
                isError = 1;
                this.removeFile(file);
            },
            queuecomplete: function () {
                var file_value = pf_files_uploaded.join(",");
                $('#pf-files').val(file_value); // Store filenames in a hidden input
            }
        });

        // for incorporation certificate
        icFgDropzone = new Dropzone("#ic-file-dropzone", {
            url: BASEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" +
                timestamp,
            autoProcessQueue: false, // Prevent automatic upload
            addRemoveLinks: true,
            maxFilesize: 6,
            acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
            parallelUploads: 40,
            maxFiles: 60,
            success: function (file, response) {

                ic_files_uploaded.push(response.filename);
            },
            error: function (file, errorMessage) {
                isError = 1;
                this.removeFile(file);
            },
            queuecomplete: function () {
                var file_value = ic_files_uploaded.join(",");
                $('#ic-files').val(file_value); // Store filenames in a hidden input
            }
        });

        // for incorporation certificate
        lcFgDropzone = new Dropzone("#lc-file-dropzone", {
            url: BASEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" +
                timestamp,
            autoProcessQueue: false, // Prevent automatic upload
            addRemoveLinks: true,
            maxFilesize: 6,
            acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
            parallelUploads: 40,
            maxFiles: 60,
            success: function (file, response) {

                lc_files_uploaded.push(response.filename);
            },
            error: function (file, errorMessage) {
                isError = 1;
                this.removeFile(file);
            },
            queuecomplete: function () {
                var file_value = lc_files_uploaded.join(",");
                $('#lc-files').val(file_value); // Store filenames in a hidden input
            }
        });

        // for about company
        // intialize description editor
        if ($("#about").val() !== undefined) {
            initEditor("about");
        }



        initSelect();

        dobDatePicker("date_of_birth");

        initPastDatePicker("license_start_date");



        $("#company-form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#company-form").attr('action');
            console.log(formData);
            var is_valid = formValidation("company-form");
            if (!is_valid) {
                return false;
            }
            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                    } else {
                        validation(response.message);
                    }
                },
                error: function () {
                    internalError();
                }
            });

        });

        $("#company-description").submit(function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#company-description").attr('action');
            console.log(formData);
            var is_valid = formValidation("company-description");
            if (!is_valid) {
                return false;
            }
            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                    } else {
                        validation(response.message);
                    }
                },
                error: function () {
                    internalError();
                }
            });

        });

        $("#contact-form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#contact-form").attr('action');

            var is_valid = formValidation("contact-form");
            if (!is_valid) {
                return false;
            }
            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        location.reload();
                    } else {
                        validation(response.message);
                    }
                },
                error: function () {
                    internalError();
                }
            });

        });

        $("#category-description").submit(function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#category-description").attr('action');
            console.log(formData);
            var is_valid = formValidation("category-description");
            if (!is_valid) {
                return false;
            }
            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                    } else {
                        validation(response.message);
                    }
                },
                error: function () {
                    internalError();
                }
            });

        });

        $("#domain-form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#domain-form").attr('action');
            var is_valid = formValidation("domain-form");
            if (!is_valid) {
                return false;
            }
            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        location.reload();
                    } else {
                        validation(response.message);
                    }
                },
                error: function () {
                    internalError();
                }
            });

        });


        $("#licence-form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#licence-form").attr('action');
            var is_valid = formValidation("licence-form");
            if (!is_valid) {
                return false;
            }
            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        location.reload();
                    } else {
                        validation(response.message);
                    }
                },
                error: function () {
                    internalError();
                }
            });
        });
        // verify proof form
        
    });

   
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const bgDivs = document.querySelectorAll(".cds-professional-responsive-banner-bg");

        bgDivs.forEach(div => {
            const bgImage = div.getAttribute("data-bg"); // Get the image URL from the data attribute
            div.style.backgroundImage = `url(${bgImage})`;

            // Optional: Adjust height dynamically for truly responsive behavior
            const updateHeight = () => {
                if (!div.style.height) {
                    div.style.height = `${div.offsetWidth * 0.5625}px`; // 16:9 aspect ratio
                }
            };

            window.addEventListener("resize", updateHeight);
            updateHeight(); // Initial height adjustment
        });
    });
</script>
@endsection