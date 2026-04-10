@extends('admin-panel.layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/24-CDS-ticket-system-view.css') }}">
@endsection

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
<!-- Header Section -->
    <div class="CdsTicket-header">
        <div class="CdsTicket-header-top">
            <div class="CdsTicket-ticket-title">
                <h1 class="CdsTicket-ticket-id mb-0">#{{ $ticket->ticket_number }}</h1>
                <span class="CdsTicket-status-badge CdsTicket-status-{{ $ticket->status_text ? strtolower(str_replace(' ', '-', $ticket->status_text)) : 'open' }}">{{ $ticket->status_text }}</span>
                <span class="CdsTicket-priority-badge CdsTicket-priority-{{ $ticket->priority_text ? strtolower($ticket->priority_text) : 'normal' }}">{{ $ticket->priority_text }} Priority</span>
            </div>
            <div class="CdsTicket-header-actions">
                <button class="CdsTicket-btn CdsTicket-btn-secondary" type="button" onclick="printTicket()">
                    <i class="fa fa-print"></i> Print
                </button>
                <button class="CdsTicket-btn CdsTicket-btn-secondary" type="button" onclick="exportTicket()">
                    <i class="fa fa-download"></i> Export
                </button>
                <button class="CdsTicket-btn CdsTicket-btn-secondary text-danger" type="button" onclick="deleteTicket('{{ $ticket->unique_id }}')">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>
        </div>
        <div class="CdsTicket-info-grid">
            <div class="CdsTicket-info-item">
                <span class="CdsTicket-info-label">Subject</span>
                <span class="CdsTicket-info-value">{{ $ticket->subject }}</span>
            </div>
            <div class="CdsTicket-info-item">
                <span class="CdsTicket-info-label">Created</span>
                <span class="CdsTicket-info-value">{{ $ticket->created_at->format('M d, Y g:i A') }}</span>
            </div>
            <div class="CdsTicket-info-item">
                <span class="CdsTicket-info-label">Category</span>
                <span class="CdsTicket-info-value">
                    @if($ticket->category)
                        <span class="badge" style="background-color: {{ $ticket->category->color }}; color: white;">
                            {{ $ticket->category->name }}
                        </span>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </span>
            </div>
            <div class="CdsTicket-info-item">
                <span class="CdsTicket-info-label">Assigned To</span>
                <span class="CdsTicket-info-value">
                    {{ $ticket->assignedTo->name ?? 'Unassigned' }}
                </span>
            </div>
        </div>
    </div>


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <!-- Main Content Area -->
    <div class="CdsTicket-main-content">
        <!-- Conversation Section -->
        <div class="CdsTicket-conversation">
            <div class="CdsTicket-conversation-header">
                <h2 class="CdsTicket-conversation-title">Conversation</h2>
            </div>
            <div id="replies-container">
                @foreach($ticket->replies as $reply)
                    <div class="CdsTicket-message">
                        <div class="CdsTicket-message-header">
                            <div class="CdsTicket-avatar" style="background: linear-gradient(135deg, {{ $reply->reply_type === 'admin' ? 'var(--CdsTicket-primary), var(--CdsTicket-accent)' : 'var(--CdsTicket-success), #059669' }});">
                                {{ strtoupper(substr($reply->user->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="CdsTicket-message-info">
                                <div class="CdsTicket-message-author">{{ $reply->user->name ?? 'System' }}</div>
                                <div class="CdsTicket-message-time">{{ $reply->created_at->format('M d, Y g:i A') }}</div>
                                @if($reply->is_internal)
                                    <span class="badge bg-secondary ms-2">Internal</span>
                                @endif
                            </div>
                        </div>
                        <div class="CdsTicket-message-body">
                            {!! $reply->formatted_message !!}
                            @if($reply->attachments->count() > 0)
                                <div class="CdsTicket-attachments mt-2">
                                    @foreach($reply->attachments as $attachment)
                                        <a href="{{ $attachment->download_url }}" class="CdsTicket-attachment" target="_blank">
                                            <i class="fa {{ $attachment->file_icon }}"></i> {{ $attachment->original_name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Add Reply Form -->
            <div class="CdsTicket-reply-box">
                <form id="replyForm">
                    @csrf
                    <textarea class="CdsTicket-reply-textarea" id="replyMessage" name="message" rows="4" placeholder="Type your reply..."></textarea>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            {!! FormHelper::formSelect([
                                'name' => 'update_status',
                                'label' => 'Status',
                                'id' => 'updateStatus',
                                'options' => FormHelper::ticketCommentStatus(),
                                'value_column' => 'value',
                                'label_column' => 'label',
                                'is_multiple' => false,
                                'required' => true,
                            ]) !!}

                                
                            {{--<select class="CdsTicket-filter" id="updateStatus" name="update_status">
                                <option value="">No Status Change</option>
                                <option value="open">Mark as Open</option>
                                <option value="in_progress">Mark as In Progress</option>
                                <option value="waiting_for_customer">Mark as Waiting for Customer</option>
                                <option value="resolved">Mark as Resolved</option>
                                <option value="closed">Mark as Closed</option>
                            </select>--}}
                        </div>
                        <div class="col-md-12">  
                            <button type="button" class="CdsTicket-btn CdsTicket-btn-secondary" onclick="document.getElementById('attachments').click()">
                                📎 Attach Files
                            </button>
                            <input type="file" class="form-control" id="attachments" name="attachments[]" multiple style="display: none;" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                            <div class="form-text">You can upload multiple files. Maximum size: 10MB per file.</div>
                            <div id="selected-files" class="mt-2" style="display: none;">
                                <h6>Selected Files:</h6>
                                <div id="file-list"></div>
                            </div>
                        </div>
                    </div>
                    <div class="CdsTicket-reply-actions mt-3">
                        <button type="submit" class="CdsTicket-btn CdsTicket-btn-primary">
                            <i class="fa fa-paper-plane"></i> Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Sidebar -->
        <div class="CdsTicket-sidebar">
            <!-- Contact Information -->
            <div class="CdsTicket-sidebar-card">
                <h3 class="CdsTicket-sidebar-title">User Information</h3>
                <div class="CdsTicket-contact-info">
                    <div class="CdsTicket-contact-item">
                        <span class="CdsTicket-contact-icon"><i class="fa fa-user"></i></span>
                        <span>{{ $ticket->user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="CdsTicket-contact-item">
                        <span class="CdsTicket-contact-icon"><i class="fa fa-envelope"></i></span>
                        <span>{{ $ticket->user->email ?? 'N/A' }}</span>
                    </div>
                    <div class="CdsTicket-contact-item">
                        <span class="CdsTicket-contact-icon"><i class="fa fa-phone"></i></span>
                        <span>{{ $ticket->user->phone_no ?? 'N/A' }}</span>
                    </div>
                    <div class="CdsTicket-contact-item">
                        <span class="CdsTicket-contact-icon"><i class="fa fa-user-tag"></i></span>
                        <span>{{ ucfirst($ticket->user->role ?? 'N/A') }}</span>
                    </div>
                    <div class="CdsTicket-contact-item">
                        <span class="CdsTicket-contact-icon"><i class="fa fa-calendar"></i></span>
                        <span>{{ $ticket->user->created_at->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <!-- Statistics -->
            <div class="CdsTicket-sidebar-card">
                <h3 class="CdsTicket-sidebar-title">Statistics</h3>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="stat-item">
                            <div class="stat-number">{{ $ticket->replies->count() }}</div>
                            <div class="stat-label">Replies</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-item">
                            <div class="stat-number">{{ $ticket->attachments->count() }}</div>
                            <div class="stat-label">Attachments</div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="stat-item">
                            <div class="stat-number">{{ $ticket->response_time ? round($ticket->response_time / 60, 1) : 'N/A' }}</div>
                            <div class="stat-label">Response Time (hrs)</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-item">
                            <div class="stat-number">{{ $ticket->resolution_time ? round($ticket->resolution_time / 60, 1) : 'N/A' }}</div>
                            <div class="stat-label">Resolution Time (hrs)</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Activity History -->
            <div class="CdsTicket-sidebar-card">
                <h3 class="CdsTicket-sidebar-title">Activity History</h3>
                <div class="CdsTicket-activity-list">
                    @foreach($ticket->histories->take(10) as $history)
                        <div class="CdsTicket-activity-item">
                            <div class="CdsTicket-activity-icon"><i class="fa {{ $history->action_icon }}"></i></div>
                            <div class="CdsTicket-activity-content">
                                <div class="CdsTicket-activity-text">{{ $history->action_text }}</div>
                                <div class="CdsTicket-activity-time">{{ $history->time_ago }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

			</div>
	
	</div>
  </div>
</div>


@endsection

@section('javascript')
<script src="{{ asset('assets/plugins/chatapp/ticket-user-socket.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize websocket for ticket replies
    if (typeof currentUserId !== 'undefined') {
        // Initialize ticket user socket
        // The ticket-user-socket.js will handle the websocket connection
    }
    
    // Handle reply form submission
    $('#replyForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Debug: Log form data
        console.log('Form submission - Files:', $('#attachments')[0].files);
        console.log('Form submission - Message:', $('#replyMessage').val());
        
        $.ajax({
            url: '{{ baseUrl("tickets/reply/" . $ticket->unique_id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i> Sending...'
                );
            },
            success: function(response) {
                console.log('Success response:', response);
                if (response.status) {
                    // Clear the form instead of reloading
                    $('#replyMessage').val('');
                    $('#attachments').val('');
                    $('#updateStatus').val('');
                    $('#selected-files').hide();
                    $('#file-list').empty();
                    
                    // The websocket will handle adding the new reply to the container
                    successMessage('Reply sent successfully');
                } else {
                    errorMessage('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error response:', xhr.responseText);
                console.log('Error status:', status);
                console.log('Error:', error);
                errorMessage('Error sending reply');
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html(
                    '<i class="fa fa-paper-plane"></i> Send Reply'
                );
            }
        });
    });

    // Auto-resize textarea
    const textarea = document.getElementById('replyMessage');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 300) + 'px';
        });
    }

    // Show selected files
    $('#attachments').on('change', function() {
        const files = this.files;
        const fileList = $('#file-list');
        const selectedFiles = $('#selected-files');
        
        if (files.length > 0) {
            fileList.empty();
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
                const fileItem = `
                    <div class="selected-file-item" style="display: flex; justify-content: space-between; align-items: center; padding: 5px 10px; background: #f8f9fa; border-radius: 4px; margin-bottom: 5px;">
                        <span><i class="fa fa-file"></i> ${file.name} (${fileSize} MB)</span>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${i})" style="margin-left: 10px;">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                `;
                fileList.append(fileItem);
            }
            selectedFiles.show();
        } else {
            selectedFiles.hide();
        }
    });
});

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
                location.reload();
            } else {
                errorMessage('Error: ' + response.message);
            }
        },
        error: function() {
            errorMessage('Error updating status');
        }
    });
}

function assignTicket() {
    const assignedTo = $('#assignTo').val();
    $.ajax({
        url: '{{ baseUrl("tickets/assign/" . $ticket->unique_id) }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            assigned_to: assignedTo
        },
        success: function(response) {
            if (response.status) {
                location.reload();
            } else {
                errorMessage('Error: ' + response.message);
            }
        },
        error: function() {
            errorMessage('Error assigning ticket');
        }
    });
}

function deleteTicket(ticketId) {
    if (confirm('Are you sure you want to delete this ticket? This action cannot be undone.')) {
        window.location.href = '{{ baseUrl("tickets/delete") }}/' + ticketId;
    }
}

function printTicket() {
    window.print();
}

function exportTicket() {
    alert('Export functionality to be implemented');
}

function removeFile(index) {
    const input = document.getElementById('attachments');
    const dt = new DataTransfer();
    const { files } = input;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    input.files = dt.files;
    $('#attachments').trigger('change'); // Trigger change event to update display
}
</script>
@endsection 