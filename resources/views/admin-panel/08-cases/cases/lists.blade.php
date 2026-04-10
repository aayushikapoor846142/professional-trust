@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('cases') !!}
@endsection
@section('styles')
<link href="{{ url('assets/css/20-CDS-cases-list.css') }}" rel="stylesheet" />
<link href="{{ url('assets/css/20-CDS-cases-grid.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/css/42-CDS-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/20-1-CDS-cases-list.css') }}">
@endsection
@section('content')


<!-- Content -->
<div class="CDSPostCaseNotifications-list-view02-refresh-bar">
    <div class="CDSPostCaseNotifications-list-view02-refresh-progress"></div>
</div>
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
        <!-- Header -->
        <div class="CDSPostCaseNotifications-list-view02-header">
            <h1>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
                {{$pageTitle}}
            </h1>
            <div class="CDSPostCaseNotifications-list-view02-live-indicator">
                <span class="CDSPostCaseNotifications-list-view02-live-dot"></span>
                LIVE
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="CDSPostCaseNotifications-list-view-stats-container">
            <div class="CDSPostCaseNotifications-list-view-stat-card">
                <span class="CDSPostCaseNotifications-list-view-stat-number" id="activeCount">{{$totalCase}}</span>
                <span class="CDSPostCaseNotifications-list-view-stat-label">Active Cases</span>
            </div>
            <div class="CDSPostCaseNotifications-list-view-stat-card">
                <span class="CDSPostCaseNotifications-list-view-stat-number" id="proposalCount">{{$totalProposal}}</span>
                <span class="CDSPostCaseNotifications-list-view-stat-label">Proposals</span>
            </div>
            <div class="CDSPostCaseNotifications-list-view-stat-card">
                <span class="CDSPostCaseNotifications-list-view-stat-number" id="proCount">{{countCase('unread_case')}}</span>
                <span class="CDSPostCaseNotifications-list-view-stat-label">Unread Cases</span>
            </div>
        </div>
        @include('admin-panel.08-cases.cases.header-search')
        <!-- Tabs Navigation and Live Updates Section -->
        <a href="javascript:;" onclick="switchToListView()" class="CdsTYButton-btn-primary" id="listViewBtn">List View</a>
        <a href="javascript:;" onclick="switchToGridView()" class="btn btn-outline-primary" id="gridViewBtn">Grid View</a>
        <div class="CDSPostCaseNotifications-list-view-tabs-section">
            <div class="CDSPostCaseNotifications-list-view-tabs-container">
                <a href="{{baseUrl('cases?type=all')}}" class="CDSPostCaseNotifications-list-view-tab  {{ $type == 'all' ? 'CDSPostCaseNotifications-list-view-tab-active' : '' }}" data-tab="all">
                    All <span class="CDSPostCaseNotifications-list-view-tab-count">({{countCase('all')}})</span>
                </a>
                <a href="{{baseUrl('cases?type=unread_case')}}" class="CDSPostCaseNotifications-list-view-tab  {{ $type == 'unread_case' ? 'CDSPostCaseNotifications-list-view-tab-active' : '' }}" data-tab="unread">
                    Unread Case <span class="CDSPostCaseNotifications-list-view-tab-count">({{countCase('unread_case')}})</span>
                </a>
                <a href="{{baseUrl('cases?type=viewed_case')}}" class="CDSPostCaseNotifications-list-view-tab {{ $type == 'viewed_case' ? 'CDSPostCaseNotifications-list-view-tab-active' : '' }}" data-tab="viewed">
                    Viewed Case <span class="CDSPostCaseNotifications-list-view-tab-count">({{countCase('viewed_case')}})</span>
                </a>
                <a href="{{baseUrl('cases?type=proposal_sent')}}" class="CDSPostCaseNotifications-list-view-tab {{ $type == 'proposal_sent' ? 'CDSPostCaseNotifications-list-view-tab-active' : '' }}" data-tab="proposal-sent">
                    Proposal Sent <span class="CDSPostCaseNotifications-list-view-tab-count">({{countCase('proposal_sent')}})</span>
                </a>
                <a href="{{baseUrl('cases?type=favourite')}}" class="CDSPostCaseNotifications-list-view-tab  {{ $type == 'favourite' ? 'CDSPostCaseNotifications-list-view-tab-active' : '' }}" data-tab="favourite">
                    Favourite <span class="CDSPostCaseNotifications-list-view-tab-count">({{countCase('favourite')}})</span>
                </a>
                <a href="{{baseUrl('cases?type=award_case')}}" class="CDSPostCaseNotifications-list-view-tab {{ $type == 'award_case' ? 'CDSPostCaseNotifications-list-view-tab-active' : '' }}" data-tab="awarded">
                    Awarded <span class="CDSPostCaseNotifications-list-view-tab-count">(0)</span>
                </a>
            </div>
            
            @if(!empty($privacySettings))
                <form id="caseNotificationForm" class="js-validate" action="{{ baseUrl('/cases/settings') }}" method="post">
                    @csrf
                    <input type="hidden" name="module_settings" value="{{$record->unique_id ?? ''}}">
                    @if(!empty($privacySettings))
                        @foreach($privacySettings as $key => $settings)
                                <input type="hidden" name="settings[{{ $key }}][privacy_option_id]" value="{{ $settings->id }}">
                                <input type="hidden" name="settings[{{ $key }}][type]" value="toogle">
                            {!! FormHelper::formToogleCheckbox([
                                'name' => "settings[$key][value]",
                                'label' => $settings->action_label,
                                'checkbox_class' => 'case-notification-toogle',                        
                                'options' => json_decode($settings->options,true),
                                'id' => 'radio-{{$settings->unique_id}}',
                                'value' => 'enable',
                                'checked' =>  !empty($settings->userPrivacy) && $settings->userPrivacy->privacy_option_value != ""
                                        ? $settings->userPrivacy->privacy_option_value
                                        : '',
                                ]) 
                            !!}
                        @endforeach
                    @endif
                </form>
            @endif
        </div>

        

			 </div>
             <!-- new -->

            <div class="CDSPostCaseNotifications-compact-list-container">
                <div class="CDSPostCaseNotifications-compact-list-header">
                    <div class="CDSPostCaseNotifications-compact-list-header-item">Case Details</div>
                    <div class="CDSPostCaseNotifications-compact-list-header-item">Status</div>
                    <div class="CDSPostCaseNotifications-compact-list-header-item">Client</div>
                    <div class="CDSPostCaseNotifications-compact-list-header-item">Proposals</div>
                    <div class="CDSPostCaseNotifications-compact-list-header-item">Actions</div>
                </div>

                <div class="CDSPostCaseNotifications-compact-list-case-list" id="casesList">
                </div>
            </div>
             <!-- end new -->
			 <div class="CDSDashboardContainer-main-content-inner-body">
                <div class="CDSPostCaseNotifications-list-view02-container">
                    {{--<ul class="CDSPostCaseNotifications-list-view02-feed-list" id="casesList">
                        <!-- Cases will be dynamically added here -->
                    </ul>--}}

        <div class="CDSPostCaseNotifications-grid-view02-container">
            <div class="CDSPostCaseNotifications-grid-view02-feed-grid" id="gridCasesList">
            </div>
        </div>

        <div id="common-skeleton-loader" style="display:none;">
            @include('components.loaders.case-loader')              
        </div>
    </div>   

    <!-- Notification -->
    <div class="CDSPostCaseNotifications-list-view-notification" id="CDSPostCaseNotifications-notification"></div>
			</div>
	
	</div>
  </div>
