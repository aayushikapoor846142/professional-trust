@extends('admin-panel.layouts.app')
@section('styles')
  <link href="{{ url('assets/css/27-CDS-my-services.css') }}" rel="stylesheet" />
<link href="{{ url('assets/css/30-CDS-service-configure.css') }}" rel="stylesheet" />
<link href="{{ url('assets/css/custom-file-upload.css') }}" rel="stylesheet" />
<style>
.social-account-links {
    margin-top: 10px;
}

.social-account-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.social-account-item:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.1);
}

.social-account-name {
    font-weight: 500;
    color: #495057;
}

.social-account-status .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.social-account-actions {
    margin-top: 10px;
}

.social-account-actions .btn {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.social-account-actions .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert {
    border-radius: 6px;
    border: none;
    font-size: 0.875rem;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

/* Loading spinner animation */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Alert positioning */
.alert.position-fixed {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border-radius: 8px;
}
</style>
@endsection
@section('page-submenu')
{!! pageSubMenu('my-profile') !!}
@endsection
@php 
if(!isset($user)){
    $user = auth()->user();
}
@endphp
@section('content')
<section class="cds-t25n-content-professional-profile-section">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="cds-t25n-content-professional-profile-container pt-4">
                    <div class="cds-t25n-content-professional-profile-container-top mt-0">
                        <div class="cds-professional-banner">
                            @if ($user->banner_image != '')
                                <div id="profile-banner" class="cds-t25n-content-professional-profile-container-top-banner cds-professional-responsive-banner-bg" data-bg="{{ userBannerDirUrl($user->banner_image)  }}"  style="background-image: url('{{ userBannerDirUrl($user->banner_image) }}');"> </div>
                            @else
                                <div id="profile-banner" class="cds-t25n-content-professional-profile-container-top-banner cds-professional-responsive-banner-bg" data-bg="{{ url('assets/images/c-profile-bg.jpg') }}" style="background-image: url('assets/images/c-profile-bg.jpg');"> </div>
                            @endif  
                            <a onclick="showPopup('<?php echo baseUrl('/crop-banner-image') ?>')" href="javascript:;" class="cds-edit-profile-banner"><i class="fa fa-edit"></i> Edit</a>
                        </div> 
                        <div class="cds-t25n-content-professional-profile-container-top-professional-details">
                            <div class="cds-t25n-content-professional-profile-container-top-professional-details-header">
                                <div class="cds-t25n-content-professional-profile-container-top-professional-image ">
                                    {!! getProfeilImage($user->profile_image,$user->id) !!}
                                    <a onclick="showPopup('<?php echo baseUrl('/crop-user-image') ?>')" href="javascript:;" class="cds-edit-profile-image"><i class="fa fa-edit"></i></a>
                                </div>                                
                                <div class="cds-t25n-content-professional-profile-container-top-professional-actions">
                                    <ul>
                                        <li>
                                            <a href="{{baseUrl('message-centre')}}">Message</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="cds-t25n-content-professional-profile-container-top-professional-details-body">
                                <div class="cds-t25n-content-professional-profile-container-top-professional-information">
                                    <div class="cds-t25n-content-professional-profile-container-top-professional-information-personal">
                                        <h4>{{$user->first_name." ".$user->last_name}}<span class="cds-licence-status active"><span></span>{{$user->status}}</span></h4>
                                        <span >{{$user->professionalLicense->title??''}}</span>
                                        <ul>
                                            <li>{{$user->primaryCompanyAddress->country??'N/A'}},</li>
                                            <li></li>
                                            <li>{{$user->primaryCompanyAddress->state??'N/A'}},</li>
                                            <li></li>
                                            <li>{{$user->primaryCompanyAddress->city??'N/A'}}</li>
                                            <li></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="cds-t25n-content-professional-profile-container-top-professional-details-footer">
                                    <div class="cds-t25n-content-professional-profile-container-top-professional-information-regulator">
                                        <span>
                                            Regulatory Body <i class="fa-sharp-duotone fa-thin fa-chevron-right"></i>
                                            <a href="javascript:;" target="_blank">{{$user->professionalLicense->regulatoryBody->name??'N/A'}}</a>
                                        </span>
                                    </div>
                                    <div class="cds-t25n-content-professional-profile-container-top-professional-information-regulator">
                                        <span>Licence No. <i class="fa-sharp-duotone fa-thin fa-chevron-right"></i> <a href="#" target="_blank">{{$user->professionalLicense->license_number??'N/A'}}</a></span>
                                        <span class="licence-class">Class <i class="fa-sharp-duotone fa-thin fa-chevron-right"></i> <a href="#" target="_blank">{{$user->professionalLicense->class_level??'N/A'}}</a></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cds-t25n-content-professional-profile-container-main-body">
                        <div class="cds-t25n-content-professional-profile-container-main-navigation">
                        </div>
                        <div class="cds-t25n-content-professional-profile-container-main-body-information">
                            <div class="cds-t25n-content-professional-profile-container-main-body-information-exp">
                                <div class="cds-t25n-content-professional-profile-container-main-body-information-expertise-container">
                                    @if(isset($template))
                                        @include("admin-panel.04-profile.profile.".$template)
                                    @endif
                                    @if(isset($service_template))
                                        @include("admin-panel.04-profile.manage-services.".$service_template)
                                    @endif
                                </div>
                                @if(isset($showSidebar) && $showSidebar)
                                <div class="cdsTYDashboard-profile-sidebar">
                                    @if($template == 'feed-manage')
                                    <div class="cdsTYDashboard-profile-edit-links">
                                        <div class="cdsTYDashboard-profile-edit-links-header">Feeds</div>
                                        <div class="cdsTYDashboard-profile-edit-links-body">
                                            <ul>
                                                <li class="cdsTYDashboard-profile-inline-list"><a href="{{ baseUrl('feed/manage?type=my') }}"> My Feeds</a> <p>({{contFeedStatusData('my-feed')}})</p></li>
                                                <li class="cdsTYDashboard-profile-inline-list"><a href="{{ baseUrl('feed/manage?type=all') }}"> All Feeds</a> <p>({{contFeedStatusData('all-feed')}})</p></li>
                                                <li class="cdsTYDashboard-profile-inline-list"><a  href="{{ baseUrl('feed/manage?type=my&sub_type=draft') }}"> Draft Feeds </a> <p>({{contFeedStatusData('draft')}})</p></li>
                                                <li class="cdsTYDashboard-profile-inline-list"><a href="{{ baseUrl('feed/manage?type=my&sub_type=scheduled') }}"> Schedule Feeds </a><p> ({{contFeedStatusData('scheduled')}})</p></li>
                                                <li class="cdsTYDashboard-profile-inline-list"><a href="{{ baseUrl('feed/manage?type=commented') }}"> Commented </a><p>({{contFeedStatusData('commented')}})</p></li>
                                                <li class="cdsTYDashboard-profile-inline-list"><a href="{{ baseUrl('feed/manage?type=pinned') }}"> Pinned </a><p>({{contFeedStatusData('pinned')}})</p></li>
                                                <li class="cdsTYDashboard-profile-inline-list"><a href="{{ baseUrl('feed/manage?type=favourite') }}"> Favourite </a><p>({{contFeedStatusData('favourite')}})</p></li>
                                            </ul>
                                        </div>
                                    </div>
                                    @else
                                    <div
                                        class="cds-t25n-content-professional-profile-container-main-body-information-exp-details">
                                        <h4>@if(($license_detail->license_start_date??'') != '')
                                                {{getProfessionalExp($license_detail->license_start_date,'date')}}+
                                            @else
                                                0
                                            @endif
                                         </h4>
                                        <span>
                                            @if(($license_detail->license_start_date??'') != '')
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
                                                <li><a href="{{ baseUrl('profile/personal-detail') }}"> Personal Details</a> </li>
                                                <li><a href="{{ baseUrl('profile/companies') }}"> Companies</a> </li>
                                                <li><a href="{{ baseUrl('profile/license-detail') }}"> License Details </a></li>
                                                <li><a href="{{ baseUrl('profile/documents') }}"> Documents </a></li>
                                                <li><a href="{{ baseUrl('profile/banking-details') }}"> Banking Details </a></li>
                                                <li><a href="{{ baseUrl('profile/generate-qr-code') }}"> Generate Qr Code </a></li>
                                                <li><a href="{{ baseUrl('profile/ai-protection') }}"> AI Protection </a></li>
                                                @if($user->userDetail && $user->userDetail->additional_detail_form != '')
                                                    <li><a href="{{ baseUrl('profile/additional-detail') }}"> Additional Detail </a></li>
                                                @endif
                                                <li><a href="{{ baseUrl('profile/confirm-login') }}"> Login Devices </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                     <!-- Social Login Linking Section -->
                                    @php
                                        $anySocialLinked = ($user->social_connect == 1 && !empty($user->provider));
                                        $currentProvider = $user->provider;
                                    @endphp
                                    
                                    @if($anySocialLinked)
                                        <!-- Show linked social account status with unlink option -->
                                        <div class="cdsTYDashboard-profile-edit-links mt-3">
                                            <div class="cdsTYDashboard-profile-edit-links-header">
                                                <i class="fas fa-link me-1"></i>Social Account
                                            </div>
                                            <div class="cdsTYDashboard-profile-edit-links-body">
                                                <div class="social-account-links">
                                                    <div class="social-account-item mb-2">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                @if($currentProvider === 'google')
                                                                    <img src="{{ url('assets/images/icons/google.svg') }}" alt="Google" class="me-2" style="width: 16px; height: 16px;">
                                                                    <span class="social-account-name">Google</span>
                                                                @elseif($currentProvider === 'facebook')
                                                                    <img src="{{ url('assets/images/icons/facebook.svg') }}" alt="Facebook" class="me-2" style="width: 16px; height: 16px;">
                                                                    <span class="social-account-name">Facebook</span>
                                                                @elseif($currentProvider === 'linkedin')
                                                                    <img src="{{ url('assets/images/icons/linkedin.svg') }}" alt="LinkedIn" class="me-2" style="width: 16px; height: 16px;">
                                                                    <span class="social-account-name">LinkedIn</span>
                                                                @endif
                                                            </div>
                                                            <div class="social-account-status">
                                                                <span class="badge bg-success">Linked</span>
                                                            </div>
                                                        </div>
                                                        <div class="alert alert-success mt-2 p-2" style="font-size: 11px;">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            <strong>Success:</strong> Your {{ ucfirst($currentProvider) }} account is linked.
                                                        </div>
                                                        <div class="social-account-actions mt-2">
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="unlinkSocialAccount('{{ $currentProvider }}')">
                                                                <i class="fas fa-unlink me-1"></i>Unlink {{ ucfirst($currentProvider) }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section("javascript")
@push('scripts')
<script>
var users = '';
var feed_status = '';
</script>
<script src="{{ url('assets/js/feeds.js?v='.mt_rand())  }}"></script>
<script src="{{ url('assets/js/custom-file-upload.js') }}"></script>
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
    $(document).on("click", ".practise-more", function (event) {
        $('.pending-practice').removeClass('d-none');
        $(this).removeClass('practise-more');
        $(this).addClass('practise-less');
        $(this).text('less');
    });
    $(document).on("click", ".practise-less", function (event) {
        $('.pending-practice').addClass('d-none');
        $(this).removeClass('practise-less');
        $(this).addClass('practise-more');
        $(this).text('+'+$('.pending-practice').length+' More');
    });
    $(document).on("click", ".core-more", function (event) {
        $('.pending-core').removeClass('d-none');
        $(this).removeClass('core-more');
        $(this).addClass('core-less');
        $(this).text('less');
    });
    $(document).on("click", ".core-less", function (event) {
        $('.pending-core').addClass('d-none');
        $(this).removeClass('core-less');
        $(this).addClass('core-more');
        $(this).text('+'+$('.pending-core').length+' More');
    });

    // Social Account Functions
    function unlinkSocialAccount(provider) {
        // Validate provider
        const validProviders = ['google', 'facebook', 'linkedin'];
        if (!validProviders.includes(provider)) {
            showAlert('error', 'Invalid provider specified.');
            return;
        }

        if (!confirm('Are you sure you want to unlink your ' + provider.charAt(0).toUpperCase() + provider.slice(1) + ' account? This action cannot be undone.')) {
            return;
        }

        // Show loading state
        const button = event.target.closest('.social-account-actions').querySelector('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Unlinking...';
        button.disabled = true;

        // Make AJAX request to unlink
        $.ajax({
            url: '{{ baseUrl("panel/social/unlink") }}',
            type: 'POST',
            data: {
                provider: provider,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status) {
                    // Show success message
                    showAlert('success', response.message);
                    // Reload page to update the UI
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', response.message || 'Failed to unlink account.');
                    // Reset button
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'An error occurred while unlinking the account. Please try again.';
                
                // Try to get more specific error message from response
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 419) {
                    errorMessage = 'Session expired. Please refresh the page and try again.';
                } else if (xhr.status === 403) {
                    errorMessage = 'Access denied. Please check your permissions.';
                }
                
                showAlert('error', errorMessage);
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    }

    function showAlert(type, message) {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Add to page
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>
@endsection