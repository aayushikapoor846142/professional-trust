@if($records)
@foreach($records as $key => $record)
<div class="CDSFeed-feed-card" data-feed-id="{{ $record->id }}">
    <div class="CDSFeed-feed-header">
        <div class="CDSFeed-feed-author">
            <!-- Profile Image with Initials Fallback -->
            @if ($record->user->profile_image)
                <img class="CDSFeed-feed-avatar" 
                     src="{{ userDirUrl($record->user->profile_image, 't') }}" 
                     alt="{{ $record->user->first_name }}">
            @else
                @php
                    $initial = strtoupper(substr($record->user->first_name, 0, 1));
                    $bgColor = ['667eea', 'f59e0b', '10b981', 'ef4444', '8b5cf6'][array_rand([0,1,2,3,4])];
                @endphp
                <div class="CDSFeed-feed-avatar CDSFeed-avatar-initial" 
                     style="background: #{{ $bgColor }};">
                    {{ $initial }}
                </div>
            @endif
            
            <div class="CDSFeed-feed-meta">
                <span class="CDSFeed-feed-name">{{ $record->user->first_name ?? '' }} {{ $record->user->last_name ?? '' }}</span>
                <span class="CDSFeed-feed-time">{{ $record->created_at->diffForHumans() }} @if($record->edited_at != '')
                    [Edited at: {{ date('d M, Y', strtotime($record->edited_at)) }}]
                @endif</span>
                
            </div>
        </div>
        
        <!-- Status and Actions -->
        <div class="CDSFeed-feed-header-actions">
            @if($record->status == 'published')
                <span class="CDSFeed-feed-status CDSFeed-status-published">Published</span>
            @elseif($record->status == 'draft')
                <span class="CDSFeed-feed-status CDSFeed-status-draft">Draft</span>
            @elseif($record->status == 'scheduled')
                <span class="CDSFeed-feed-status CDSFeed-status-scheduled">Scheduled  </span><br>
                <span class="CDSFeed-feed-time">[Schedule at: {{ date('d M, Y', strtotime($record->schedule_date)) }}]</span>
            @endif
            
            <!-- Follow/Unfollow Button (for other users) -->
            
        
            @if(auth()->user()->id != $record->user->id)
                @if(checkIfFollowing(auth()->user()->id, $record->user->id) > 0)
                    <button class="CdsTYButton-btn-secondary  CDSFeed-btn-follow" 
                                                    onclick="unfollow({{$record->user->id}},'following')">
                                                Unfollow
                                            </button>
                @else
                   <button class="CdsTYButton-btn-primary CDSFeed-btn-follow" 
                                                    onclick="followBack({{$record->user->id}},'following')">
                                                Follow 
                                            </button>
                @endif
            @endif
            
            <!-- Dropdown Menu -->
            <div class="CDSFeed-dropdown">
                <button class="CDSFeed-dropdown-toggle" onclick="toggleDropdown({{ $record->id }})">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <ul class="CDSFeed-dropdown-menu" id="dropdown-{{ $record->id }}">
                    @if($record->user->id == auth()->user()->id)
                        <li>
                            @if($record->is_pin == 1)
                            <a class="CDSFeed-dropdown-item CDSFeed-unpin" href="javascript:;" data-href="{{baseUrl('my-feeds/'.$record->unique_id.'/unpin-post')}}">
                                <i class="fa-regular fa-thumbtack-slash"></i> Unpin Post
                            </a>
                            @else
                            <a class="CDSFeed-dropdown-item CDSFeed-pin" href="javascript:;" data-href="{{baseUrl('my-feeds/'.$record->unique_id.'/pin-post')}}">
                                <i class="fa-regular fa-thumbtack"></i> Pin Post
                            </a>
                            @endif
                            
                        </li>
                        <li>
                            <a class="CDSFeed-dropdown-item CDSFeed-copy-feed " onclick="copyFeed(this)" data-href="{{ baseUrl('my-feeds/copy-feed/'.$record->unique_id) }}">
                                <i class="fa-regular fa-copy"></i> Copy
                            </a>
                        </li>
                        <li>
                            <a class="CDSFeed-dropdown-item"  onclick="showRightSlidePanel(this)" data-href="{{baseUrl('my-feeds/feed-setting/'.$record->unique_id)}}">
                                <i class="fa-regular fa-gear"></i> Settings
                            </a>
                        </li>
                        <li>
                            <a class="CDSFeed-dropdown-item CDSFeed-text-danger" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('my-feeds/delete/'.$record->unique_id) }}">
                                <i class="fa-regular fa-trash"></i> Delete
                            </a>
                        </li>
                    @else
                        <!-- Follow/Unfollow in dropdown -->
                        <li>
                            @if(auth()->user()->following->contains($record->user->id))
                                <a class="CDSFeed-dropdown-item CDSFeed-follow" data-href="{{ baseUrl('my-feeds/'.$record->user->id.'/follow') }}">
                                    <i class="fa-solid fa-user-minus"></i> Unfollow
                                </a>
                            @else
                                <a class="CDSFeed-dropdown-item CDSFeed-unfollow" data-href="{{ baseUrl('my-feeds/'.$record->user->id.'/unfollow') }}">
                                    <i class="fa-solid fa-user-plus"></i> Follow
                                </a>
                            @endif
                        </li>
                        
                        <!-- Remove from followers -->
                        @if(checkIfFollowing($record->user->id, auth()->user()->id) > 0)
                            <li>
                                <a class="CDSFeed-dropdown-item" 
                                   onclick="removeFromFollowers({{$record->user->id}},'followers')" 
                                   href="javascript:;">
                                    <i class="fa-regular fa-user-xmark"></i> Remove From Followers
                                </a>
                            </li>
                        @endif
                        
                        <!-- Chat/Message Options -->
                        @php
                            $chat_request = \App\Models\ChatRequest::with('chat')->where(function ($query) use ($record) {
                                $query->where('sender_id', auth()->id())
                                      ->where('receiver_id', $record->user->id);
                            })->orWhere(function ($query) use ($record) {
                                $query->where('sender_id', $record->user->id)
                                      ->where('receiver_id', auth()->id());
                            })->first();
                        @endphp
                        
                        @if(isset($chat_request) && $chat_request != NULL)
                            @if($chat_request->is_accepted == 1 && $chat_request->chat)
                                <li>
                                    <a class="CDSFeed-dropdown-item" 
                                       href="{{url('panel/message-centre/chat/'.$chat_request->chat->unique_id)}}">
                                        <i class="fa-regular fa-message"></i> Send Message
                                    </a>
                                </li>
                            @elseif($chat_request->is_accepted == 2)
                                <li class="CDSFeed-dropdown-disabled">
                                    <span class="CDSFeed-dropdown-item">Request Declined</span>
                                </li>
                            @else
                                <li class="CDSFeed-dropdown-disabled">
                                    <span class="CDSFeed-dropdown-item">Request Pending</span>
                                </li>
                            @endif
                        @else
                            <li>
                                <a class="CDSFeed-dropdown-item" 
                                   href="{{ baseUrl('send-chat-request/'.$record->user->unique_id.'/') }}">
                                    <i class="fa-regular fa-comment"></i> Send Chat Request
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Feed Content -->
    <div class="CDSFeed-feed-content">
        <div class="CDSFeed-feed-text">
            {!! html_entity_decode($record->post) !!}
        </div>
        
        <!-- Media Files -->
        @if(!empty($record->media))
            @php
                $mediaFiles = explode(',', $record->media);
            @endphp
            <div class="CDSFeed-feed-media">
                @foreach($mediaFiles as $media)
                    <div class="CDSFeed-media-item"  onclick="cdsFeedMainOpenPreview(this)" data-href="{{ baseUrl('my-feeds/view-media/'.$record->unique_id.'/'.$media) }}">
                        <img src="{{ feedDirUrl(trim($media), 't') }}" 
                             alt="Media Image" 
                             class="CDSFeed-media-image"
                             >
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- Feed Stats -->
    <div class="CDSFeed-feed-stats">
        <div class="CDSFeed-feed-stat">
            <span>👁️</span>
            <span>{{ $record->views ?? 0 }} views</span>
        </div>
        <div class="CDSFeed-feed-stat">
            <span>❤️</span>
            <span id="like-count-{{ $record->id }}">{{ $record->likes->count() }} likes</span>
        </div>
        <div class="CDSFeed-feed-stat">
            <a href="{{ baseUrl('my-feeds/detail/'.$record->unique_id) }}#comments">
                <span>💬</span>
                <span>{{ $record->comments->count() }} comments</span>
            </a>
        </div>
        
    </div>
    
    <!-- Feed Actions -->
    <div class="CDSFeed-feed-actions">
        <!-- Like Button -->
        <button class="CDSFeed-action-btn CDSFeed-like-btn {{ $record->likes->contains('added_by', auth()->id()) ? 'liked' : '' }}" 
                data-id="{{ $record->id }}">
            <i class="{{ $record->likes->contains('added_by', auth()->id()) ? 'fa-solid fa-thumbs-up' : 'fa-regular fa-thumbs-up' }}"></i>
            {{ $record->likes->contains('added_by', auth()->id()) ? 'Liked' : 'Like' }}
        </button>
        @if($record->added_by != auth()->user()->id)
            @if(checkFeedSettings($record->id,'allow_to_repost'))
                <div class="CDSFeed-feed-stat">
                    <a href="javascript:;" class="CDSFeed-action-btn" data-href="{{ baseUrl('my-feeds/repost-feed/'.$record->unique_id) }}" onclick="confirmRepostFeed(this)">
                        <i class="fa-solid fa-repeat-alt"></i>
                        <span>Repost</span>
                    </a>
                </div>
            @endif
        @endif
        <!-- Share Button -->
        <button class="CDSFeed-action-btn CDSFeed-share-btn" onclick="toggleShareOptions({{ $record->id }})">
            <i class="fa-regular fa-share-nodes"></i>
            Share
        </button>
        
        <a href="{{ baseUrl('my-feeds/detail/'.$record->unique_id) }}" class="CDSFeed-action-btn">
            View Details
        </a>

        @if($record->added_by != auth()->user()->id)
    {{-- Favourite Button (shown when not favorited) --}}
    <button class="CDSFeed-action-btn"
            onclick="addToFavorites({{$record->id}})"
            id="add-fav-btn-{{$record->id}}"
            @if(!empty(checkFavFeed($record->id))) style="display:none;" @endif>
        <img src="{{ url('assets/svg/dashboard/star-icon-outline.svg') }}" alt="Favourite" class="fav-icon"> 
        Favourite
    </button>

    {{-- Remove Favourite Button (shown when favorited) --}}
    <button class="CDSFeed-action-btn"
            onclick="removeFromFavorites({{$record->id}})"
            id="remove-fav-btn-{{$record->id}}"
            @if(empty(checkFavFeed($record->id))) style="display:none;" @endif>
        <img src="{{ url('assets/svg/dashboard/star-icon.svg') }}" alt="Favourite" class="fav-icon"> 
        Remove from Favourite
    </button>
