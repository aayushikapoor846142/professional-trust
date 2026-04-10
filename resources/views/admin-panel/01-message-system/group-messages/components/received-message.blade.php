<div class="group-messages-message received" data-message-id="{{ $group_message->unique_id }}"
    id="message-{{ $group_message->unique_id }}">
    <div class="group-messages-message-avatar">
        @if($group_message->sentBy->profile_image != '')
        <img src="{{ $group_message->sentBy->profile_image ? userDirUrl($group_message->sentBy->profile_image, 't') : 'assets/images/default.jpg' }}"
            alt="{{ $group_message->sentBy->first_name }}" class="group-messages-message-avatar-image" />
        @else
        <div class="group-messages-message-avatar-image" data-initial="{{ userInitial($group_message->sentBy) }}">
            <span>{{ userInitial($group_message->sentBy) }}</span>
        </div>
        @endif
    </div>

    <div class="group-messages-message-content">
        <div class="group-messages-message-header">
            <span
                class="group-messages-message-sender">{{ $group_message->sentBy->first_name . " " . $group_message->sentBy->last_name }}</span>
            <span class="group-messages-message-time">
                @php
                $timezone = getUserTimezone();
                $checkTimezone = isValidTimezone($timezone);
                @endphp
                @if($checkTimezone)
                {{ $group_message->created_at->timezone($timezone)->format('H:i') }}
                @else
                {{ date('H:i', strtotime($group_message->created_at)) }}
                @endif
            </span>
        </div>

        <div
            class="group-messages-message-bubble {{ $group_message->checkMemberStatus($group_message->group_id, $group_message->user_id) }}">
            <!-- Reply to message -->
            @if($group_message->reply_to != NULL && !empty($group_message->replyTo))
            <div class="group-messages-reply-to"
                onclick="scrollToGrpMessage(this,'{{ $group_message->group_id }}','{{ $group_message->replyTo->unique_id }}')">
                @if($group_message->replyTo->message != NULL)
                <span class="group-messages-reply-username">{!!
                    makeBoldBetweenAsterisks($group_message->replyTo->message) !!}</span>
                @endif
                @if($group_message->replyTo->attachment != NULL)
                <div class="group-messages-reply-attachment">
                    <div class="group-messages-attachment-icon">
                        <i class="fa-solid fa-paperclip"></i>
                    </div>
                    <div class="group-messages-attachment-info">
                        <span class="group-messages-attachment-name">Attachment</span>
                    </div>
                </div>
                @endif
                <p class="group-messages-reply-text">Reply quoted message</p>
            </div>
            @endif

            <!-- Message content -->
            <div class="group-messages-message-text">
                @if($group_message->edited_at)
                <span class="group-messages-edited-indicator"
                    id="editedMsg{{ $group_message->unique_id }}">edited</span>
                @endif

                @if($group_message->deleted_at == NULL)
                <p class="group-messages-chat-message" id="cpMsg{{ $group_message->unique_id }}">
                    {!! makeBoldBetweenAsterisks($group_message->message) !!}
                </p>
                @else
                <p class="group-messages-deleted-message">This message was deleted.</p>
                @endif
            </div>

            <!-- Attachments -->
            @if($group_message->attachment && $group_message->deleted_at == NULL)
            <div
                class="group-messages-message-attachment {{ (count(explode(',', $group_message->attachment)) > 1) ? 'files-uploaded' : '' }}">
                @include('admin-panel.01-message-system.group-messages.components.attachments-common', [
                'get_attachments' => $group_message->attachment,
                'chat_msg_id' => $group_message->id,
                'chat_msg_Unique_id' => $group_message->unique_id
                ])
            </div>
            @endif

            <!-- Message reactions -->
            @if($group_message->messageReactions && $group_message->messageReactions->count() > 0)
            <div class="group-messages-message-reactions">
             

                <!-- Reaction tooltip -->
                <div class="group-messages-reactions-tooltip" id="reactionsList{{ $group_message->unique_id }}">
                    <span class="group-messages-reactions-icon">
                        <i class="fa-regular fa-face-smile-plus"></i>
                        <span
                            class="group-messages-reactions-count">{{ $group_message->messageReactions->count() }}</span>
                    </span>
                    <div class="group-messages-reactions-tooltip-content">
                        <div class="group-messages-reactions-users">
                            @foreach($group_message->messageReactions as $msgReaction)
                            <div class="group-messages-reaction-user">
                                <div class="group-messages-reaction-user-avatar"
                                    data-initial="{{ userInitial($msgReaction->userAdded) }}">
                                    {{ userInitial($msgReaction->userAdded) }}
                                </div>
                                <span class="group-messages-reaction-user-name">
                                    @if($msgReaction->added_by == auth()->user()->id)
                                    (You)
                                    @else
                                    {{ $msgReaction->userAdded ? $msgReaction->userAdded->first_name : '' }}
                                    @endif
                                </span>
                                <div class="group-messages-reaction-emoji-display">
                                    {{ $msgReaction->reaction }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Message actions -->
            @if($group_message->deleted_at == NULL)
            <div class="group-messages-message-actions">
                <div class="group-messages-message-options">
                    <button class="group-messages-options-btn" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>
                    <ul class="group-messages-dropdown-menu">
                        @if($group_message->message != NULL)
                        <li>
                            <a class="group-messages-dropdown-item" href="javascript:;"
                                onclick="groupCopyMessage('cpMsg{{ $group_message->unique_id }}')">
                                Copy <i class="fa fa-copy"></i>
                            </a>
                        </li>
                        @endif
                        <li>
                            <a class="group-messages-dropdown-item replyTo" href="javascript:;"
                                onclick="groupReplyTo(this,'{{ $group_message->group_id }}','{{ $group_message->id }}')">
                                Reply <i class="fa fa-reply"></i>
                            </a>
                        </li>
                        <li>
                            <a class="group-messages-dropdown-item" href="javascript:;"
                                data-message-id="{{ $group_message->unique_id }}" onclick="deleteGroupMessage(this)">
                                Delete for me <i class="fa fa-trash"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Emoji reaction area -->
                <div class="group-messages-emoji-reaction-area">
                    <div class="group-messages-reaction-input">
                        <div class="group-messages-emoji-icon message-reaction"
                            data-message-id="{{ $group_message->unique_id }}"
                            id="emojiBtn-{{ $group_message->unique_id }}">
                            <i class="fa-regular fa-face-smile"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
