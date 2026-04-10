@extends('admin-panel.layouts.app')

@section('content')
@include('admin-panel.dashboard-tabs.common.dashboard-nav', ['activeTab' => 'invoices'])

<!-- Dashboard Container -->
<main class="cdsTYDashboard-main-main-content">
    <div class="CdsTransactionOverview-container">
        <!-- Transaction Overview Dashboard -->
        <div class="container">
            <div class="row">
                <!-- Statistics Cards -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="cds-ty-dashboard-box CdsTransactionOverview-transaction-stat-card">
                        <div class="CdsTransactionOverview-transaction-stat-card-content">
                            <div class="CdsTransactionOverview-transaction-stat-icon">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div class="CdsTransactionOverview-transaction-stat-details">
                                <h3 class="transaction-stat-value">${{ number_format($totalRevenue ?? 0, 2) }}</h3>
                                <p class="CdsTransactionOverview-transaction-stat-label">Total Revenue</p>
                                <span class="CdsTransactionOverview-transaction-stat-change">
                                    <i class="fa-solid fa-arrow-up"></i> +{{ $revenueGrowth ?? 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="cds-ty-dashboard-box CdsTransactionOverview-transaction-stat-card">
                        <div class="CdsTransactionOverview-transaction-stat-card-content">
                            <div class="CdsTransactionOverview-transaction-stat-icon">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="CdsTransactionOverview-transaction-stat-details">
                                <h3 class="transaction-stat-value">{{ $totalTransactions ?? 0 }}</h3>
                                <p class="CdsTransactionOverview-transaction-stat-label">Total Transactions</p>
                                <span class="CdsTransactionOverview-transaction-stat-change">
                                    <i class="fa-solid fa-arrow-up"></i> +{{ $transactionGrowth ?? 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="cds-ty-dashboard-box CdsTransactionOverview-transaction-stat-card">
                        <div class="CdsTransactionOverview-transaction-stat-card-content">
                            <div class="CdsTransactionOverview-transaction-stat-icon">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="CdsTransactionOverview-transaction-stat-details">
                                <h3 class="transaction-stat-value">{{ $activeSubscriptions ?? 0 }}</h3>
                                <p class="CdsTransactionOverview-transaction-stat-label">Active Subscriptions</p>
                                <span class="CdsTransactionOverview-transaction-stat-change">
                                    <i class="fa-solid fa-arrow-up"></i> +{{ $subscriptionGrowth ?? 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="cds-ty-dashboard-box CdsTransactionOverview-transaction-stat-card">
                        <div class="CdsTransactionOverview-transaction-stat-card-content">
                            <div class="CdsTransactionOverview-transaction-stat-icon">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div class="CdsTransactionOverview-transaction-stat-details">
                                <h3 class="transaction-stat-value">{{ $pendingTransactions ?? 0 }}</h3>
                                <p class="CdsTransactionOverview-transaction-stat-label">Pending Transactions</p>
                                <span class="CdsTransactionOverview-transaction-stat-change">
                                    <i class="fa-solid fa-minus"></i> {{ $pendingChange ?? 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Analytics Section -->
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

            <!-- Quick Actions and Recent Transactions -->
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

            <!-- Transaction Summary by Status -->
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
        </div>
    </div>
</main>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/25-CDS-transaction-overview.css') }}">
@endsection

@section('javascript')
@include('admin-panel.dashboard-tabs.common.dashboard-scripts')
@endsection