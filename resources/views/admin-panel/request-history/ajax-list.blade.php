@php
    $formatDate = function($dt) {
        return $dt ? \Carbon\Carbon::parse($dt)->format('d M Y, h:i A') : '-';
    };
@endphp

@forelse($requests as $req)
    <div class="cdsTYDashboard-table-row">
        <div class="cdsTYDashboard-table-cell">
            <a href="{{ baseUrl('/withdrawal-requests/'.$req->unique_id) }}" class="text-decoration-none">#{{ $req->unique_id }}</a>
        </div>
        <div class="cdsTYDashboard-table-cell">${{ number_format($req->amount, 2) }}</div>
        <div class="cdsTYDashboard-table-cell">{{ $formatDate($req->request_date) }}</div>
        <div class="cdsTYDashboard-table-cell">{{ $formatDate($req->processed_date) }}</div>
        <div class="cdsTYDashboard-table-cell">
            @if($req->status === 'completed')
                <span class="badge bg-success">Completed</span>
            @elseif($req->status === 'pending')
                <span class="badge bg-warning text-dark">Pending</span>
            @else
                <span class="badge bg-secondary">{{ ucfirst($req->status) }}</span>
            @endif
        </div>
        <div class="cdsTYDashboard-table-cell">
            <a class="btn btn-sm btn-outline-primary" href="{{ baseUrl('/withdrawal-requests/'.$req->unique_id) }}">View</a>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-4">No records found.</div>
@endforelse

@if($requests->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="small text-muted">
            Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} entries
        </div>
        <div>
            {{ $requests->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
@endif

