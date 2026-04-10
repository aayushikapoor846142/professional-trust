@if($requests->isNotEmpty())
    @foreach($requests as $value)
    <div class="CdsCaseRequest-case-header">
        <div class="CdsCaseRequest-case-header-content">
            <div class="CdsCaseRequest-case-header-top">
                <div>
                    <h1 class="CdsCaseRequest-case-title">{{$value->cases->case_title}}</h1>
                    <div class="CdsCaseRequest-case-id">{{$value->title}}</div>
                </div>
                <div class="CdsCaseRequest-case-actions">
                    <a href=" {{baseUrl('case-with-professionals/view-request/'.$value->unique_id)}}" class="CdsCaseRequest-action-btn CdsCaseRequest-btn-view" title="View Details">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span>View</span>
                    </a>
                    <a  onclick="markAsComplete(this)" data-href="{{ baseUrl('case-with-professionals/mark-as-complete-request/'.$value->unique_id) }}" class="CdsCaseRequest-action-btn CdsCaseRequest-btn-edit" title="Edit Case">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span>Mark as complete</span>
                    </a>
                    <a  href="{{ baseUrl('case-with-professionals/edit-request/'.$value->unique_id) }}" class="CdsCaseRequest-action-btn CdsCaseRequest-btn-edit" title="Edit Case">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span>Edit</span>
                    </a>
                    <a onclick="confirmAction(this)" data-href="{{ baseUrl('case-with-professionals/delete-request/'.$value->unique_id) }}" class="CdsCaseRequest-action-btn CdsCaseRequest-btn-delete" title="Delete Case">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                        <span>Delete</span>
                    </a>
                </div>
            </div>
            <div class="CdsCaseRequest-case-meta">
                <div class="CdsCaseRequest-meta-item">
                    <svg class="CdsCaseRequest-meta-icon" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Requested on {{date('d M Y',strtotime($value->created_at))}}</span>
                </div>
                <div class="CdsCaseRequest-status-badge">
                    <span class="CdsCaseRequest-status-indicator"></span>
                    <span>{{ucwords(str_replace('-', ' ', $value->status))}}</span>
                </div>
                <!-- <div class="CdsCaseRequest-user-avatar">LB</div> -->
                    {!! getProfileImage(auth()->user()->unique_id) !!}
            </div>
        </div>
    </div>
        @endforeach
    @else
    <h5 class="text-danger">No Request Added</h5>
    @endif