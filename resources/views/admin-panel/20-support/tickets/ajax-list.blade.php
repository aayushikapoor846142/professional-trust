
@if($records->count() > 0)
    @foreach($records as $ticket)
        <div class="CdsTicket-ticket-card" data-status="{{ strtolower($ticket->status_text) }}">
            <div class="CdsTicket-card-header">
                <div class="CdsTicket-header-top">
                    <div class="CdsTicket-ticket-meta">
                        <div class="CdsTicket-checkbox-wrapper">
                            <input type="checkbox" class="CdsTicket-ticket-checkbox" value="{{ $ticket->unique_id }}">
                        </div>
                        <div class="CdsTicket-ticket-id">
                            <span class="CdsTicket-ticket-icon">
                                <i class="fa fa-ticket"></i>
                            </span>
                            {{ $ticket->ticket_number }}
                        </div>
                    </div>
                    <span class="CdsTicket-status-badge CdsTicket-status-{{ strtolower(str_replace(' ', '-', $ticket->status_text)) }}">{{ $ticket->status_text }}</span>
                </div>
                <span class="CdsTicket-ticket-subject">
                  
                    @if($ticket->is_urgent)
                        <span class="badge bg-danger ms-1"><i class="fa fa-exclamation-triangle"></i></span>
                    @endif
                    @if($ticket->is_overdue)
                        <span class="badge bg-warning ms-1"><i class="fa fa-clock"></i></span>
                    @endif
                </span>
              
            </div>
            <div class="CdsTicket-card-body">
                <div class="CdsTicket-info-item">
                    <span class="CdsTicket-info-label"><i class="fa fa-layer-group"></i> Category</span>
                    <span class="CdsTicket-info-value">
                        @if($ticket->category)
                            {{ $ticket->category->name }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </span>
                </div>
                <div class="CdsTicket-info-item">
                    <span class="CdsTicket-info-label"><i class="fa fa-flag"></i> Priority</span>
                    <span class="CdsTicket-priority-badge CdsTicket-priority-{{ $ticket->priority_text ? strtolower($ticket->priority_text) : 'normal' }}">{{ $ticket->priority_text }}</span>
                </div>
                <div class="CdsTicket-info-item">
                    <span class="CdsTicket-info-label"><i class="fa fa-user"></i> Assigned To</span>
                    <span class="CdsTicket-info-value">
                        @if($ticket->assignedTo)
                            {{ $ticket->assignedTo->name }}
                        @else
                            <span class="text-muted">Unassigned</span>
                        @endif
                    </span>
                </div>
                <div class="CdsTicket-info-item">
                    <span class="CdsTicket-info-label"><i class="fa fa-calendar"></i> Created</span>
                    <span class="CdsTicket-info-value">{{ $ticket->created_at->format('M d, Y') }}</span>
                </div>
            </div>
            <div class="CdsTicket-card-footer">
                <div class="CdsTicket-date-info">
                    <i class="fa fa-clock"></i>
                    @if($ticket->lastReply)
                        Last reply: {{ $ticket->lastReply->created_at->diffForHumans() }}
                    @else
                        Updated: {{ $ticket->updated_at->diffForHumans() }}
                    @endif
                </div>
                <div class="CdsTicket-action-buttons">
                    <a href="{{ baseUrl('tickets/'. $ticket->unique_id) }}" class="CdsTicket-btn CdsTicket-btn-primary" title="View Ticket">View Details</a>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="CdsTicket-ticket-card" style="text-align:center; padding:2rem;">
        <i class="fa fa-ticket fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No tickets found</h5>
        <p class="text-muted">Try adjusting your search criteria or filters.</p>
    </div>
@endif
<script>
function updateStatus(ticketId, status) {
    $.ajax({
        url: '{{ baseUrl("tickets/update-status") }}/' + ticketId,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: status
        },
        success: function(response) {
            if (response.status) {
                loadData(); // Reload the table
                showAlert('success', 'Ticket status updated successfully');
            } else {
                showAlert('error', response.message);
            }
        },
        error: function() {
            showAlert('error', 'Error updating ticket status');
        }
    });
}
function assignTicket(ticketId) {
    $('#assignTicketModal').modal('show');
    $('#assignTicketId').val(ticketId);
}
function updatePriority(ticketId) {
    $('#updatePriorityModal').modal('show');
    $('#updatePriorityTicketId').val(ticketId);
}
function showAlert(type, message) {
    if (type === 'success') {
        alert('Success: ' + message);
    } else {
        alert('Error: ' + message);
    }
}
</script> 