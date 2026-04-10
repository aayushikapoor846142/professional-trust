@if(count($records) > 0) 
@if(isset($reviewGiven) && $reviewGiven != null)
<script>
    $(document).ready(function() {
        toggleInlineReview({{ $reviewGiven }});
    });
</script>
@endif
@foreach($records as $key => $record)

<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            {!! FormHelper::formCheckbox([ 'value' => $record->unique_id, 'data-id' => $record->unique_id, 'checkbox_class' => 'row-checkbox custom-control-input case-checkbox' ]) !!}
        </div>
    </div>
    @if(auth()->user()->role!="client")
    <div class="cdsTYDashboard-table-cell" data-label="Professional Details">
        <div class=" d-block d-md-flex">
            <div class="CdsSendInvitation-client-avatar">
                {{ strtoupper(substr( $record->user->email, 0, 1)) }}
            </div>
            <div class="CdsSendInvitation-client-info ms-2">
                <div class="CdsSendInvitation-client-email">{{ $record->user->email ?? '' }}</div>
                <div class="CdsSendInvitation-client-meta">Invitation #{{ $record->id }}</div>
            </div>
        </div>
    </div>
    @endif
    <div class="cdsTYDashboard-table-cell" data-label="Status">
        @if($record->status == 'pending')
            <span class="CdsSendInvitation-status CdsSendInvitation-status-warning">
                <span class="CdsSendInvitation-status-dot"></span>
                Pending Approval
            </span>
            @elseif($record->status == 'review_given')
            <span class="CdsSendInvitation-status CdsSendInvitation-status-success">
                <span class="CdsSendInvitation-status-dot"></span>
                Review Given
            </span>
            @else
            <span class="CdsSendInvitation-status">
                {{ ucfirst(str_replace('_', ' ', $record->status)) }}
            </span>
        @endif
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Sent Date">
        <div class="d-flex gap-2">
            <div class="CdsSendInvitation-date">{{ \Carbon\Carbon::parse($record->created_at)->format('M d, Y') }}</div>
            <div class="CdsSendInvitation-time">{{ \Carbon\Carbon::parse($record->created_at)->format('h:i A') }}</div>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Response">
        @if(isset($record->rating))
            <div class="CdsSendInvitation-rating">
                @for($i = 1; $i <= 5; $i++)
                    <span class="CdsSendInvitation-star {{ $i <= $record->rating ? 'CdsSendInvitation-star-filled' : '' }}">★</span>
                @endfor
            </div>
        @else
            <span style="color: #6c757d;">-</span>
        @endif
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Actions">
        <div class="d-flex gap-2">
            @if(checkPrivilege([ 'route_prefix' => 'panel.review-received', 'module' => 'professional-review-received', 'action' => 'view' ]))
            <button class="CdsSendInvitation-show-review-btn" onclick="toggleInlineReview({{ $record->id }})">
                Show Review
                <svg class="CdsSendInvitation-review-toggle-icon" id="review-icon-desktop-{{ $record->id }}" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
            @endif            
            <div class="btn-group">
                <button class="CdsCustomForm-action-btn dropdown-toggle"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="true"
                        aria-expanded="false"
                        style="border: none; background: transparent;">
                    <i class="fa-regular fa-ellipsis-vertical"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                
                    
                            <li>
                                <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('reviews/review-received/report-spam-review/' . $record->unique_id) ?>')" >
                                    <svg class="CdsSendInvitation-dropdown-item-icon" fill="currentColor" viewBox="0 0 20 20" width="20" height="20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Report Spam Review
                                </a>
                            </li>
                    
                  
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.review-received',
                        'module' => 'professional-review-received',
                        'action' => 'delete'
                    ]))
                        <li>
                            <a class="dropdown-item text-danger d-flex gap-1"
                            onclick="confirmAction(this)"
                            data-href="{{ baseUrl('reviews/review-received/delete/' . $record->unique_id) }}">
                                <svg class="CdsSendInvitation-dropdown-item-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Delete
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

        </div>
    </div>
</div>

