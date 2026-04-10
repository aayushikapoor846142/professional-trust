
@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('settings') !!}
@endsection
@section('content')<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
<h1>Security Settings</h1>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
<div class="cdsTYSupportDashboard-amount-contributed">
				<div class="cds-t25n-content-professional-profile-container-main-navigation">
					<ul class="status-tabs">
						<li class="cds-active">
                            <a href="#" class="tab-link cds-active" data-tab="change_password">Change Password</a>
                        </li>
					</ul>
				</div>
				<div id="change_password" class="tab-content">
					@include('admin-panel.04-profile.security-settings.change-password')
				</div>
                
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
        const activeTab = urlParams.get('tab') || 'change_password'; 
       
        function switchToTab(tab) {
            $('.status-tabs li').removeClass('cds-active');
            $('.tab-content').addClass('d-none');
            $('.tab-link[data-tab="' + tab + '"]').parent('li').addClass('cds-active');
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