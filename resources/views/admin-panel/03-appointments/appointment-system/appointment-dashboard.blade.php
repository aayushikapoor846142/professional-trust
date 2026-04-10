@extends('admin-panel.layouts.app')

@section('page-submenu')
{!! pageSubMenu('appointment-system') !!}
@endsection

@section('styles')
<link href="{{ url('assets/css/11-CDS-appointment-overview.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
<div class="CdsAppointmentOverview-header">
        <h1 class="mb-2 mb-md-0">{{ $pageTitle }}</h1>
        <div class="CdsAppointmentOverview-header-actions">
            <a href="{{ baseUrl('appointments/appointment-booking/save-booking') }}" class="CdsAppointmentOverview-btn CdsAppointmentOverview-btn-primary">
                <span>+</span> New Appointment
            </a>
            <a href="{{ baseUrl('appointments/appointment-booking/calendar') }}" class="CdsAppointmentOverview-btn CdsAppointmentOverview-btn-secondary">
                <span>📅</span> View Calendar
            </a>
        </div>
    </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">

    <div class="CdsAppointmentOverview-stats-grid">
        @php
            $statuses = [
                'all' => ['label' => 'Total', 'icon' => '📊'],
                'approved' => ['label' => 'Upcoming', 'icon' => '📅'],
                'completed' => ['label' => 'Completed', 'icon' => '✅'],
                'cancelled' => ['label' => 'Cancelled', 'icon' => '❌'],
                'awaiting' => ['label' => 'Awaiting', 'icon' => '⏳'],
                'draft' => ['label' => 'Draft', 'icon' => '📝'],
            ];
        @endphp
        @foreach($statuses as $key => $info)
        <div class="CdsAppointmentOverview-stat-card">
            <div class="CdsAppointmentOverview-stat-icon">{{ $info['icon'] }}</div>
            <div class="CdsAppointmentOverview-stat-value">{{ $appointmentsCount[$key] ?? 0 }}</div>
            <div class="CdsAppointmentOverview-stat-label">{{ $info['label'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="CdsAppointmentOverview-appointments-section">
        <div class="CdsAppointmentOverview-section-header">
            <h2 class="CdsAppointmentOverview-section-title">Upcoming Appointments</h2>
            <a href="{{ baseUrl('appointments/appointment-booking') }}" class="CdsAppointmentOverview-btn CdsAppointmentOverview-btn-secondary" style="font-size: 12px; padding: 8px 16px;">
                View All →
            </a>
        </div>
        <!-- div table -->
        <div class="cdsTYDashboard-table">
            <div class="cdsTYDashboard-table-wrapper">
                <div class="cdsTYDashboard-table-header">
                    <div class="cdsTYDashboard-table-cell">Client</div>
                    <div class="cdsTYDashboard-table-cell">Service</div>
                    <div class="cdsTYDashboard-table-cell">Date & Time</div>
                    <div class="cdsTYDashboard-table-cell">Status</div>
                    <div class="cdsTYDashboard-table-cell">Actions</div>
                </div>
                <div class="cdsTYDashboard-table-body">
                    @forelse($upcomingAppointments ?? [] as $appointment)
                        <div class="cdsTYDashboard-table-row">
                            <div class="cdsTYDashboard-table-cell" data-label="Client">
                                <div class="d-flex align-items-center">
                                    <div class="CDSSupportPayment-avatar">
                                        @if(optional($appointment->client)->profile_image)
                                            <img src="{{ userDirUrl(optional($appointment->client)->profile_image, 't') }}" class="rounded-circle me-2" width="36" height="36" alt="{{ optional($appointment->client)->first_name }}">
                                        @else
                                            <span class="badge bg-secondary rounded-circle me-2" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;">
                                                {{ strtoupper(substr(optional($appointment->client)->first_name,0,1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="CdsSendInvitation-client-info ms-2">
                                        <div class="CDSSupportPayment-customer-name fw-semibold">{{ optional($appointment->client)->first_name }} {{ optional($appointment->client)->last_name }}</div>
                                        <div class="CDSSupportPayment-customer-email">ID: {{ $appointment->unique_id }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="cdsTYDashboard-table-cell" data-label="Service">{{ optional($appointment->service)->name }}</div>
                            <div class="cdsTYDashboard-table-cell" data-label="Date & Time">
                                <div class="d-flex gap-2">
                                    {{ $appointment->appointment_date ? dateFormat($appointment->appointment_date) : 'NA' }}
                                    <span class="text-muted small">{{ $appointment->start_time_converted }} - {{ $appointment->end_time_converted }}</span>
                                </div>
                            </div>
                            <div class="cdsTYDashboard-table-cell" data-label="Status">
                                <span class="CdsAppointmentOverview-status-badge CdsAppointmentOverview-status-{{ $appointment->status }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                            <div class="cdsTYDashboard-table-cell" data-label="Actions">
                                <a href="{{ baseUrl('appointments/appointment-booking/view/'.$appointment->unique_id) }}" class="CdsAppointmentOverview-action-btn">👁️</a>
                            </div>
                        </div>
                    @empty
                    <div class="cdsTYDashboard-table-cell">
                        <div class="text-center">No upcoming appointments found.</div>
                    </div>
                    @endforelse
                </div>  
            </div>
        </div>
        <!-- # div table -->

        {{--<table class="CdsAppointmentOverview-appointments-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($upcomingAppointments ?? [] as $appointment)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center;">
                            @if(optional($appointment->client)->profile_image)
                                <img src="{{ userDirUrl(optional($appointment->client)->profile_image, 't') }}" class="rounded-circle me-2" width="36" height="36" alt="{{ optional($appointment->client)->first_name }}">
                            @else
                                <span class="badge bg-secondary rounded-circle me-2" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;">
                                    {{ strtoupper(substr(optional($appointment->client)->first_name,0,1)) }}
                                </span>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ optional($appointment->client)->first_name }} {{ optional($appointment->client)->last_name }}</div>
                                <div class="text-muted small">ID: {{ $appointment->unique_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="CdsAppointmentOverview-service-name">{{ optional($appointment->service)->name }}</td>
                    <td class="CdsAppointmentOverview-date-time">
                        {{ $appointment->appointment_date ? dateFormat($appointment->appointment_date) : 'NA' }}<br>
                        <span class="text-muted small">{{ $appointment->start_time_converted }} - {{ $appointment->end_time_converted }}</span>
                    </td>
                    <td>
                        <span class="CdsAppointmentOverview-status-badge CdsAppointmentOverview-status-{{ $appointment->status }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ baseUrl('appointments/appointment-booking/view/'.$appointment->unique_id) }}" class="CdsAppointmentOverview-action-btn">👁️</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No upcoming appointments found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>--}}
    </div>

    <div class="CdsAppointmentOverview-bottom-nav">
       @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-types',
                    'module' => 'professional-appointment-types',
                    'action' => 'add'
                ]))      
    <a href="{{ baseUrl('appointments/settings') }}"
   onclick="sessionStorage.setItem('activeTab', 'CDS-appointment-type-tab')"
   class="CdsAppointmentOverview-nav-item">
            <span class="CdsAppointmentOverview-nav-item-icon">📋</span>
            <span>Appointment Types</span>
        </a>
        @endif

                  @if(checkPrivilege([
                        'route_prefix' => 'panel.appointments.block-dates',
                        'module' => 'professional-appointments-block-dates',
                        'action' => 'add'
                    ]))       
                    <a href="{{ baseUrl('appointments/block-dates/add') }}" class="CdsAppointmentOverview-nav-item">
            <span class="CdsAppointmentOverview-nav-item-icon">⛔</span>
            <span>Block Dates</span>
        </a>
                @endif

                     @if(checkPrivilege([
                        'route_prefix' => 'panel.appointment-booking-flow',
                        'module' => 'professional-appointment-booking-flow',
                        'action' => 'add'
                    ]))  
        
        <a href="{{ baseUrl('appointments/appointment-booking-flow') }}" class="CdsAppointmentOverview-nav-item">
            <span class="CdsAppointmentOverview-nav-item-icon">🔄</span>
            <span>Booking Flow</span>
        </a>
          @endif
    </div>

			</div>
	
	</div>
  </div>
</div>

@endsection
