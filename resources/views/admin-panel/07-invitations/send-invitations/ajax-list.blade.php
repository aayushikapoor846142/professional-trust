@if(count($records) > 0)
    @foreach($records as $key => $record)
        <div class="cdsTYDashboard-table-row">
            <div class="cdsTYDashboard-table-cell cdsCheckbox">
                <div class="custom-checkbox">
                    {!! FormHelper::formCheckbox([
                        'value' => $record->unique_id,
                        'data-id' => $record->unique_id,
                        'checkbox_class' => 'row-checkbox custom-control-input case-checkbox'
                       
                    ]) !!}
                </div>
            </div>
            <div class="cdsTYDashboard-table-cell" data-label="Client Details">
                <div class=" d-block d-md-flex">
                    <div class="CdsSendInvitation-client-avatar">{{ strtoupper(substr($record->email, 0, 1)) }}</div>
                    <div class="CdsSendInvitation-client-info ms-2">
                        <div class="CdsSendInvitation-client-email">{{ $record->email }}</div>
                        <div class="CdsSendInvitation-client-meta">Invitation #{{ $record->id }}</div>
                    </div>
                </div>
            </div>    
            <div class="cdsTYDashboard-table-cell" data-label="Status">
                @if($record->status == 'pending')
                    <span class="CdsSendInvitation-status CdsSendInvitation-status-warning">
                        <span class="CdsSendInvitation-status-dot"></span>
                        Pending
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
                <div class="d-flex gap-2 align-items-start">
                    <div class="CdsSendInvitation-date">{{ \Carbon\Carbon::parse($record->created_at)->format('M d, Y') }}</div>
                    <div class="CdsSendInvitation-time">{{ \Carbon\Carbon::parse($record->created_at)->format('h:i A') }}</div>
                </div>
            </div>    
            <div class="cdsTYDashboard-table-cell" data-label="Response">
                @if($record->status == 'review_given' && isset($record->review->rating))
                    <div class="CdsSendInvitation-rating">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="CdsSendInvitation-star {{ $i <= $record->review->rating ? 'CdsSendInvitation-star-filled' : '' }}">★</span>
                        @endfor
                    </div>
                @else
                    <span style="color: #6c757d;">-</span>
                @endif
            </div>   
             <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}</div> 
            <div class="cdsTYDashboard-table-cell" data-label="Actions">
                @if($record->status == 'review_given')
                    <button class="CdsSendInvitation-show-review-btn" onclick="toggleInlineReview({{ $record->id }})">
                        Show Review
                        <svg class="CdsSendInvitation-review-toggle-icon" id="review-icon-desktop-{{ $record->id }}" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    @elseif(checkPrivilege([
                        'route_prefix' => 'panel.send-invitations',
                        'module' => 'professional-send-invitations',
                        'action' => 'delete'
                    ]))
                    <div class="btn-group">
                        <button class="CdsSendInvitation-action-trigger" onclick="toggleDropdown(event)">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                            </svg>
                        </button>
                        <div class="CdsSendInvitation-dropdown">
                            <ul class="CdsSendInvitation-dropdown-menu">
                                
                                @if($record->status != 'review_given')
                                <li class="CdsSendInvitation-dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('send-invitations/delete/'.$record->unique_id) }}">
                                        <svg class="CdsSendInvitation-dropdown-item-icon" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        Delete
                                    </a>
                                {{--<a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                                    data-href="{{ baseUrl('send-invitations/delete/'.$record->id) }}">
                                    <svg class="CdsSendInvitation-dropdown-item-icon" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                   <span class="text-danger">Delete</span>
                                </a>  --}}                                 
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                @else
                    <span style="color: #6c757d;">-</span>
                @endif
            </div>    
            </div>    
        </div>
            @if($record->status == 'review_given')
            <!-- Desktop Inline Review Row -->
            <div class="CdsSendInvitation-desktop-review-row" id="desktop-review-{{ $record->id }}" style="display: none;">
                <div class="CdsSendInvitation-inline-review-desktop">
                    <div class="CdsSendInvitation-inline-review-content">
                        <div class="CdsSendInvitation-review-section">
                            <div class="CdsSendInvitation-review-label">Customer Review</div>
                            <div class="CdsSendInvitation-review-text">
                                {!! $record->review->review ?? 'No Review provided.' !!}
                            
                            </div>
                        </div>
                        <div class="CdsSendInvitation-review-meta">
                            <span class="CdsSendInvitation-review-meta-item">
                                <i class="fa-solid fa-calendar"></i>
                                Reviewed on {{ dateFormat($record->review->created_at ?? '', 'd M Y, h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
    @endforeach
@else
    <div class="text-center text-muted py-5">
        <svg width="48" height="48" fill="currentColor" viewBox="0 0 20 20" style="opacity: 0.3; margin-bottom: 1rem;">
            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
        </svg>
        <p style="font-size: 1.125rem;">No invitations have been sent yet</p>
    </div>
@endif

<script type="text/javascript">
    $(document).ready(function() {
        $(".row-checkbox").change(function() {
            if ($(".row-checkbox:checked").length > 0) {
                $(".cds-action-elements").show();
            } else {
                $(".cds-action-elements").hide();
            }
            $("#datatableCounter").html($(".row-checkbox:checked").length);
        });
        
    });
    
    // Toggle inline review
    function toggleInlineReview(id) {
        const mobileReview = document.getElementById('inline-review-' + id);
        const desktopReview = document.getElementById('desktop-review-' + id);
        const mobileIcon = document.getElementById('review-icon-' + id);
        const desktopIcon = document.getElementById('review-icon-desktop-' + id);
        
        // Toggle mobile review
        if (mobileReview) {
            if (mobileReview.style.display === 'none' || mobileReview.style.display === '') {
                mobileReview.style.display = 'block';
                if (mobileIcon) mobileIcon.style.transform = 'rotate(180deg)';
            } else {
                mobileReview.style.display = 'none';
                if (mobileIcon) mobileIcon.style.transform = 'rotate(0deg)';
            }
        }
        
        // Toggle desktop review
        if (desktopReview) {
            if (desktopReview.style.display === 'none' || desktopReview.style.display === '') {
                desktopReview.style.display = 'block';
                if (desktopIcon) desktopIcon.style.transform = 'rotate(180deg)';
            } else {
                desktopReview.style.display = 'none';
                if (desktopIcon) desktopIcon.style.transform = 'rotate(0deg)';
            }
        }
    }
    
    // Toggle dropdown menu
    function toggleDropdown(event) {
        event.stopPropagation();
        const button = event.currentTarget;
        const dropdown = button.nextElementSibling;
        const isOpen = dropdown.classList.contains('show');
        
        // Close all other dropdowns
        document.querySelectorAll('.CdsSendInvitation-dropdown.show').forEach(d => {
            d.classList.remove('show');
        });
        
        // Toggle current dropdown
        if (!isOpen) {
            dropdown.classList.add('show');
        }
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.CdsSendInvitation-action-menu')) {
            document.querySelectorAll('.CdsSendInvitation-dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
    
    // Handle dropdown actions
    function handleAction(action, email, id) {
        // Close the dropdown
        document.querySelectorAll('.CdsSendInvitation-dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
        
        // Handle the action
        switch(action) {
            case 'view-stats':
                // Navigate to stats page
                window.location.href = BASEURL + '/invitations-sent/stats/' + id;
                break;
            case 'view-reviews':
                // Navigate to reviews page
                window.location.href = BASEURL + '/invitations-sent/reviews/' + id;
                break;
        }
    }
</script>