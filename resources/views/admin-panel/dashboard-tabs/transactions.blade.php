     <div class="cdsTYDashboard-integrated-container-component" data-container-id="1">
        <div class="cdsTYDashboard-integrated-container-header">
            <div class="cdsTYDashboard-integrated-header-left">
                <h1 class="cdsTYDashboard-integrated-container-title">Transaction Overview</h1>
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
                        <!-- <h2>Transaction Overview</h2> -->
                        <p>Welcome to your minimalist dashboard. You can collapse the sidebar using the arrow button for more screen space, or minimize entire containers using the chevron button in the header.</p>
                        
                        <!-- Summary Cards -->
                        <div class="cdsTYDashboard-main-summary-cards">
                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('cases')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Total Revenue</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ $totalRevenue ?? 0 }}">{{ $totalRevenue ?? 0 }}</div>
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
                                        <div class="cdsTYDashboard-main-card-label">Total Transactions</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ $totalTransactions ?? 0 }}">{{ $totalTransactions ?? 0 }}</div>
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
                                        <div class="cdsTYDashboard-main-card-label">Active Subscriptions</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ $activeSubscriptions ?? 0 }}">{{ $activeSubscriptions ?? 0 }}</div>
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
                                        <div class="cdsTYDashboard-main-card-label">Pending Transactions</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ $pendingTransactions ?? 0 }}">{{ $pendingTransactions ?? 0 }}</div>
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
                  <div class="row">
        <!-- Revenue Chart -->
        <div class="col-xl-8 mb-4">
            <div class="cds-ty-dashboard-box p-3 bg-white rounded-3">
                <div class="cds-ty-dashboard-box-header">
                    <h4 class="cds-ty-dashboard-box-title">
                        <i class="fa-solid fa-chart-line me-2"></i>
                        Revenue Overview
                    </h4>
                    <div class="cds-ty-dashboard-box-actions">
                        <select class="form-select form-select-sm" id="revenuePeriod">
                            <option value="7">Last 7 Days</option>
                            <option value="30" selected>Last 30 Days</option>
                            <option value="90">Last 90 Days</option>
                            <option value="365">Last Year</option>
                        </select>
                    </div>
                </div>
                <div class="cds-ty-dashboard-box-body">
                    <div class="CdsTransactionOverview-custom-chart-container">
                        <canvas id="revenueOverviewChart" width="800" height="350"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Types Distribution -->
        <div class="col-xl-4 mb-4">
            <div class="cds-ty-dashboard-box p-3 bg-white rounded-3">
                <div class="cds-ty-dashboard-box-header">
                    <h4 class="cds-ty-dashboard-box-title">
                        <i class="fa-solid fa-pie-chart me-2"></i>
                        Transaction Types
                    </h4>
                </div>
                <div class="cds-ty-dashboard-box-body">
                    <div class="CdsTransactionOverview-custom-chart-container">
                        <canvas id="transactionTypesChart" width="400" height="350"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="row">
        <!-- Recent Transactions -->
        <div class="col-xl-12 mb-4">
            <div class="cds-ty-dashboard-box p-3 bg-white rounded-3">
                <div class="cds-ty-dashboard-box-header d-flex flex-wrap align-items-start gap-2 justify-content-between">
                    <h4 class="cds-ty-dashboard-box-title mb-0">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>
                        Recent Transactions
                    </h4>
                    <div class="cds-ty-dashboard-box-actions">
                        <a href="{{ baseUrl('transactions/history') }}" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="cds-ty-dashboard-box-body">
                    <div class="CdsTransactionOverview-recent-transactions-list" id="recentTransactionsList">
                        @if($recentTransactions->count() > 0)
                            @foreach($recentTransactions as $transaction)
                                <div class="CdsTransactionOverview-recent-transaction-item">
                                    <div class="CdsTransactionOverview-transaction-avatar">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <div class="CdsTransactionOverview-transaction-details">
                                        <h6>{{ $transaction->payment_type ?? 'Payment' }}</h6>
                                        <p>{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    <div class="CdsTransactionOverview-transaction-amount">
                                        <div class="amount">${{ number_format($transaction->total_amount ?? 0, 2) }}</div>
                                        <div class="status CdsTransactionOverview-status-{{ $transaction->invoice->payment_status ?? 'pending' }}">
                                            {{ ucfirst($transaction->invoice->payment_status ?? 'pending') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fa-solid fa-inbox fa-2x text-muted"></i>
                                <p class="mt-2 text-muted">No recent transactions found</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
       <div class="row">
        <div class="col-12 mb-4">
            <div class="cds-ty-dashboard-box p-3 bg-white rounded-3">
                <div class="cds-ty-dashboard-box-header">
                    <h4 class="cds-ty-dashboard-box-title">
                        <i class="fa-solid fa-list-check me-2"></i>
                        Transaction Summary by Status
                    </h4>
                </div>
                <div class="cds-ty-dashboard-box-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="CdsTransactionOverview-status-summary-card CdsTransactionOverview-status-success">
                                <div class="CdsTransactionOverview-status-summary-icon">
                                    <i class="fa-solid fa-check-circle"></i>
                                </div>
                                <div class="CdsTransactionOverview-status-summary-content">
                                    <h5>{{ $completedTransactions ?? 0 }}</h5>
                                    <p>Completed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="CdsTransactionOverview-status-summary-card CdsTransactionOverview-status-pending">
                                <div class="CdsTransactionOverview-status-summary-icon">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <div class="CdsTransactionOverview-status-summary-content">
                                    <h5>{{ $pendingTransactions ?? 0 }}</h5>
                                    <p>Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="CdsTransactionOverview-status-summary-card CdsTransactionOverview-status-failed">
                                <div class="CdsTransactionOverview-status-summary-icon">
                                    <i class="fa-solid fa-times-circle"></i>
                                </div>
                                <div class="CdsTransactionOverview-status-summary-content">
                                    <h5>{{ $failedTransactions ?? 0 }}</h5>
                                    <p>Failed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="CdsTransactionOverview-status-summary-card CdsTransactionOverview-status-refunded">
                                <div class="CdsTransactionOverview-status-summary-icon">
                                    <i class="fa-solid fa-undo"></i>
                                </div>
                                <div class="CdsTransactionOverview-status-summary-content">
                                    <h5>{{ $refundedTransactions ?? 0 }}</h5>
                                    <p>Refunded</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
