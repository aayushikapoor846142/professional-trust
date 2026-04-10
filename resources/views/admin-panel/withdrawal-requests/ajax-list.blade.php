@if($withdrawalRequests->count() > 0)
    @foreach($withdrawalRequests as $request)
        <div class="cdsTYDashboard-table-row">
            <div class="cdsTYDashboard-table-cell" data-label="Request ID">
                <strong>#{{ $request->unique_id }}</strong>
            </div>
            
            <div class="cdsTYDashboard-table-cell" data-label="Amount">
                <strong>{{ $request->formatted_amount }}</strong>
            </div>
            
            <div class="cdsTYDashboard-table-cell" data-label="Banking Details">
                <div>
                    <strong>{{ $request->bankingDetail->bank_name }}</strong><br>
                    <small class="text-muted">****{{ substr($request->bankingDetail->account_number, -4) }}</small>
                </div>
            </div>
            
            <div class="cdsTYDashboard-table-cell" data-label="Status">
                {!! $request->status_badge !!}
            </div>
            
            <div class="cdsTYDashboard-table-cell" data-label="Request Date">
                {{ $request->request_date->format('M d, Y H:i') }}
            </div>
            
            <div class="cdsTYDashboard-table-cell" data-label="File">
                @if($request->file_upload)
                    <a href="{{ url('/panel/withdrawal-requests/' . $request->unique_id . '/download') }}" 
                       class="btn btn-sm btn-outline-primary" title="Download File">
                        <i class="fas fa-download"></i>
                    </a>
                @else
                    <span class="text-muted">No file</span>
                @endif
            </div>
            
            <div class="cdsTYDashboard-table-cell" data-label="Actions">
                <div class="btn-group" role="group">
                    <a href="{{ url('/panel/withdrawal-requests/' . $request->unique_id) }}" 
                       class="btn btn-sm btn-outline-info" title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if($request->status === 'pending')
                    <button type="button" class="btn btn-sm btn-outline-warning ms-1" title="Send Reminder"
                        onclick="sendReminder('{{ $request->unique_id }}')">
                        <i class="fas fa-bell"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="text-center text-muted py-5">
        <i class="fas fa-money-bill-transfer fa-4x mb-4 text-muted"></i>
        <h4>No Withdrawal Requests</h4>
        <p class="mb-4">You haven't created any withdrawal requests yet.</p>
        @if($user->activeBankingDetail)
            <a href="{{ url('/panel/withdrawal-requests/create') }}" class="CdsTYButton-btn-primary btn-lg">
                <i class="fas fa-plus"></i> Create Your First Withdrawal Request
            </a>
        @else
            <a href="{{ url('/panel/profile/banking-details') }}" class="CdsTYButton-btn-primary btn-lg">
                <i class="fas fa-university"></i> Add Banking Details First
            </a>
        @endif
    </div>
@endif 