@php
    $discussionSidebarData = $discussionData ?? [];
@endphp

@if(!empty($discussionSidebarData))
    @foreach($discussionSidebarData as $discussion)
        @if($discussion->user)
            @php
                $isCreator = $discussion->added_by == auth()->id();
                $member = $discussion->member->where('member_id', auth()->id())->first();
                $isJoinRequestAllowed = $discussion->allow_join_request == 1;
                $isJoinRequestPending = $isJoinRequestAllowed && $member && $member->status == 'pending';
                $isActiveMember = $isJoinRequestAllowed && $member && $member->status == 'active';
                $isPrivatePost = $discussion->type == 'private';
                $canViewContent = $isCreator || !$isPrivatePost || $isActiveMember;
                $disableComment = ($isJoinRequestPending || $isPrivatePost) && $discussion->added_by != auth()->id() && !$isActiveMember;
            @endphp

            <!-- Thread Card -->
            <article class="CdsDiscussionThread-glass-card"> <!-- Title -->
              

                <div class="CdsDiscussionThread-card-top">
                    

                    <div class="CdsDiscussionThread-card-info">
                       
                        <div class="CdsDiscussionThread-author-information"><!-- Avatar -->
                    <div class="CdsDiscussionThread-avatar-glow">
                        @if($discussion->user->profile_image)
                            <img src="{{ userDirUrl($discussion->user->profile_image, 't') }}" 
                                 alt="{{ $discussion->user->first_name }}"
                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        @else
                            {{ userInitial($discussion->user) }}
                        @endif
                    </div>
					 <div class="CdsDiscussionThread-card-author">
                                {{ Str::limit($discussion->user->first_name . ' ' . $discussion->user->last_name, 120) }}
                            </div>
					
					
					
					</div>
                        <div class="CdsDiscussionThread-card-header">
                            <span class="CdsDiscussionThread-posted-date">{{ $discussion->created_at->diffForHumans() }}</span>
                            
                            <!-- Dropdown Menu for Creator -->
                            @if($isCreator || checkPrivilege([
                                'route_prefix' => 'panel.discussion-threads',
                                'module' => 'professional-discussion-threads',
                                'action' => 'delete'
                            ]))
                                <div class="CdsDiscussionThread-dropdown">
                                    <button class="CdsDiscussionThread-dropdown-trigger">
                                        <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" stroke="currentColor" />
                                        </svg>
                                    </button>
                                    <div class="CdsDiscussionThread-dropdown-menu">
                                        @if($isCreator && checkPrivilege([
                                            'route_prefix' => 'panel.discussion-threads',
                                            'module' => 'professional-discussion-threads',
                                            'action' => 'edit'
                                        ]))
                                            <div class="CdsDiscussionThread-dropdown-item"
                                                 onclick="openCustomPopup(this)" data-href="{{ baseUrl('manage-discussion-threads/edit/'.$discussion->unique_id.'/modal') }}">
                                                <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                                                    <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke="currentColor" />
                                                </svg>
                                                <span>Edit</span>
                                            </div>
                                            <div class="CdsDiscussionThread-dropdown-divider"></div>
                                            @endif
                                         <div class="CdsDiscussionThread-dropdown-item"
                                                 onclick="window.location.href='{{ baseUrl('manage-discussion-threads/'.$discussion->unique_id.'/detail') }}'">
                                                <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                                                    <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke="currentColor" />
                                                </svg>
                                                <span>View</span>
                                            </div>
                                            <div class="CdsDiscussionThread-dropdown-divider"></div>

                                        @if($isCreator && checkPrivilege([
                                            'route_prefix' => 'panel.discussion-threads',
                                            'module' => 'professional-discussion-threads',
                                            'action' => 'delete'
                                            ]))
                                            <div class="CdsDiscussionThread-dropdown-item danger">
                                                <a href="javascript:;" data-href="{{ baseUrl('manage-discussion-threads/delete/'.$discussion->unique_id) }}"
                                                onclick="confirmDiscussionAction(this)">
                                                    <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke="currentColor" />
                                                    </svg>
                                                    <span>Delete</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div> <div class="CdsDiscussionThread-card-meta">
                            <span class="CdsDiscussionThread-badge-glow">
                                {{ $discussion->category->name ?? 'General' }}
                            </span>
                           
                            @if($isPrivatePost)
                                <span class="CdsDiscussionThread-badge-private">Private</span>
                            @endif
                        </div>
                </div>
