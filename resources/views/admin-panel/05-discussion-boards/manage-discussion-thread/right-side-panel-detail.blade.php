   <!-- Sidebar -->
        
                @include("admin-panel.05-discussion-boards.manage-discussion-thread.right-side-panel")
                <!-- Categories -->
                <!-- Members -->
                @if($discussion->added_by == auth()->user()->id)
                    <div class="CdsDiscussionThread-members-section">
                        <h3 class="CdsDiscussionThread-members-title">Members</h3>
                        @if($members->isNotEmpty())
                            @foreach($members as $member)
                                <div class="CdsDiscussionThread-member-item">
                                    <div class="CdsDiscussionThread-member-avatar">{!! getProfileImage($member->unique_id) !!}</div>
                                    <span class="CdsDiscussionThread-member-name">{{ $member->first_name ??'' }} {{ $member->last_name ??'' }}</span>
                                    <a href="javascript:;" data-href="{{baseUrl('manage-discussion-threads/delete-member/'.$member->unique_id.'/'.$discussion->unique_id)}}"
                                    onclick="confirmDiscussionAction(this)" class="CdsDiscussionThread-delete-btn">Delete</a>
                                </div>
                            @endforeach
                        @else
                            <div class="CdsDiscussionThread-member-item">
                                <span class="text-danger">N/A</span>
                            </div>

                        @endif
                    </div>
                    <!-- Join Request -->
                    <div class="CdsDiscussionThread-members-section">
                        <h3 class="CdsDiscussionThread-members-title">Pending Join Request</h3>
                        @if($pendingMembers->isNotEmpty())
                            @foreach($pendingMembers as $member)
                                <div class="CdsDiscussionThread-member-item">
                                    <div class="CdsDiscussionThread-member-avatar">{!! getProfileImage($member->unique_id) !!}</div>
                                    <span class="CdsDiscussionThread-member-name">{{ $member->first_name ??'' }} {{ $member->last_name ??'' }}</span>

                                    <a href="javascript:;" data-href="{{baseUrl('manage-discussion-threads/accept-member/'.$member->unique_id.'/'.$discussion->unique_id)}}"
                                    data-action="Accept Join Request" onclick="confirmAnyAction(this)" class="CdsDiscussionThread-delete-btn">Accept</a>

                                    <a href="javascript:;" data-href="{{baseUrl('manage-discussion-threads/delete-member/'.$member->unique_id.'/'.$discussion->unique_id)}}"
                                    onclick="confirmDiscussionAction(this)" class="CdsDiscussionThread-delete-btn">Delete</a>
                                </div>
                            @endforeach
                        @else
                            <div class="CdsDiscussionThread-member-item">
                                <span class="text-danger">N/A</span>
                            </div>
                        @endif
                        <!-- Join request items would go here -->
                    </div>
                @endif

  