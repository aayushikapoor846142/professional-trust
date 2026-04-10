@extends('admin-panel.layouts.app')

@section('content')
@include('admin-panel.dashboard-tabs.common.dashboard-nav', ['activeTab' => 'appointments'])

<!-- Dashboard Container -->
<main class="cdsTYDashboard-main-main-content">
    <div class="CdsAppointmentOverview-container">
        <!-- Header -->
        <div class="CdsAppointmentOverview-header">
            <h1 class="mb-2 mb-md-0">Appointment Dashboard</h1>
            <div class="CdsAppointmentOverview-header-actions">
                <a href="{{ baseUrl('appointments/appointment-booking/save-booking') }}" class="CdsAppointmentOverview-btn CdsAppointmentOverview-btn-primary">
                    <span>+</span> New Appointment
                </a>
                <a href="{{ baseUrl('appointments/appointment-booking/calendar') }}" class="CdsAppointmentOverview-btn CdsAppointmentOverview-btn-secondary">
                    <span>📅</span> View Calendar
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
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

        <!-- Upcoming Appointments Section -->
        <div class="CdsAppointmentOverview-appointments-section">
            <div class="CdsAppointmentOverview-section-header">
                <h2 class="CdsAppointmentOverview-section-title">Upcoming Appointments</h2>
                <a href="{{ baseUrl('appointments/appointment-booking') }}" class="CdsAppointmentOverview-btn CdsAppointmentOverview-btn-secondary" style="font-size: 12px; padding: 8px 16px;">
                    View All →
                </a>
            </div>
            
            <!-- Appointments Table -->
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
                            </div>
                            <div class="cdsTYDashboard-table-cell" data-label="Service">{{ optional($appointment->service)->name }}</div>
                            <div class="cdsTYDashboard-table-cell" data-label="Date & Time">
                                <div class="CdsAppointmentOverview-date-time">
                                    <div class="CdsAppointmentOverview-date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</div>
                                    <div class="CdsAppointmentOverview-time">{{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}</div>
                                </div>
                            </div>
                            <div class="cdsTYDashboard-table-cell" data-label="Status">
                                <span class="CdsAppointmentOverview-status-badge CdsAppointmentOverview-status-{{ strtolower($appointment->status) }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                            <div class="cdsTYDashboard-table-cell" data-label="Actions">
                                <a href="{{ baseUrl('appointments/appointment-booking/view/'.$appointment->unique_id) }}" class="CdsAppointmentOverview-btn CdsAppointmentOverview-btn-secondary" style="font-size: 11px; padding: 4px 8px;">
                                    View
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="cdsTYDashboard-table-row">
                            <div class="cdsTYDashboard-table-cell" data-label="No appointments found" style="text-align: center; grid-column: 1 / -1;">
                                No upcoming appointments found.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/11-CDS-appointment-overview.css') }}">
@endsection

@section('javascript')
@include('admin-panel.dashboard-tabs.common.dashboard-scripts')
@endsection
