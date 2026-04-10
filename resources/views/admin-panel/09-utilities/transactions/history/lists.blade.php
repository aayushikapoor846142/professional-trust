@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('transactions') !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/common-table-redesign.css') }}">
@endsection
@section('content')
 
 <div class="cds-t25n-content-professional-profile-container-main-navigation">

        <div class="dashboard-tabs">
            <a href="#" class="dashboard-tab dashboard-tab-active tab-link" data-tab="onetime"> One Time Payment</a>
            <a href="#" class="dashboard-tab tab-link" data-tab="monthly">Monthly Payment</a>
        </div>
					
				</div>
 <section class="cdsTYSupportDashboard-amount-contributed">
				
				<div id="onetime" class="tab-content">
					@include('admin-panel.09-utilities.transactions.history.onetime-payment-lists')
				</div>
				<div id="monthly" class="tab-content d-none">
                	@include('admin-panel.09-utilities.transactions.history.monthly-payment-lists')
				</div>
            </section>



<!-- End Content -->
@endsection

@section('javascript')
<script>
       $(document).ready(function () {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'onetime'; 
       
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