@extends('admin-panel.layouts.app')

@section('page-submenu')
{!! pageSubMenu('appointment-system') !!}
@endsection
@section('styles')
<link href="{{ url('assets/css/CDS-appointment-settings.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
<div class="CdsTYDashboardAppointment-settings-tabs">
           @if(checkPrivilege([
                'route_prefix' => 'panel.time-duration',
                'module' => 'professional-time-duration',
                'action' => 'list'
            ]))
        <a href="#" class="CdsTYDashboardAppointment-settings-tab CdsTYDashboardAppointment-settings-active tab-link" data-tab="CDS-timeduration-tab" data-href="{{ baseUrl('time-duration') }}">Time Duration</a>
        @endif

           @if(checkPrivilege([
                'route_prefix' => 'panel.appointment-types',
                'module' => 'professional-appointment-types',
                'action' => 'list'
            ]))     

        <a href="#" class="CdsTYDashboardAppointment-settings-tab tab-link" data-tab="CDS-appointment-type-tab" data-href="{{ baseUrl('appointment-types') }}">Appointment Type</a>
     @endif
    </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <!-- Content Sections -->
    <div class="CdsTYDashboardAppointment-settings-section CdsTYDashboardAppointment-setting-tab-active" id="CDS-timeduration-tab">
        <div class="cds-tab-content" id="time-duration-content"></div>
    </div>
    
    <div class="CdsTYDashboardAppointment-settings-section" id="CDS-appointment-type-tab">
        <div class="cds-tab-content" id="appointment-types-content"></div>
    </div>
			</div>
	
	</div>
  </div>
</div>

@endsection

@section("javascript")
<script>
$(document).ready(function(){
    // Load initial tab content
    renderPage("{{ baseUrl('time-duration') }}", 'time-duration-content');
    
     var selectedTabId = sessionStorage.getItem('activeTab');
    if (selectedTabId) {
        sessionStorage.removeItem('activeTab'); // clean up after use
        setTimeout(function() {
            $('.tab-link[data-tab="' + selectedTabId + '"]').trigger('click');
        }, 100); // slight delay to allow DOM rendering
    }
    
    // Tab switching functionality
    $(document).on("click", ".tab-link", function(e){
        e.preventDefault();
        
        // Update active tab styling
        $(".CdsTYDashboardAppointment-settings-tab").removeClass("CdsTYDashboardAppointment-settings-active");
        $(this).addClass("CdsTYDashboardAppointment-settings-active");
        
        // Show/hide sections
        var tabId = $(this).data("tab");
        $(".CdsTYDashboardAppointment-settings-section").removeClass("CdsTYDashboardAppointment-setting-tab-active");
        $("#" + tabId).addClass("CdsTYDashboardAppointment-setting-tab-active");
        $(".cds-tab-content").html('');
        // Load content if not already loaded
        var href = $(this).data("href");
        var contentId = tabId === 'CDS-timeduration-tab' ? 'time-duration-content' : 'appointment-types-content';
        
        if ($("#" + contentId).html().trim() === '') {
            renderPage(href, contentId);
        }
    });
});

function renderPage(href, containerId){
    $.ajax({
        type: "GET",
        url: href,
        dataType: 'json',
        beforeSend:function(){
            $("#" + containerId).html('<div class="text-center py-4"><i class="fa fa-spin fa-spinner fa-3x"></i></div>');
        },
        success: function(data) {
            $("#" + containerId).html(data.contents);
            $("#rightSlidePanel").removeClass("active");
        },
        error: function() {
            $("#" + containerId).html('<div class="text-center text-danger py-4">Error loading content. Please try again.</div>');
        }
    });
}
</script>
@endsection