<div class="CdsSendInvitation-table-row">

    <!-- Desktop Inline Review Row -->
    <div class="CdsSendInvitation-desktop-review-row" id="desktop-review-{{ $record->id }}" style="display: none;">
        <div class="CdsSendInvitation-inline-review-desktop">
            <div class="CdsSendInvitation-inline-review-content">
                <div class="CdsSendInvitation-review-section">
                    <div class="CdsSendInvitation-review-label">Review Given</div>
                    <div class="CdsSendInvitation-review-text">
                        {!! $record->review ?? 'No Review provided.' !!}
                    </div>
                </div>
                <div class="CdsSendInvitation-review-meta">
                    <span class="CdsSendInvitation-review-meta-item">
                        <i class="fa-solid fa-calendar"></i>
                        Reviewed on {{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role == "professional") @php $check_reply = \App\Models\ReviewReplies::where('review_id', $record->id) ->where('professional_id', auth()->user()->id) ->first(); @endphp

    <div class="CdsSendInvitation-desktop-review-row" id="desktop-review-reply-{{ $record->id }}" style="display: none;">
        <div class="CdsSendInvitation-inline-review-desktop">
            <div class="CdsSendInvitation-inline-review-content">
                <div class="CdsSendInvitation-review-section">
                    <div class="CdsSendInvitation-review-label">
                        Reply By : {{ $record->cdsCompanyDetail->company_name ?? '' }}
                    </div>

                    <div class="CdsSendInvitation-review-text">
                        @if($check_reply) {{-- Show existing reply --}}
                        <p class="fw-semibold mb-2">{{ $check_reply->reply }}</p>

                        {{-- Action Buttons --}}
                        <div class="mt-2">
                            @if($check_reply->edited!=1)
                            <button class="btn btnReply" type="button" onclick="showEditForm({{ $check_reply->id }})"><i class="fa-light fa-reply fa-rotate-180 fa-sm"></i> Edit Reply</button>
                            @endif
                            <a class="btn btnRemove ms-md-2" href="{{ baseUrl('reviews/review-received/delete-review-reply/' . $check_reply->unique_id) }}"> <i class="fa-light fa-trash fa-sm"></i> Delete Reply </a>
                        </div>

                        {{-- Edit Form --}}
                        <form style="display: none;" id="editForm{{ $check_reply->id }}" action="{{ baseUrl('reviews/review-received/update-review-reply/' . $check_reply->unique_id) }}" method="post">
                            @csrf
                            <div class="client-comment row mt-2">
                                <div class="col-sm-8">
                                    <input type="text" name="reply" value="{{ $check_reply->reply }}" required class="form-control" placeholder="Write a Reply.." oninput="validateHtmlTags(this)" />
                                </div>
                                <div class="col-sm-4 text-end">
                                    <button type="submit" class="CdsTYButton-btn-primary">Update reply</button>
                                </div>
                            </div>
                        </form>
                        @else {{-- No reply yet - show reply form --}} @if(checkPrivilege([ 'route_prefix' => 'panel.review-received', 'module' => 'professional-review-received', 'action' => 'reply' ]))
                        <form action="{{ baseUrl('reviews/review-received/send-review-reply/' . $record->unique_id) }}" method="post">
                            @csrf
                            <div class="client-comment row mt-2">
                                <div class="col-sm-8">
                                    <input type="text" name="reply" required class="form-control" placeholder="Write a Reply.." oninput="validateHtmlTags(this)" />
                                </div>
                                <div class="col-sm-4 text-end">
                                    <button type="submit" class="CdsTYButton-btn-primary">Post reply</button>
                                </div>
                            </div>
                        </form>
                        @endif @endif
                    </div>
                </div>

                @if(isset($check_reply->created_at))
                <div class="CdsSendInvitation-review-meta mt-2">
                    <span class="CdsSendInvitation-review-meta-item">
                        <i class="fa-solid fa-calendar"></i>
                        Reply on {{ \Carbon\Carbon::parse($check_reply->created_at)->format('M d, Y') }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
    @endforeach
@else
<div class="text-center text-muted py-5">
    <svg width="48" height="48" fill="currentColor" viewBox="0 0 20 20" style="opacity: 0.3; margin-bottom: 1rem;">
        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
    </svg>
    <p style="font-size: 1.125rem;">No Review has been received yet</p>
</div>
@endif