@endif
    </div>
    
    <!-- Share Options -->
    <div class="CDSFeed-share-options" id="shareOptions-{{ $record->id }}" style="display: none;">
        <a href="javascript:;" class="CDSFeed-share-option share-email" 
           data-url="{{ baseUrl('my-feeds/'.$record->unique_id) }}">
            <i class="fa-solid fa-envelope"></i>
        </a>
        <a href="javascript:;" class="CDSFeed-share-option share-whatsapp" 
           data-url="{{ baseUrl('my-feeds/'.$record->unique_id) }}">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
        <a href="javascript:;" class="CDSFeed-share-option share-twitter" 
           data-url="{{ baseUrl('my-feeds/'.$record->unique_id) }}">
            <i class="fa-brands fa-x-twitter"></i>
        </a>
        <a href="javascript:;" class="CDSFeed-share-option share-linkedin" 
           data-url="{{ baseUrl('my-feeds/'.$record->unique_id) }}">
            <i class="fa-brands fa-linkedin"></i>
        </a>
    </div>
    

</div>
@endforeach

@endif

 <link href="{{ url('assets/css/16-CDS-case-document-preview.css') }}" rel="stylesheet" />
<script>
    function cdsFeedMainOpenPreview(e) {
    var url = $(e).data("href");

    $.ajax({
        url:url,
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
           
            if (response.status) {
                hideLoader();
                $("#cdsFeedPreviewOverlay").html(response.contents);
            } else {
                hideLoader();
                errorMessage(response.message);
            }
        },
        error: function() {
            internalError();
        }
    });
}
</script>