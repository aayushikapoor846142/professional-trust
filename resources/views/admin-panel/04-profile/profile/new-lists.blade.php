@extends('admin-panel.layouts.app')
@section('content')
<div class="ch-action">
                    <a href="{{ baseUrl('articles/add') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-plus fa-solid"></i>
                        Add New
                    </a>
                </div>
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="cds-t25n-content-professional-profile-container-main-body-information-exp">
                                            <div class="cds-t25n-content-professional-profile-container-main-body-information-expertise-container">
                                                @include("admin-panel.04-profile.profile.".$template)
                                            </div>
                                            @if(isset($showSidebar) && $showSidebar)
                                            <div class="cdsTYDashboard-profile-sidebar">
                                                <div class="cds-t25n-content-professional-profile-container-main-body-information-exp-details">
                                                    <h4>@if($license_detail->license_start_date != '')
                                                            {{getProfessionalExp($license_detail->license_start_date,'date')}}+
                                                        @else
                                                            0
                                                        @endif
                                                    </h4>
                                                    <span>
                                                        @if($license_detail->license_start_date != '')
                                                            {{getProfessionalExp($license_detail->license_start_date,'exp')}} Experience
                                                        @else
                                                            Years Experience
                                                        @endif
                                                        </span>
                                                </div>
                                                <div class="cdsTYDashboard-profile-edit-links">
                                                    <div class="cdsTYDashboard-profile-edit-links-header">Edit Profile</div>
                                                    <div class="cdsTYDashboard-profile-edit-links-body">
                                                        <ul>
                                                            <li><a href="{{ baseUrl('profile/personal-detail') }}"> Personal details</a> </li>
                                                            <li><a href="{{ baseUrl('profile/company-detail') }}"> Company details</a> </li>
                                                            <li><a href="{{ baseUrl('profile/license-detail') }}"> License details </a></li>
                                                            <li><a href="{{ baseUrl('profile/documents') }}"> Documents </a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
			</div>
	
	</div>
  </div>
</div>				
				

@endsection
@section("javascript")
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const bgDivs = document.querySelectorAll(".cds-professional-responsive-banner-bg");
        bgDivs.forEach(div => {
            const bgImage = div.getAttribute("data-bg");
            div.style.backgroundImage = `url(${bgImage})`;
            const updateHeight = () => {
                if (!div.style.height) {
                    div.style.height = `${div.offsetWidth * 0.5625}px`;
                }
            };
            window.addEventListener("resize", updateHeight);
            updateHeight();
        });
    });
</script>
@endsection