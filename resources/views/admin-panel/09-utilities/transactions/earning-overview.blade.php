@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('earnings') !!}
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="cdsTYSupportDashboard-point-earned">
        <div class="cdsTYSupportDashboard-point-earned-container">
            @include("components.points-earn-progress-bar",['bg_user'=>auth()->user()])
        </div>
    </div>     
			</div>
	
	</div>
  </div>
</div>

<!-- End Content -->
@endsection

@section('javascript')

@endsection
