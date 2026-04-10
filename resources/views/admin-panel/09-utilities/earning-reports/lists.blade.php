@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('earnings') !!}
@endsection

@section('content')<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
Total Earnings: ${{totalProfessionalEarning('all',auth()->user()->id)}}
 <div class="dashboard-tabs">
            <a href="#" class="dashboard-tab dashboard-tab-active tab-link" data-tab="case-earning"> Case Earning</a>
            <a href="#" class="dashboard-tab tab-link" data-tab="appointment-earning">Appointment Earning</a>
             <a href="#" class="dashboard-tab tab-link" data-tab="other-earning">Other Earning</a>
        </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
	<div id="case-earning" class="tab-content">
					@include('admin-panel.09-utilities.earning-reports.case-lists')
				</div>
				<div id="appointment-earning" class="tab-content d-none">
                	@include('admin-panel.09-utilities.earning-reports.appointment-lists')
				</div>
                <div id="other-earning" class="tab-content d-none">
                	@include('admin-panel.09-utilities.earning-reports.global-invoice-lists')
				</div>
			</div>
	
	</div>
  </div>
</div>
 



<!-- End Content -->
@endsection

@section('javascript')
<script>
       $(document).ready(function () {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'case-earning'; 
       
        function switchToTab(tab) {
            $('.dashboard-tab').removeClass('dashboard-tab-active');
            $('.tab-content').addClass('d-none');
            $('.tab-link[data-tab="' + tab + '"]').addClass('dashboard-tab-active');
            $('#' + tab).removeClass('d-none');
        }

        switchToTab(activeTab);

        $('.tab-link').on('click', function (e) {
            e.preventDefault();
            const tab = $(this).data('tab');

            switchToTab(tab);

            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        });
    });
</script>


@endsection