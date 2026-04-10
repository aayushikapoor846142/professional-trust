@extends('admin-panel.layouts.app')
@if(checkPrivilege([
                        'route_prefix' => 'panel.appointments.block-dates',
                        'module' => 'professional-appointments-block-dates',
                        'action' => 'add'
                    ]))
                    @php
                    $canAddBlockDates=true;
                    @endphp
@else
                    @php
                    $canAddBlockDates=false;
                    @endphp
@endif
@php 
$page_arr = [
    'page_title' => 'Add Block Date',
    'page_description' => 'Add new block date for appointments.',
    'page_type' => 'add-block-date',
    'canAddBlockDates' => $canAddBlockDates,
    'appointmentsFeatureStatus' => $appointmentsFeatureStatus ?? null,
];
@endphp
@section('page-submenu')
{!! pageSubMenu('appointment-system',$page_arr) !!}
@endsection
@section('content')
                @if(isset($appointmentsFeatureStatus))
                    @if(!$canAddBlockDates)
                        <div class="alert alert-danger mb-3">
                            <strong>⚠ Block Dates Management</strong><br>
                            {{ $appointmentsFeatureStatus['message']  }}
                           
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                                <strong>⚠ Block Dates Management</strong><br>
                           
                            {{ $appointmentsFeatureStatus['message'] }}
                        </div>
                    @endif
                @endif
                @if($canAddBlockDates)
              
                <div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
                    <div class="CDSDashboardContainer-main-content">
                        <div class="CDSDashboardContainer-main-content-inner">  
                             <div class="CDSDashboardContainer-main-content-inner-header">
                                <ul class="status-tabs">
                                    <li class="">
                                        <a class="tab-link" href="{{ baseUrl('appointments/appointment-booking/calendar') }}">
                                            Appointment Calendar
                                        </a>
                                    </li>
                                    <li class="cds-active">
                                        <a class="tab-link" href="{{ baseUrl('appointments/block-dates/add') }}">
                                            Block Dates
                                        </a>
                                    </li>
                                </ul>
                             </div>
                             <div class="CDSDashboardContainer-main-content-inner-body">
                                @include("admin-panel.03-appointments.appointment-system.block-dates.block-date-calendar")
                             </div>
                        </div>
                    </div>
                </div>
                @else
                    <div class="text-center text-muted">
                        <p>You don't have permission to add block dates.</p>
                    </div>
                @endif
@endsection

