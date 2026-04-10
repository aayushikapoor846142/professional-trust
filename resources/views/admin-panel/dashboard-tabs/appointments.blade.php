<!-- appointment dashboard -->
     <div class="cdsTYDashboard-integrated-container-component" data-container-id="1">
        <div class="cdsTYDashboard-integrated-container-header">
            <div class="cdsTYDashboard-integrated-header-left">
                <h1 class="cdsTYDashboard-integrated-container-title">Appointment Overview</h1>
            </div>
            <div class="cdsTYDashboard-integrated-header-controls">
                <button class="cdsTYDashboard-integrated-sidebar-toggle" aria-label="Toggle Sidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
                <button class="cdsTYDashboard-integrated-minimize-btn" aria-label="Minimize Container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 15l7-7 7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
                    <section id="overview" class="cdsTYDashboard-integrated-section-header">
                        <!-- <h2>Appointment Overview</h2> -->
                        <p>Welcome to your minimalist dashboard. You can collapse the sidebar using the arrow button for more screen space, or minimize entire containers using the chevron button in the header.</p>
                        
                        <!-- Summary Cards -->
                        <div class="cdsTYDashboard-main-summary-cards">
                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('cases')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Total Appointment</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countAppointment('all') }}">{{ countAppointment('all') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(94, 114, 228, 0.1); color: #5e72e4;">
                                        📁
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                        <span>↑</span>
                                        <span>12%</span>
                                    </div>
                                    <span style="color: #6b7280;">vs last month</span>
                                </div>--}}
                            </div>

                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('appointments')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Upcoming Appointment</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countAppointment('approved') }}">{{ countAppointment('approved') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                        📅
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(countCase('open') > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ countCase('open') ?? [] }}</span>
                                        </div>
                                        <span style="color: #6b7280;">upcoming</span>
                                    @else
                                        <span style="color: #6b7280;">No upcoming active cases</span>
                                    @endif
                                </div>--}}
                            </div>

                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('messages')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Completed Appointment</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countAppointment('completed') }}">{{ countAppointment('completed') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(251, 146, 60, 0.1); color: #fb923c;">
                                        💌
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($unreadMessages ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $unreadMessages ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">unread</span>
                                    @else
                                        <span style="color: #6b7280;">All caught up!</span>
                                    @endif
                                </div>--}}
                            </div>

                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Cancelled Appointment</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countAppointment('cancelled') }}">{{ countAppointment('cancelled') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        💳
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($pendingInvoices ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $pendingInvoices ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">pending</span>
                                    @else
                                        <span style="color: #6b7280;">All invoices cleared</span>
                                    @endif
                                </div>--}}
                            </div>
                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Awaiting Appointment</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countAppointment('awaiting') }}">{{ countAppointment('awaiting') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        💳
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($pendingInvoices ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $pendingInvoices ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">pending</span>
                                    @else
                                        <span style="color: #6b7280;">All invoices cleared</span>
                                    @endif
                                </div>--}}
                            </div>
                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Draft Appointment</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countAppointment('draft') }}">{{ countAppointment('draft') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        💳
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($pendingInvoices ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $pendingInvoices ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">pending</span>
                                    @else
                                        <span style="color: #6b7280;">All invoices cleared</span>
                                    @endif
                                </div>--}}
                            </div>
                        </div>

                    </section>
                    <!-- appointment recent -->
                    
                    <section id="active" class="cdsTYDashboard-integrated-section">
                        <div class="CdsDashboardAppoinment-compact-list-container">
                            <div class="CdsDashboardAppoinment-compact-list-header">
                            
                                <div class="CdsDashboardAppoinment-compact-list-header-item CdsDashboardAppoinment-compact-list-client">Client</div>
                            
                                <div class="CdsDashboardAppoinment-compact-list-header-item CdsDashboardAppoinment-compact-list-service">Service</div>
                                <div class="CdsDashboardAppoinment-compact-list-header-item CdsDashboardAppoinment-compact-list-datetime">Date & Time</div>
                                <div class="CdsDashboardAppoinment-compact-list-header-item">Status</div>
                                <div class="CdsDashboardAppoinment-compact-list-header-item">Payment</div>
                                <div class="CdsDashboardAppoinment-compact-list-header-item">Actions</div>
                            </div>

                            <div class="CdsDashboardAppoinment-compact-list-appointment-list">
                                @forelse($upcomingAppointments ?? [] as $appointment)
                                <div class="CdsDashboardAppoinment-compact-list-appointment-item">
                                    
                                    <div class="CdsDashboardAppoinment-compact-list-client-cell">

                                        @if(optional($appointment->client)->profile_image)
                                            <img src="{{ userDirUrl(optional($appointment->client)->profile_image, 't') }}" class="rounded-circle me-2 CdsDashboardAppoinment-compact-list-avatar" alt="{{ optional($appointment->client)->first_name }}">
                                        @else
                                            <div class="CdsDashboardAppoinment-compact-list-avatar CdsDashboardAppoinment-compact-list-purple"> {{ strtoupper(substr(optional($appointment->client)->first_name,0,1)) }}</div>
                                        
                                        @endif
                                        <div class="CdsDashboardAppoinment-compact-list-client-info">
                                        <div class="CdsDashboardAppoinment-compact-list-patient-id">ID: #{{$appointment->unique_id}}</div>
                                            <div class="CdsDashboardAppoinment-compact-list-client-name">{{ optional($appointment->client)->first_name }} {{ optional($appointment->client)->last_name }}</div>
                                            <div class="CdsDashboardAppoinment-compact-list-client-specialty">{{ optional($appointment->service)->name }}</div>
                                        </div>
                                    </div>
                                
                                    <div class="CdsDashboardAppoinment-compact-list-service-cell">
                                        {{ optional($appointment->service)->name }}
                                    </div>
                                    <div class="CdsDashboardAppoinment-compact-list-datetime-cell">
                                        <div class="CdsDashboardAppoinment-compact-list-datetime-date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</div>
                                        <div class="CdsDashboardAppoinment-compact-list-datetime-duration">{{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}</div>
                                    </div>
                                    <div class="CdsDashboardAppoinment-compact-list-status-cell">
                                        <span class="CdsDashboardAppoinment-compact-list-status-badge CdsDashboardAppoinment-compact-list-{{$appointment->status}}">
                                            <span class="CdsDashboardAppoinment-compact-list-status-dot"></span>
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>
                                    <div class="CdsDashboardAppoinment-compact-list-payment-cell">
                                        <div class="CdsDashboardAppoinment-compact-list-payment-amount">{{currencySymbol($appointment->currency)}} {{ $appointment->price}}</div>
                                        <div class="CdsDashboardAppoinment-compact-list-payment-status CdsDashboardAppoinment-compact-list-paid">
                                            <span class="CdsDashboardAppoinment-compact-list-check-icon">✓</span>  {{ ucfirst($appointment->payment_status) }}
                                        </div>
                                    </div>
                                    <div class="CdsDashboardAppoinment-compact-list-actions-cell">
                                        <a href="{{ baseUrl('appointments/appointment-booking/view/'.$appointment->unique_id) }}" class="CdsDashboardAppoinment-compact-list-action-btn CdsDashboardAppoinment-compact-list-view">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </a>
                                        {{--<button class="CdsDashboardAppoinment-compact-list-action-btn CdsDashboardAppoinment-compact-list-edit">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                            </svg>
                                        </button>
                                        <button class="CdsDashboardAppoinment-compact-list-action-btn CdsDashboardAppoinment-compact-list-notify">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                            </svg>
                                        </button>--}}
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
                    </section>
                    <!-- end appointment recent -->