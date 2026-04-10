@extends('admin-panel.layouts.app')
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <div class="row mb-4">
            <div class="col-xl-4 col-md-4 mb-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total }}</div>
                        </div>
                        <i class="fas fa-list-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4 mb-3 ">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pending }}</div>
                        </div>
                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4 mb-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completed }}</div>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
 <div class="cds-ty-dashboard-box-header">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="ch-head">
                        <i class="fa-table fas me-1"></i>
                        Request History
                    </div>
                </div>
            </div>
<div class="cds-form-container search-area">
                    <div class="row justify-content-end">
                        <div class="col-lg-3 col-md-6 col-xl-3 col-xxl-3">
                            <select class="form-control" id="statusFilter" onchange="loadHistory()">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
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

                <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading request history...</p>
                </div>

                <div id="errorMessage" class="alert alert-danger" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="errorText"></span>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="loadHistory()">Retry</button>
                </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="cdsTYDashboard-table" id="tableContainer">
                    <div class="cdsTYDashboard-table-wrapper">
                        <div class="cdsTYDashboard-table-header">
                            <div class="cdsTYDashboard-table-cell">Request ID</div>
                            <div class="cdsTYDashboard-table-cell">Amount</div>
                            <div class="cdsTYDashboard-table-cell">Raised Date</div>
                            <div class="cdsTYDashboard-table-cell">Completed Date</div>
                            <div class="cdsTYDashboard-table-cell">Status</div>
                            <div class="cdsTYDashboard-table-cell">Action</div>
                        </div>
                        <div class="cdsTYDashboard-table-body" id="ajaxTable">
                            @includeWhen(isset($initialRequests), 'admin-panel.request-history.ajax-list', ['requests' => $initialRequests])
                        </div>
                    </div>
                </div>
			</div>
	
	</div>
  </div>
</div>


@endsection

@section('javascript')
<script>
    let historyPage = 1;

    function loadHistory(page = 1) {
        historyPage = page;
        const status = $('#statusFilter').val();
        const search = $('#datatableSearch').val();
        $('#loadingIndicator').show();
        $('#errorMessage').hide();

        $.ajax({
            url: "{{ baseUrl('/request-history-ajax') }}",
            method: 'POST',
            data: {
                _token: csrf_token,
                status: status,
                search: search,
                page: page
            },
            success: function(res) {
                $('#loadingIndicator').hide();
                if (res.status) {
                    $('#ajaxTable').html(res.contents);
                } else {
                    $('#errorText').text(res.message || 'Failed to load data');
                    $('#errorMessage').show();
                }
            },
            error: function(xhr) {
                $('#loadingIndicator').hide();
                $('#errorText').text((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to load data');
                $('#errorMessage').show();
            }
        });
    }

    function clearSearch() {
        $('#datatableSearch').val('');
        loadHistory(1);
    }

    $(document).ready(function() {
        $('#search-form').on('submit', function(e) {
            e.preventDefault();
            loadHistory(1);
        });

        // Intercept pagination clicks in AJAX table
        $('#ajaxTable').on('click', '.pagination a', function(e) {
            e.preventDefault();
            const href = $(this).attr('href') || '';
            const match = href.match(/page=(\d+)/);
            const page = match ? parseInt(match[1], 10) : 1;
            loadHistory(page);
        });

        // Do not auto-reload; show server-rendered results first
    });
</script>
@endsection

