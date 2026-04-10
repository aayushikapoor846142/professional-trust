   <div class="cdsTYDashboard-integrated-container-component" data-container-id="1">
        <div class="cdsTYDashboard-integrated-container-header">
            <div class="cdsTYDashboard-integrated-header-left">
                <h1 class="cdsTYDashboard-integrated-container-title">Overview</h1>
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
                    <!-- <h2>Overview</h2> -->
                    <p>Welcome to your minimalist dashboard. You can collapse the sidebar using the arrow button for more screen space, or minimize entire containers using the chevron button in the header.</p>
                    
                    <!-- Summary Cards -->
                    <div class="cdsTYDashboard-main-summary-cards">
                        <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('cases')">
                            <div class="cdsTYDashboard-main-card-header">
                                <div class="cdsTYDashboard-main-card-info">
                                    <div class="cdsTYDashboard-main-card-label">Total Cases</div>
                                    <div class="cdsTYDashboard-main-card-value" data-count="{{ $totalCases ?? 0 }}">0</div>
                                </div>
                                <div class="cdsTYDashboard-main-card-icon" style="background: rgba(94, 114, 228, 0.1); color: #5e72e4;">
                                    📁
                                </div>
                            </div>
                        </div>

                        <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('appointments')">
                            <div class="cdsTYDashboard-main-card-header">
                                <div class="cdsTYDashboard-main-card-info">
                                    <div class="cdsTYDashboard-main-card-label">Appointments</div>
                                    <div class="cdsTYDashboard-main-card-value" data-count="{{ count($upcomingAppointments ?? []) }}">0</div>
                                </div>
                                <div class="cdsTYDashboard-main-card-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                    📅
                                </div>
                            </div>
                            {{--<div class="cdsTYDashboard-main-card-footer">
                                @if(count($upcomingAppointments ?? []) > 0)
                                    <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                        <span>↑</span>
                                        <span>{{ count($upcomingAppointments ?? []) }}</span>
                                    </div>
                                    <span style="color: #6b7280;">upcoming</span>
                                @else
                                    <span style="color: #6b7280;">No upcoming appointments</span>
                                @endif
                            </div>--}}
                        </div>

                        <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('messages')">
                            <div class="cdsTYDashboard-main-card-header">
                                <div class="cdsTYDashboard-main-card-info">
                                    <div class="cdsTYDashboard-main-card-label">Unread Messages</div>
                                    <div class="cdsTYDashboard-main-card-value" data-count="{{ $unreadMessages ?? 0 }}">0</div>
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
                                    <div class="cdsTYDashboard-main-card-label">Pending Invoices</div>
                                    <div class="cdsTYDashboard-main-card-value" data-count="{{ $pendingInvoices ?? 0 }}">0</div>
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

                <section id="activity" class="cdsTYDashboard-integrated-section">
                   <div class="cdsTYDashboard-integrated-section-card-header">
                        <h3 class="cdsTYDashboard-main-table-title">Recent Cases</h3>
                        <a href="{{ baseUrl('cases') }}" class="cdsTYDashboard-main-view-all">View all →</a>
                    </div>
                    <div class="cdsTYDashboard-main-table-card">
           
                        <div class="cdsTYDashboard-main-table-list">
                            @forelse($recentCases ?? [] as $case)
                                <div class="cdsTYDashboard-main-table-row" onclick="handleRowClick('case', {{ $case->unique_id }})">
                                    <div class="cdsTYDashboard-main-row-content">
                                        <div class="cdsTYDashboard-main-row-icon" style="background: #f3f4f6;">
                                            🇬🇧
                                        </div>
                                        <div class="cdsTYDashboard-main-row-info">
                                            <div class="cdsTYDashboard-main-row-title">{{ $case->title ?? 'Case Title' }}</div>
                                            <div class="cdsTYDashboard-main-row-subtitle">{{ $case->country ?? 'Country' }} • {{ $case->created_at ? $case->created_at->diffForHumans() : 'Recently' }}</div>
                                        </div>
                                    </div>
                                    <span class="cdsTYDashboard-main-row-status cdsTYDashboard-main-status-{{ strtolower($case->status ?? 'draft') }}">{{ $case->status ?? 'Draft' }}</span>
                                </div>
                            @empty
                                <div class="cdsTYDashboard-main-table-row">
                                    <div class="cdsTYDashboard-main-row-content">
                                        <div class="cdsTYDashboard-main-row-icon" style="background: #f3f4f6;">
                                            📁
                                        </div>
                                        <div class="cdsTYDashboard-main-row-info">
                                            <div class="cdsTYDashboard-main-row-title">No recent cases</div>
                                            <div class="cdsTYDashboard-main-row-subtitle">Start by creating your first case</div>
                                        </div>
                                    </div>
                                    <span class="cdsTYDashboard-main-row-status cdsTYDashboard-main-status-draft">Empty</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>

                <section id="analytics" class="cdsTYDashboard-integrated-section"> 
                    <div class="cdsTYDashboard-integrated-section-card-header"> 
                        <h3 class="cdsTYDashboard-main-table-title">Recent Invoices</h3>
                        <a href="{{ baseUrl('/invoices') }}" class="cdsTYDashboard-main-view-all">View all →</a>
                    </div>
                    <div class="cdsTYDashboard-main-table-card">
                        <div class="cdsTYDashboard-main-table-list">
                            @forelse($recentInvoices ?? [] as $invoice)
                                <div class="cdsTYDashboard-main-table-row" onclick="handleRowClick('invoice', {{ $invoice->unique_id }})">
                                    <div class="cdsTYDashboard-main-row-content">
                                        <div class="cdsTYDashboard-main-row-icon" style="background: #fef3c7;">
                                            📄
                                        </div>
                                        <div class="cdsTYDashboard-main-row-info">
                                            <div class="cdsTYDashboard-main-row-title">#{{ $invoice->invoice_number ?? $invoice->id }}</div>
                                            <div class="cdsTYDashboard-main-row-subtitle">
                                                @if($invoice->status === 'paid')
                                                    Paid
                                                @elseif($invoice->status === 'overdue')
                                                    Overdue
                                                @else
                                                    Due in {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->diffForHumans() : 'soon' }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <span class="cdsTYDashboard-main-row-amount" style="color: {{ $invoice->status === 'paid' ? '#10b981' : ($invoice->status === 'overdue' ? '#ef4444' : '#5e72e4') }};">
                                        ₹{{ number_format($invoice->amount ?? 0, 2) }}
                                    </span>
                                </div>
                            @empty
                                <div class="cdsTYDashboard-main-table-row">
                                    <div class="cdsTYDashboard-main-row-content">
                                        <div class="cdsTYDashboard-main-row-icon" style="background: #fef3c7;">
                                            📄
                                        </div>
                                        <div class="cdsTYDashboard-main-row-info">
                                            <div class="cdsTYDashboard-main-row-title">No recent invoices</div>
                                            <div class="cdsTYDashboard-main-row-subtitle">Create your first invoice</div>
                                        </div>
                                    </div>
                                    <span class="cdsTYDashboard-main-row-amount">₹0.00</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>