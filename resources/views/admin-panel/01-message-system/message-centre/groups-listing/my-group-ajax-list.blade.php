{{-- Main content --}}
    @if($groupdata->isNotEmpty())
      

             @php
                $sortedGroups = $groupdata->sortByDesc(function ($grp) {
                    return max(strtotime($grp->created_at), strtotime($grp->last_message_date ?? '0000-00-00 00:00:00'));
                });
                $bannerIndex = 1;
            @endphp
            
            @foreach($sortedGroups as $grp)
                @php
                    // Cycle through banner styles
                    $bannerClass = 'CdsDashboardGroups-list-view-banner-' . $bannerIndex;
                    $bannerIndex = $bannerIndex >= 6 ? 1 : $bannerIndex + 1;
                    
                    // Get group initial
                    $initial = strtoupper(substr($grp->name, 0, 1));
                    
                    // Check if group is new (created within last 7 days)
                    $isNew = strtotime($grp->created_at) > strtotime('-7 days');
                @endphp
                
                <div class="CdsDashboardGroups-list-view-group-card other-group-tab" 
                     data-group-id="{{ $grp->id }}" 
                     data-unique-id="{{ $grp->unique_id }}">
                    
                    <div class="CdsDashboardGroups-list-view-card-banner {{ $bannerClass }}">
                        @if($grp->group_image)
                            <img src="{{ groupChatDirUrl($grp->group_image, 't') }}" 
                                 alt="{{ $grp->name }}" 
                                 style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                        @else
                            {{ $initial }}
                        @endif
                        
                        <div class="CdsDashboardGroups-list-view-status-badges">
                            {{-- Member count badge --}}
                            @if(isset($grp->members_count))
                                <div class="CdsDashboardGroups-list-view-status-badge CdsDashboardGroups-list-view-badge-members">
                                    {{ $grp->members_count }} members
                                </div>
                            @endif
                            
                            {{-- Private badge --}}
                            @if($grp->type == 'Private')
                                <div class="CdsDashboardGroups-list-view-status-badge CdsDashboardGroups-list-view-badge-private">
                                    🔒 Private
                                </div>
                            @endif
                            
                            {{-- New badge --}}
                            @if($isNew)
                                <div class="CdsDashboardGroups-list-view-status-badge CdsDashboardGroups-list-view-badge-new">
                                    New
                                </div>
                            @endif
                        </div>
                        
                        {{-- Request count badge --}}
                        @if(count($grp->groupRequest) > 0)
                            <div class="CdsDashboardGroups-list-view-request-badge">
                                {{ count($grp->groupRequest) }} join requests
                            </div>
                        @endif
                    </div>
                    
                    <div class="CdsDashboardGroups-list-view-card-content">
                        <h3 class="CdsDashboardGroups-list-view-group-name">
                            {{ Str::limit($grp->name, 30) }}
                        </h3>
                        
                        @if(isset($grp->description))
                            <p class="CdsDashboardGroups-list-view-group-description">
                                {{ Str::limit($grp->description, 100) }}
                            </p>
                        @else
                            <p class="CdsDashboardGroups-list-view-group-description">
                                Join this group to connect and collaborate with other members.
                            </p>
                        @endif
                        
                        {{-- Members section --}}
                        <div class="CdsDashboardGroups-list-view-members-section">
                            <span class="CdsDashboardGroups-list-view-members-label">Members</span>
                            <div class="CdsDashboardGroups-list-view-avatars-container">
                                <div class="CdsDashboardGroups-list-view-avatar-stack">
                                    @if(isset($grp->members) && $grp->members->count() > 0)
                                        @foreach($grp->members->take(5) as $index => $member)
                                            <div class="CdsDashboardGroups-list-view-avatar {{ $grp->type == 'Private' ? 'CdsDashboardGroups-list-view-avatar-private' : 'CdsDashboardGroups-list-view-avatar-'.($index + 1) }}">
                                                {!! getProfileImage($member->unique_id) !!}

                                            </div>
                                        @endforeach
                                        
                                        @if($grp->members->count() > 5)
                                            <div class="CdsDashboardGroups-list-view-avatar CdsDashboardGroups-list-view-avatar-more">
                                                +{{ $grp->members->count() - 5 }}
                                            </div>
                                        @endif
                                    @else
                                        {{-- Default avatars if no members data --}}
                                        @for($i = 0; $i < 3; $i++)
                                            <div class="CdsDashboardGroups-list-view-avatar {{ $grp->type == 'Private' ? 'CdsDashboardGroups-list-view-avatar-private' : 'CdsDashboardGroups-list-view-avatar-'.($i + 1) }}">
                                                {{ $grp->type == 'Private' ? '👤' : '?' }}
                                            </div>
                                        @endfor
                                    @endif
                                </div>
                                
                                <div class="CdsDashboardGroups-list-view-group-type">
                                    @if($grp->type == 'Private')
                                        <svg class="CdsDashboardGroups-list-view-lock-icon" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    <span>{{ucfirst( $grp->type) }} Group</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="CdsDashboardGroups-list-view-card-actions">
                            <button 
                                class="CdsDashboardGroups-list-view-join-button other-group-info cdsBlueGradient" 
                                data-group-id="{{ $grp->id }}" 
                                data-unique-id="{{ $grp->unique_id }}" 
                                onclick="window.open('{{ baseUrl('group/chat/'.$grp->unique_id) }}', '_blank')">
                                View Group
                            </button>
                            {{--<a href="{{baseUrl('group/chat/'.$grp->unique_id)}}" target="_blank" class="CdsDashboardGroups-list-view-join-button other-group-info" 
                                    data-group-id="{{ $grp->id }}" 
                                    data-unique-id="{{ $grp->unique_id }}">
                                 View Group
                            </a>--}}
                            @if($type=="my-created-group-list")
                            <div class="dropdown">
                                <button class="CdsDashboardGroups-list-view-more-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-light fa-ellipsis"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:;" onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('group/edit-new-group/' . $grp->unique_id) ?>">Edit Group Details</a></li>
                                    <li><a class="dropdown-item" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('group/delete-group/'.$grp->unique_id) }}">Delete Group</a></li>
                                </ul>
                            </div>
                            @endif
                            <!-- <button class="CdsDashboardGroups-list-view-more-button">
                                ⋯
                            </button> -->
                        </div>
                    </div>
                </div>
            @endforeach
     @else
            <div class="CdsDashboardGroups-list-view-empty-state">
                <h5>No Groups Available</h5>
            </div>
    @endif
       
    
