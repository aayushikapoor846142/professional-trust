@extends('admin-panel.layouts.app')

@section('content')
 <div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
<!-- Tabs Navigation -->
    <div class="dashboard-tabs">
        <a href="#" class="dashboard-tab dashboard-tab-active tab-link" data-tab="basic-details-tab">Basic Details</a>
        <a href="#" class="dashboard-tab tab-link" data-tab="upcoming-invoice-tab">Upcoming Invoice</a>
    </div>
      <div id="invoice-loader" class="mt-50" style="display: none;">
                @include('components.skelenton-loader.my-membership-plans-skeleton')
            </div>
    <!-- Content Sections -->

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
   <div class="dashboard-section dashboard-section-active" id="basic-details-tab">
        <div class="dashboard-box">
            <div class="dashboard-box-body">
                <div class="dashboard-segments">
                
                    <div class="membership-content">
                        <!-- Basic details will load here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="dashboard-section" id="upcoming-invoice-tab">
        <div class="dashboard-box">
            <div class="dashboard-box-body">
                <div class="dashboard-segments">
                 
                
                    <div class="invoice-content">
                        <!-- Upcoming invoice will load here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
			</div>
	
	</div>
  </div>
</div>   


@endsection

@section('javascript')
 
<script type="text/javascript">
function loadBasicDetails() {
        $("#invoice-loader").show();
    $.ajax({
        type: "POST",
      url: BASEURL + '/my-membership-plans/ajax-list',
        data: {
            _token: csrf_token,
            professional_id: "{{ $professional_id }}"
        },
        success: function(response) {
               $("#invoice-loader").hide();
            $(".membership-content").html(response.contents);
        },
        error: function(xhr) {
           $("#invoice-loader").hide();
            $(".membership-content").html("<p>Error loading basic details.</p>");
        }
    });
}

function loadUpcomingInvoice() {
     $(".invoice-content").html("");
    $("#invoice-loader").show();
    $.ajax({
        type: "POST",
            url: BASEURL + '/my-membership-plans/upcoming-subscription-history',
        data: {
            _token: csrf_token,
            professional_id: "{{ $professional_id }}"
        },
        success: function(response) {
            $("#invoice-loader").hide();
            $(".invoice-content").html(response.contents);
        },
        error: function(xhr) {
            $("#invoice-loader").hide();
            $(".invoice-content").html("<p>Error loading invoices.</p>");
        }
    });
}

$(document).ready(function() {
    // Load basic details immediately
    loadBasicDetails();

    // Handle tab switching
    $(".tab-link").on("click", function(e) {
        e.preventDefault();
        var tabId = $(this).data("tab");
        
        $(".tab-link").removeClass("dashboard-tab-active");
        $(this).addClass("dashboard-tab-active");

        $(".dashboard-section").removeClass("dashboard-section-active");
        $("#" + tabId).addClass("dashboard-section-active");

        // Load invoice data only when invoice tab is opened first time
        if (tabId === "upcoming-invoice-tab") {
            loadUpcomingInvoice();
        }
    });
});
</script>


@endsection