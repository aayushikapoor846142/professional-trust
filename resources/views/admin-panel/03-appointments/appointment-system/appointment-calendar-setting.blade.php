@extends('admin-panel.layouts.app')
@if(checkPrivilege([
                        'route_prefix' => 'panel.appointments',
                        'module' => 'professional-appointments',
                        'action' => 'view'
                    ]))
                    @php
                    $canViewAppointments=true;
                    @endphp
@else
                    @php
                    $canViewAppointments=false;
                    @endphp
@endif
@if(checkPrivilege([
                        'route_prefix' => 'panel.appointments.block-dates',
                        'module' => 'professional-appointments-block-dates',
                        'action' => 'list'
                    ]))
                    @php
                    $canViewBlockDates=true;
                    @endphp
@else
                    @php
                    $canViewBlockDates=false;
                    @endphp
@endif
@php 
$page_arr = [
    'page_title' => 'Appointment Calendar ',
    'page_description' => 'Manage appointment calendar and block dates.',
    'page_type' => 'appointment-calendar',
    'canViewAppointments' => $canViewAppointments,
    'canViewBlockDates' => $canViewBlockDates,
    'appointmentsFeatureStatus' => $appointmentsFeatureStatus ?? null,
];
@endphp
@section('page-submenu')
{!! pageSubMenu('appointment-system',$page_arr) !!}
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
                @if(isset($appointmentsFeatureStatus))
                    @if(!$canViewAppointments)
                        <div class="alert alert-danger mb-3">
                            <strong>⚠ Appointments Management</strong><br>
                            {{ $appointmentsFeatureStatus['message']  }}
                           
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                                <strong>⚠ Appointments Management</strong><br>
                           
                            {{ $appointmentsFeatureStatus['message'] }}
                        </div>
                    @endif
                @endif
                <ul class="status-tabs">
            
            <li class="cds-active">
                <a class="tab-link" data-href="{{ baseUrl('/appointments/appointment-booking/calendar') }}" href="#">Appointment Calendar</a>
            </li>
                @if($canViewBlockDates)
            <li>
                <a class="tab-link" data-href="{{ baseUrl('appointments/block-dates') }}" href="javascript:;">Block Dates</a>
            </li>
            @endif
        </ul>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                @if($canViewAppointments)
                <div id="calendar-content">
                    @include('admin-panel.03-appointments.appointment-system.appointment-booking.calendar')
                </div>
                @if($canViewBlockDates)
                <div id="block-dates-content" style="display:none;">
                    @include('admin-panel.03-appointments.appointment-system.block-dates.lists')
                </div>
                @endif
                @else
                    <div class="text-center text-muted">
                        <p>You don't have permission to view appointments.</p>
                    </div>
                @endif
			</div>
	
	</div>
  </div>
</div>

@endsection


@section("javascript")

<script>
    $(document).ready(function() {
      
    // Cache the content containers
    var $calendarContent = $('#calendar-content');
    var $blockDatesContent = $('#block-dates-content');
    
    // Initially show calendar, hide block dates
    $calendarContent.show();
    $blockDatesContent.hide();
    
    $('.tab-link').on('click', function(e) {
       
        e.preventDefault();
        var url = $(this).data('href');
       
        $('.status-tabs li').removeClass('cds-active');
        $(this).parent().addClass('cds-active');
        
        if (url.includes('calendar')) {
            $blockDatesContent.hide();
            $calendarContent.show();
        } else {
            $calendarContent.hide();
            $blockDatesContent.show();
        }
        
        history.pushState(null, null, url);
    });
    
    window.onpopstate = function(event) {
        if (window.location.href.includes('calendar')) {
            $('.status-tabs li').removeClass('cds-active');
            $('.status-tabs li:first-child').addClass('cds-active');
            $blockDatesContent.hide();
            $calendarContent.show();
        } else if (window.location.href.includes('block-dates')) {
            $('.status-tabs li').removeClass('cds-active');
            $('.status-tabs li:last-child').addClass('cds-active');
            $calendarContent.hide();
            $blockDatesContent.show();
        }
    };
});
</script>
@endsection