</div>

<!-- End Content -->
@endsection

@section('javascript')
<script>
    var type = "{{$type}}";
    var liveChat = "{{$liveChat}}";
</script>
<script src="{{ url('assets/plugins/global-notification.js?v='.mt_rand()) }}" type="text/javascript"></script>
<script type="text/javascript">
listCaseData();

function confirmFavourite(e) {
    var url = $(e).attr("data-href");
    var type = $(e).attr("data-type");
    var message = "";
    if(type == 'add'){
        message = "Are you mark as favourite?";
    }else{
        message = "Are you remove from favourite?";
    }
    Swal.fire({
        title: message,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}

$(document).ready(function () {

    // Attach event handlers after DOM is ready
    $('.case-notification-toogle').on('change', function () {
        $('#caseNotificationForm').submit();
    });

    // Single event handler for main service change
    $('#parent_service_id').on('change', function () {
        var service_id = $(this).val();
        if (service_id) {
            serviceList(service_id, 'sub_service_id');
        } else {
            // If no service selected, clear sub-service and refresh
            $('#sub_service_id').html('<option value="">All SubService</option>');
        }
    });
});

function serviceList(service_id, id) {
    $.ajax({
        url: "{{ baseUrl('cases/fetch-sub-service') }}",
        data: {
            service_id: service_id
        },
        dataType: "json",
        beforeSend: function() {
            console.log('AJAX request started');
            $("#" + id).html('');
        },
        success: function(response) {
            console.log('AJAX response received:', response);
            if (response.status == true) {
                $("#" + id).html(response.options);
                // Clear sub-service selection and refresh cases list when main service changes
                $("#" + id).val('').trigger('change');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            console.error('Response text:', xhr.responseText);
        }
    });
}
</script>
@endsection