<div class="CdsDiscussionThread-content-preview-section"  >
                 <h3 class="CdsDiscussionThread-card-title" onclick="window.location.href='{{ baseUrl('manage-discussion-threads/'.$discussion->unique_id.'/detail') }}'">
                    {!! html_entity_decode($discussion->topic_title) !!}
                </h3>
                <!-- Content Preview -->
                @if($canViewContent)
                    <div  
                       class="CdsDiscussionThread-card-content-link">
                        <p class="CdsDiscussionThread-card-desc">
                            {!! $discussion->short_description !!}
                        </p>
                        
                        @if($discussion->files)
                            <div class="CdsDiscussionThread-images-preview">
                                @foreach(array_slice(explode(',', $discussion->files), 0, 3) as $index => $file)
                                    <img src="{{ $file ? discussionDirUrl($file, 's') : asset('assets/images/default.jpg') }}" 
                                         alt="Attachment {{ $index + 1 }}"
                                         class="CdsDiscussionThread-preview-image" onclick="cdsDiscussionMainOpenPreview(this)" data-href="{{ baseUrl('manage-discussion-threads/view-media/'.$discussion->unique_id.'/'.$file) }}">
                                @endforeach
                                @if(count(explode(',', $discussion->files)) > 3)
                                    <span class="CdsDiscussionThread-more-images">
                                        +{{ count(explode(',', $discussion->files)) - 3 }} more
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="CdsDiscussionThread-card-desc CdsDiscussionThread-private-preview">
                        <p>{!! Str::limit($discussion->short_description, 100) !!}</p>
                        
                        @if(!$member)
                            @if(checkPrivilege([
                                'route_prefix' => 'panel.discussion-threads',
                                'module' => 'professional-discussion-threads',
                                'action' => 'join-discussion'
                            ]))
                                <button onclick="confirmAnyAction(this)" 
                                        data-action="Join This Discussion"
                                        data-href="{{ baseUrl('discussion-threads/join-discussion/'.$discussion->unique_id) }}" 
                                        type="button"
                                        class="CdsDiscussionThread-join-btn">
                                    <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 4v16m8-8H4" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Join this Discussion
                                </button>
                            @endif
                        @elseif($isJoinRequestPending)
                            @if(checkPrivilege([
                                'route_prefix' => 'panel.discussion-threads',
                                'module' => 'professional-discussion-threads',
                                'action' => 'withdraw-discussion'
                            ]))
                                <button onclick="confirmAnyAction(this)" 
                                        data-action="Withdraw Join Request"
                                        data-href="{{ baseUrl('discussion-threads/withdraw-discussion-request/'.$discussion->unique_id) }}" 
                                        type="button"
                                        class="CdsDiscussionThread-withdraw-btn">
                                    <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                                        <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Withdraw Join Request
                                </button>
                                <span class="CdsDiscussionThread-pending-badge">Join request pending</span>
                            @endif
                        @endif
                    </div>
                @endif
 </div>
                <!-- Actions -->
                <div class="CdsDiscussionThread-card-actions">  <div class="CdsDiscussionThread-card-actions-panel">
                    <!-- Reply/Comments -->
                    <div class="CdsDiscussionThread-action-item {{ $disableComment ? 'disabled' : '' }}"
                         @if(!$disableComment)
                             onclick="window.location.href='{{ baseUrl('manage-discussion-threads/'.$discussion->unique_id.'/detail') }}'"
                             style="cursor: pointer;"
                         @endif>
                        <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                            <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" />
                        </svg>
                        <span>{{ $discussion->comments->count() }} {{ Str::plural('Comment', $discussion->comments->count()) }}</span>
                    </div>

                    <!-- Favorite -->
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.discussion-threads',
                        'module' => 'professional-discussion-threads',
                        'action' => 'favourite'
                    ]))
                        <div class="CdsDiscussionThread-action-item"
                             onclick="window.location.href='{{ baseUrl('manage-discussion-threads/favourite/'.$discussion->unique_id) }}'">
                            <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="{{ $discussion->is_favourite ? 'currentColor' : 'none' }}">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" stroke="currentColor" />
                            </svg>
                            <span>{{ $discussion->is_favourite ? 'Saved' : 'Save' }}</span>
                        </div>
                    @endif
					</div>
<div class="CdsDiscussionThread-view-section">
                                            
                                            
											<a class="CdsTYButton-btn-primary"
                                                 onclick="window.location.href='{{ baseUrl('manage-discussion-threads/'.$discussion->unique_id.'/detail') }}'">
                                              
                                               View
                                            </a>
											
											
											</div>
                </div>
            </article>
        @endif
    @endforeach
@else
    <div class="CdsDiscussionThread-empty-state">
        <svg class="CdsDiscussionThread-empty-icon" viewBox="0 0 24 24" fill="none">
            <path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" stroke="currentColor" stroke-width="2"/>
        </svg>
        <h5>No Discussion Available</h5>
    </div>
@endif

 <div class="CdsCaseDocumentPreview-overlay" id="cdsDiscussionPreviewOverlay"></div>


