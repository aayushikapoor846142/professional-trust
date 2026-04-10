@extends('admin-panel.layouts.app')
@section('content')

 <div class="ch-action">
                    @if($activeBankingDetail)
                        <a href="{{ baseUrl('/withdrawal-requests/create') }}" class="CdsTYButton-btn-primary">
                            <i class="fa-solid fa-plus me-1"></i>
                            Create New Request
                        </a>
                    @endif
                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="refreshData()" title="Refresh Data">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
	<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
  <div class="row mb-4">
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Earnings
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($totalEarnings, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3 d-none">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Pending Earnings
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($pendingEarnings, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Withdrawals
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($completedWithdrawals, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pending Withdrawals
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($pendingWithdrawals, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
   @if(!$activeBankingDetail)
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>No Default Banking Detail Found!</strong> 
                    You must have at least one default banking detail to create withdrawal requests. 
                    <a href="{{ baseUrl('/profile/banking-details') }}" class="alert-link">Add banking details here</a>.
                </div>
            @endif

 <div class="d-flex align-items-center justify-content-between">
                        <div class="ch-head">
                            <i class="fa-table fas me-1"></i>
                            Manage Your Withdrawal Requests
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <small class="text-muted">Last updated: <span id="lastUpdated">-</span></small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                                <label class="form-check-label" for="autoRefresh">
                                    <small>Auto refresh</small>
                                </label>
                            </div>
                        </div>
                    </div><div class="cds-form-container search-area">
                        <div class="row justify-content-end">
                            <div class="col-lg-3 col-md-6 col-xl-3 col-xxl-3">
                                <select class="form-control" id="statusFilter" onchange="loadData()">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-12 col-xl-6 col-xxl-5">
                                <form id="search-form">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="datatableSearch" name="search" placeholder="Search by ID, amount, or description...">
                                        <button class="btn btn-secondary" type="submit"><i class="fa fa-search"></i></button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()" title="Clear Search">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                    
                    <!-- Loading indicator -->
                    <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading withdrawal requests...</p>
                    </div>

                    <!-- Error message -->
                    <div id="errorMessage" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="errorText"></span>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="loadData()">Retry</button>
                    </div>

                    <!-- div table -->
                    <div class="cdsTYDashboard-table" id="tableContainer">
                        <div class="cdsTYDashboard-table-wrapper">
                            <div class="cdsTYDashboard-table-header">
                                <div class="cdsTYDashboard-table-cell sorted-asc" onclick="sortTable(0)">Request ID <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" onclick="sortTable(1)">Amount <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" onclick="sortTable(2)">Banking Details <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" onclick="sortTable(3)">Status <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" onclick="sortTable(4)">Request Date <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell">File</div>
                                <div class="cdsTYDashboard-table-cell">Actions</div>
                            </div>
                            <div class="cdsTYDashboard-table-body" id="tableList">
                                <!-- AJAX content loaded here -->
                            </div>@include('components.table-pagination01')
                        </div>
                    </div> 
				
                    <!-- # div table -->
			</div>
	
	</div>
  </div>
</div>			


@endsection

@section('css')
<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.text-xs {
    font-size: 0.7rem;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
</style>
@endsection

@section('javascript')
<script src="{{ asset('assets/js/withdrawal-requests.js') }}"></script>
<script>
function sendReminder(id){
    if(!confirm('Send reminder to admin for this pending request?')) return;
    $.ajax({
        url: BASEURL + '/withdrawal-requests/' + id + '/remind',
        method: 'POST',
        data: { _token: csrf_token },
        beforeSend: showLoader,
        success: function(res){
            hideLoader();
            if(res.status){
                successMessage(res.message || 'Reminder sent.');
            }else{
                errorMessage(res.message || 'Could not send reminder.');
            }
        },
        error: function(xhr){
            hideLoader();
            errorMessage((xhr.responseJSON && xhr.responseJSON.message) || 'Error sending reminder.');
        }
    })
}
</script>
@endsection 