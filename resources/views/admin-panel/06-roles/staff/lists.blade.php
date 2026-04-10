
@extends('admin-panel.layouts.app')
@php 
$page_arr = [
    'page_title' => 'Staff Management',
    'page_description' => 'Create, manage, and organize your staff',
    'page_type' => 'active-staff',
    'canAddStaff' => $canAddStaff,
    'canAddStaffPermission' => checkPrivilege([
        'route_prefix' => 'panel.staff',
        'module' => 'professional-staff',
        'action' => 'add'
    ])
];
@endphp
@section('page-submenu')
{!! pageSubMenu('accounts', $page_arr) !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/common-table-redesign.css') }}">
@endsection
@section('content')
<!-- Content -->

<div class="container">
    @if(!$canAddStaff)
        <div class="alert alert-danger mb-3">
            <strong>⚠ Staff Management</strong><br>
            {{ $staffFeatureStatus['message']  }}
            <!-- @if(isset($staffFeatureStatus['current_count']) && isset($staffFeatureStatus['limit']))
                <br>Current Usage: {{ $staffFeatureStatus['current_count'] }} / {{ $staffFeatureStatus['limit'] == -1 ? 'Unlimited' : $staffFeatureStatus['limit'] }}
            @endif -->
        </div>
    @else
        <div class="alert alert-warning mb-3">
                <strong>⚠ Staff Management</strong><br>
            <!-- <strong>Current Staff:</strong> {{ $staffFeatureStatus['current_count'] }} / {{ $staffFeatureStatus['limit'] == -1 ? 'Unlimited' : $staffFeatureStatus['limit'] }}
            @if($staffFeatureStatus['limit'] != -1)
                <br><strong>Remaining Slots:</strong> {{ $staffFeatureStatus['remaining'] }}
                <br><strong>Usage:</strong> {{ $staffFeatureStatus['usage_percentage'] }}%
                @if($staffFeatureStatus['usage_percentage'] >= 80)
                    <span class="badge bg-warning">High Usage</span>
                @endif
            @endif -->
            {{ $staffFeatureStatus['message'] }}
        </div>
    @endif
    
    <div class="cds-t25n-content-professional-profile-container-main-navigation">
        <div class="dashboard-tabs">
            <a href="#" class="dashboard-tab dashboard-tab-active tab-link" data-tab="basic-details-tab" data-href="{{ baseUrl('staff/active-staffs-list') }}"> Active Staffs</a>
            <a href="#" class="dashboard-tab tab-link" data-tab="upcoming-invoice-tab" data-href="{{ baseUrl('staff/trash-staffs-list') }}">Trash Staffs</a>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 mb-3">
            <div id="render-page"></div>
        </div>
    </div>
</div>
<!-- End Content -->
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    renderPage("{{ baseUrl('staff/active-staffs-list') }}");
    // $(document).on("click", ".tab-link", function() {
    //     $(".tab-link").parents(".status-tabs").find("li").removeClass("cds-active");
    //     $(this).parents("li").addClass("cds-active");
    //     renderPage($(this).data("href"));
    // });

    $(document).on("click", ".tab-link", function(e) {
        e.preventDefault();

        // remove active class from all tabs
        $(".dashboard-tab").removeClass("dashboard-tab-active");

        // add active class to clicked tab
        $(this).addClass("dashboard-tab-active");

        // load page from data-href
        renderPage($(this).data("href"));
    });

});
function renderPage(href) {
    $.ajax({
        type: "GET",
        url: href,
        dataType: 'json',
        beforeSend: function() {
            $("#render-page").html('<div class="text-center"><i class="fa fa-spin fa-spinner"></i></div>');
        },
        success: function(data) {
            $("#render-page").html(data.contents);
            initSelect();
        },
    });
}
</script>
@endsection