@extends('admin-panel.layouts.app')
@section('page-submenu')
@php 
$page_arr = [
    'page_title' => 'Feed Details',
    'page_description' => 'Manage individual and group messages via message centre.',
    'page_type' => 'feeds-details',
];
@endphp
{!! pageSubMenu('my-profile',$page_arr) !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/14-CDS-feeds.css') }}">
@endsection
@section('content')
<!-- Header -->
<div class="CDSDashboardContainer-container CDSDashboardContainer-has-sidebar" id="CDSDashboardContainer-dashboardContainer">
<div class="CDSDashboardContainer-main-content">
    <!-- Main Content -->
                <div class="CDSFeed-main-content">
				 <div class="CDSFeed-main-content-body"><div class="CDSFeed-feeds-details-inner-wrap">
                    <!-- Feed Detail Card -->
                    <div class="CDSFeed-feed-detail-card">
                        <div class="CDSFeed-feed-card-details">
                        <div class="CDSFeed-feed-detail-header">
                            <div class="CDSFeed-feed-meta-row">
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
                                        <span class="CDSFeed-feed-time">{{ $record->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <!-- Status and Actions -->
                                <div class="CDSFeed-feed-header-actions">
                                    @if($record->status == 'published')
                                        <span class="CDSFeed-feed-status CDSFeed-status-published">Published</span>
                                    @elseif($record->status == 'draft')
                                        <span class="CDSFeed-feed-status CDSFeed-status-draft">Draft</span>
                                    @elseif($record->status == 'scheduled')
                                        <span class="CDSFeed-feed-status CDSFeed-status-scheduled">Scheduled</span>
                                    @endif
                                    
                                    <!-- Follow/Unfollow Button (for other users) -->
                                    @if(auth()->user()->id != $record->user->id)
                                        @if(checkIfFollowing(auth()->user()->id, $record->user->id) > 0)
                                            <button class="CDSFeed-btn-follow CdsTYButton-btn-secondary " 
                                                    onclick="unfollow({{$record->user->id}},'following')">
                                                Unfollow
                                            </button>
                                        @else
                                            <button class="CDSFeed-btn-follow CdsTYButton-btn-primary" 
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
                                                    <a class="CDSFeed-dropdown-item" href="javascript:;" onclick="feedEdit('{{ baseUrl('my-feeds/edit/' . $record->unique_id) }}')">
                                                        <i class="fa-regular fa-pen"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="CDSFeed-dropdown-item" href="{{ baseUrl('feeds/copy/'.$record->unique_id) }}">
                                                        <i class="fa-regular fa-copy"></i> Copy
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="CDSFeed-dropdown-item CDSFeed-text-danger" href="javascript:;" 
                                                    onclick="confirmAction(this)" 
                                                    data-href="{{ baseUrl('my-feeds/delete/'.$record->unique_id) }}">
                                                        <i class="fa-regular fa-trash"></i> Delete
                                                    </a>
                                                </li>
                                            @else
                                                <!-- Follow/Unfollow in dropdown -->
                                                {{-- @if(auth()->user()->id != $record->user->id)
                                                <li>
                                                    @if(checkIfFollowing(auth()->user()->id, $record->user->id) > 0)
                                                        <a class="CDSFeed-dropdown-item" href="javascript:;" 
                                                        onclick="unfollow({{$record->user->id}},'following')">
                                                            <i class="fa-solid fa-user-minus"></i> Unfollow
                                                        </a>
                                                    @else
                                                        <a class="CDSFeed-dropdown-item" href="javascript:;" 
                                                        onclick="followBack({{$record->user->id}},'following')">
                                                            <i class="fa-solid fa-user-plus"></i> Follow
                                                        </a>
                                                    @endif
                                                </li>
                                                @endif --}}
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
                        </div>
                        
                            <div class="CDSFeed-feed-content">
                                <div class="CDSFeed-feed-text">
                                    {!! html_entity_decode($record->post) !!}
                                </div>
                            </div>
                            <!-- Media Files -->
                            @if(!empty($record->media))
                                @php
                                    $mediaFiles = explode(',', $record->media);
                                @endphp
                                <div class="CDSFeed-feed-media">
                                    @foreach($mediaFiles as $media)
                                        <div class="CDSFeed-media-item" onclick="cdsFeedOpenPreview(this)" data-href="{{ baseUrl('my-feeds/view-media/'.$record->unique_id.'/'.$media) }}">
                                            <img src="{{ feedDirUrl(trim($media), 't') }}" 
                                                alt="Media Image" 
                                                class="CDSFeed-media-image"
                                                >
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="CDSFeed-engagement-bar">
                            <div class="CDSFeed-engagement-stats">
                                <div class="CDSFeed-stat">
                                    <span class="CDSFeed-stat-icon">❤️</span>
                                    <span>{{ $record->likes->count() }} likes</span>
                                </div>
                                <div class="CDSFeed-stat">
                                    <span class="CDSFeed-stat-icon">💬</span>
                                    <span><span class="CDSFeed-comments-count">{{ $record->comments->count() }}</span> comments</span>
                                </div>
                            </div>
                            <div class="CDSFeed-engagement-actions">
                                <button class="CDSFeed-action-btn CDSFeed-like-btn {{ $record->likes->contains('added_by', auth()->id()) ? 'liked' : '' }}" 
                                        data-id="{{ $record->id }}">
                                    <i class="{{ $record->likes->contains('added_by', auth()->id()) ? 'fa-solid fa-thumbs-up' : 'fa-regular fa-thumbs-up' }}"></i>
                                    {{ $record->likes->contains('added_by', auth()->id()) ? 'Liked' : 'Like' }}
                                </button>
                                <button class="CDSFeed-action-btn CDSFeed-share-btn" onclick="toggleShareOptions({{ $record->id }})">
                                    <i class="fa-regular fa-share-nodes"></i>
                                    Share
                                </button>
                            </div>
                        </div>
                        <!-- Share Options -->
                        <div class="CDSFeed-share-options" id="shareOptions-{{ $record->id }}" style="display: none;">
                            <a href="javascript:;" class="CDSFeed-share-option share-email" 
                            data-url="{{ baseUrl('feeds/'.$record->unique_id) }}">
                                <i class="fa-solid fa-envelope"></i>
                            </a>
                            <a href="javascript:;" class="CDSFeed-share-option share-whatsapp" 
                            data-url="{{ baseUrl('feeds/'.$record->unique_id) }}">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                            <a href="javascript:;" class="CDSFeed-share-option share-twitter" 
                            data-url="{{ baseUrl('feeds/'.$record->unique_id) }}">
                                <i class="fa-brands fa-x-twitter"></i>
                            </a>
                            <a href="javascript:;" class="CDSFeed-share-option share-linkedin" 
                            data-url="{{ baseUrl('feeds/'.$record->unique_id) }}">
                                <i class="fa-brands fa-linkedin"></i>
                            </a>
                        </div>
                    </div>
                    <div class="CDSFeed-edit"></div>
                    <!-- Comments Section -->
                    <section id="comments" class="CDSFeed-comments-section">
                        <div class="CDSFeed-comments-header">
                            <h2 class="CDSFeed-comments-title">Comments</h2>
                            <span>
                                <span class="CDSFeed-comments-count">{{ $record->comments->count() }}</span>
                                comments
                            </span>
                        </div>
        
                        <form class="CDSFeed-comment-form" id="commentForm" action="{{ baseUrl('my-feeds/save-comment/'.$record->unique_id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="CDSFeed-comment-input-wrapper">
                                <div class="CDSFeed-comment-avatar">
                                    {!! getProfileImage(auth()->user()->unique_id) !!}
                                </div>
                                <div class="CDSFeed-comment-input-container">
                                    <textarea class="CDSFeed-comment-input" name="comment" id="comment" placeholder="Add your comment..."></textarea>
                                    <div class="CDSFeed-comment-toolbar">
                                        <button type="button" class="CDSFeed-emoji-btn message-emoji-icon" title="Add emoji">
                                            <i class="fa-regular fa-face-smile"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="CDSFeed-comment-uploader">
                                <div class="CDSFeed-upload-container" id="feedMediaUpload">
                                    <div class="CDSFeed-upload-area">
                                        <input type="file" class="CDSFeed-file-input" multiple accept="image/*,.pdf,.doc,.docx" style="display: none;">
                                        <div class="CDSFeed-upload-icon">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                        </div>
                                        <p class="CDSFeed-upload-text">Drag and drop files here or click to browse</p>
                                        <p class="CDSFeed-upload-hint">Supports: JPG, PNG, GIF, PDF, DOC, DOCX (Max 10MB per file)</p>
                                    </div>
                                    
                                    <!-- File Preview Area -->
                                    <div class="CDSFeed-file-list" style="display: none;">
                                        <!-- Files will be dynamically added here -->
                                    </div>
                                </div>
                            </div>
                            <div class="CDSFeed-comment-actions">
                               
                                <button type="submit" class="CDSFeed-btn CdsTYButton-btn-primary">Post Comment</button>
                            </div>
                        </form>
                        <div class="CDSFeed-comments-list"></div>
                        
                    </section>
                </div></div> </div>
       
	
	
	
	
	
	
 </div>

                <!-- Sidebar (Optional) -->
                <div class="CDSDashboardContainer-sidebar" id="sidebar">
                    <!-- Drag Handle (visible only on desktop) -->
                    <div class="CDSDashboardContainer-drag-handle" id="dragHandle"></div>

                    <!-- Collapse Button (visible only on desktop) -->
                    <button class="CDSDashboardContainer-collapse-btn" id="collapseBtn" aria-label="Toggle Sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                        </svg>
                    </button>
     @include("admin-panel.02-feeds.components.feed-right-panel")
                  
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="CDSDashboardContainer-menu-toggle" id="menuToggle" aria-label="Toggle Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- Overlay -->
                <div class="CDSDashboardContainer-overlay" id="overlay"></div>
    
     </div>	
	
	
	
	

    <div class="CdsCaseDocumentPreview-overlay" id="cdsFeedPreviewOverlay"></div>
@endsection

@section("javascript")
<link href="{{ url('assets/css/16-CDS-case-document-preview.css') }}" rel="stylesheet" />
<script src="{{url('assets/js/custom-editor.js')}}"></script>
<script src="{{url('assets/js/custom-file-upload.js')}}"></script>

<link href="{{ url('assets/css/custom-file-upload.css') }}" rel="stylesheet" />
<script src="{{url('assets/js/manage-feeds.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    await initializeManageFeedSocket("{{$record->id}}");
    await testWebSocket("{{$record->id}}")
});
// new EmojiPicker(".message-emoji-icon", {
//     targetElement: "#comment",
// });
const emojiPicker = new EmojiPicker(".message-emoji-icon", {
    // Do not specify targetElement here
    onEmojiSelect: function(emoji) {
        insertAtCursor(emoji);
    }
});

// Function to insert emoji at cursor position
function insertAtCursor(emoji) {
    const textarea = document.getElementById("comment");
    const startPos = textarea.selectionStart;
    const endPos = textarea.selectionEnd;
    
    // Insert emoji at cursor position
    textarea.value = 
        textarea.value.substring(0, startPos) + 
        emoji + 
        textarea.value.substring(endPos);
    
    // Move cursor to position after the inserted emoji
    const newCursorPos = startPos + emoji.length;
    textarea.selectionStart = newCursorPos;
    textarea.selectionEnd = newCursorPos;
    
    // Focus back on the textarea
    textarea.focus();
}
// Initialize file upload manager by ID
const feedUploader = new FileUploadManager('#feedMediaUpload', {
    maxFileSize: 10 * 1024 * 1024, // 10MB
    maxFiles: 5, // Maximum 5 files
    allowedTypes: [
        'image/jpeg', 
        'image/png', 
        'image/gif', 
    ],
    onFileAdded: function(fileData) {
        console.log('File added:', fileData.name);
    },
    onFileRemoved: function(fileData) {
        console.log('File removed:', fileData.name);
    },
    onError: function(message) {
        // Custom error handling
        errorMessage(message, 'error');
    }
});

// Initialize the uploader
feedUploader.init();
// Handle window resize
let resizeTimer;
window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    }, 250);
});

function toggleDropdown(feedId) {
    const dropdown = $('#dropdown-' + feedId);
    $('.CDSFeed-dropdown-menu').not(dropdown).removeClass('show');
    dropdown.toggleClass('show');
}
// Form submission
document.getElementById('commentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    // Add files from uploader
    const files = feedUploader.getFiles();
    files.forEach((file, index) => {
        formData.append(`attachment[${index}]`, file);
    });
    try {
        $.ajax({
            url: $(this).attr("action"),
            type: "post",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: function() {
                showLoader();
                $(".CDSFeed-btn").attr("disabled","disabled");
            },
            
            success: function(response) {
                $(".CDSFeed-btn").removeAttr("disabled");
                if (response.status == true) {
                    hideLoader();
                    successMessage(response.message);
                    $("#commentForm")[0].reset();
                    feedUploader.reset();
                } else {
                    hideLoader();
                    errorMessage(response.message);
                    // validation(response.message);
                }
            },
            error: function() {
                $(".CDSFeed-btn").removeAttr("disabled");
                hideLoader();
                internalError();
            }
        });
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});
</script>

<script>
// Initialize all feed interactions
$(document).ready(function() {

    $(document).on('click','.comment-unlike',function(){
        const button = $(this);
        const comment_id = $(this).data("comment-id");
        $.ajax({
            url: $(this).attr("data-href"),
            type: 'POST',
            data:{
                _token:csrf_token
            },
            success: function(response) {
                if (response.status) {
                    button.removeClass('comment-unlike');
                    button.removeClass('liked');
                    button.addClass('comment-like');
                    setTimeout(() => {
                        button.attr('data-href',"{{ baseUrl('my-feeds/comment-like/') }}/"+comment_id);
                    }, 200);
                    
                    button.html('<i class="fa-regular fa-thumbs-up"></i> Like ('+response.like_count+')');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
    $(document).on('click','.comment-like',function(){
        const button = $(this);
        const comment_id = $(this).data("comment-id");
        $.ajax({
            url: $(this).attr("data-href"),
            type: 'POST',
            data:{
                _token:csrf_token
            },
            success: function(response) {
                if (response.status) {
                    button.addClass('comment-unlike liked');
                    button.removeClass('comment-like');
                    setTimeout(() => {
                        button.attr('data-href',"{{ baseUrl('my-feeds/comment-unlike/') }}/"+comment_id);
                    }, 200);
                    
                    button.html('<i class="fa-solid fa-thumbs-up"></i> Like ('+response.like_count+')');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
    // Share functionality
    $(document).on('click', '.share-email', function() {
        const postUrl = $(this).data('url');
        const subject = encodeURIComponent("Check this out!");
        const body = encodeURIComponent("Here's something interesting: " + postUrl);
        window.open(`mailto:?subject=${subject}&body=${body}`);
    });
    $(document).on('click', '.share-whatsapp', function() {
        const postUrl = $(this).data('url');
        const message = encodeURIComponent("Check this out: " + postUrl);
        window.open(`https://wa.me/?text=${message}`, '_blank');
    });
    $(document).on('click', '.share-twitter', function() {
        const postUrl = $(this).data('url');
        const text = encodeURIComponent("Check this out: " + postUrl);
        window.open(`https://twitter.com/intent/tweet?text=${text}`, '_blank');
    });
    $(document).on('click', '.share-linkedin', function() {
        const postUrl = $(this).data('url');
        const text = encodeURIComponent("Check this out!");
        window.open(`https://www.linkedin.com/shareArticle?mini=true&url=${postUrl}&title=${text}`, '_blank');
    });
    // Like functionality
    $(document).on('click', '.CDSFeed-like-btn', function(e) {
        e.preventDefault();
        const feedId = $(this).data('id');
        const button = $(this);
        
        $.ajax({
            url: BASEURL + `/my-feeds/${feedId}/like`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.liked) {
                    button.addClass('liked');
                    button.html('<i class="fa-solid fa-thumbs-up"></i> Liked');
                } else {
                    button.removeClass('liked');
                    button.html('<i class="fa-regular fa-thumbs-up"></i> Like');
                }
                $('#like-count-' + feedId).text(response.likeCount + ' likes');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
    
});

// Helper functions
function toggleDropdown(feedId) {
    const dropdown = $('#dropdown-' + feedId);
    $('.CDSFeed-dropdown-menu').not(dropdown).removeClass('show');
    dropdown.toggleClass('show');
}

function toggleShareOptions(feedId) {
    const shareOptions = $('#shareOptions-' + feedId);
    $('.CDSFeed-share-options').not(shareOptions).slideUp();
    shareOptions.slideToggle();
}

function viewImage(imageUrl) {
    // Implement image viewer modal
    window.open(imageUrl, '_blank');
}

// Click outside to close dropdowns
$(document).click(function(e) {
    if (!$(e.target).closest('.CDSFeed-dropdown').length) {
        $('.CDSFeed-dropdown-menu').removeClass('show');
    }
});
function toggleEditComment(commentId,action) {
    if(action == 'show'){
        $.ajax({
            url: "{{ baseUrl('my-feeds/edit-comment/') }}/"+commentId,
            type: "post",
            data:{
                _token:'{{ csrf_token() }}',
            },
            dataType: "json",
            beforeSend: function() {
                var loader = '<div id="feed-loader" class="CDSFeed-loader">';
                loader += '<div class="spinner-border" role="status">';
                loader += '<span class="sr-only"></span>';
                loader += '</div>';
                loader += '<div>Loading...</div>';
                loader += '</div>';
                $("#CDSFeed-reply-"+commentId).html(loader);
            },
            
            success: function(response) {
                if (response.status == true) {
                    $("#CDSFeed-reply-"+commentId).html(response.contents);
                } else {
                    $("#CDSFeed-reply-"+commentId).html('');
                }
            },
            error: function() {
                hideLoader();
                internalError();
            }
        });
    }else{
        $("#CDSFeed-reply-"+commentId).html('');
    }
}
function toggleReplyForm(commentId,action) {
    if(action == 'show'){
        $.ajax({
            url: "{{ baseUrl('my-feeds/reply-comment-form/') }}/"+commentId,
            type: "post",
            data:{
                _token:'{{ csrf_token() }}',
            },
            dataType: "json",
            beforeSend: function() {
                var loader = '<div id="feed-loader" class="CDSFeed-loader">';
                loader += '<div class="spinner-border" role="status">';
                loader += '<span class="sr-only"></span>';
                loader += '</div>';
                loader += '<div>Loading...</div>';
                loader += '</div>';
                $("#CDSFeed-reply-"+commentId).html(loader);
            },
            
            success: function(response) {
                if (response.status == true) {
                    $("#CDSFeed-reply-"+commentId).html(response.contents);
                } else {
                    $("#CDSFeed-reply-"+commentId).html('');
                }
            },
            error: function() {
                hideLoader();
                internalError();
            }
        });
    }else{
        $("#CDSFeed-reply-"+commentId).html('');
    }
}

// Toggle show more replies
document.querySelectorAll('.CDSFeed-show-replies-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        // Your logic to load more replies
        console.log('Load more replies');
    });
});
</script>
<script>
function toggleUploadDiv() {
    const uploadDiv = document.getElementById('CDSFeed-comment-uploader');
    uploadDiv.classList.toggle('show');
}

// Initialize when page loads
window.addEventListener('load', function() {
    // Remove the inline style
    const uploadDiv = document.getElementById('CDSFeed-comment-uploader');
    if (uploadDiv) uploadDiv.style.display = '';
    
});
</script>

<script>
    (function() {
    let dragCounter = 0;
    let uploadDiv = null;
    
    // Allowed file types
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
    
    // Check if drag contains files
    function hasFiles(e) {
        if (e.dataTransfer.types) {
            for (let i = 0; i < e.dataTransfer.types.length; i++) {
                if (e.dataTransfer.types[i] === "Files") {
                    return true;
                }
            }
        }
        return false;
    }
    
    // Show upload div with animation
    function showUploadDiv() {
        if (uploadDiv && !uploadDiv.classList.contains('show')) {
            uploadDiv.classList.add('show');
            // Add visual feedback
            uploadDiv.classList.add('drag-active');
        }
    }
    
    // Hide upload div with animation
    function hideUploadDiv() {
        if (uploadDiv) {
            uploadDiv.classList.remove('drag-active');
            // Optional: auto-hide after drag ends (comment out if you want it to stay open)
            setTimeout(() => {
                if (!uploadDiv.classList.contains('has-files')) {
                    uploadDiv.classList.remove('show');
                }
            }, 300);
        }
    }
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        uploadDiv = document.getElementById('CDSFeed-comment-uploader');
        
        // Remove inline display:none
        if (uploadDiv) {
            uploadDiv.style.display = '';
        }
        
        // Page-wide drag enter
        document.addEventListener('dragenter', function(e) {
            e.preventDefault();
            
            if (hasFiles(e)) {
                dragCounter++;
                if (dragCounter === 1) {
                    showUploadDiv();
                }
            }
        });
        
        // Page-wide drag over
        document.addEventListener('dragover', function(e) {
            e.preventDefault();
        });
        
        // Page-wide drag leave
        document.addEventListener('dragleave', function(e) {
            e.preventDefault();
            
            if (hasFiles(e)) {
                dragCounter--;
                if (dragCounter === 0) {
                    hideUploadDiv();
                }
            }
        });
        
        // Page-wide drop (to reset counter)
        document.addEventListener('drop', function(e) {
            e.preventDefault();
            dragCounter = 0;
            
            // Check if dropped outside upload area
            const uploadArea = document.querySelector('.CDSFeed-upload-area');
            if (!uploadArea || !uploadArea.contains(e.target)) {
                // Check file types before hiding
                let hasValidFiles = false;
                
                if (e.dataTransfer.files.length > 0) {
                    for (let file of e.dataTransfer.files) {
                        if (allowedTypes.includes(file.type) || file.type.startsWith('image/')) {
                            hasValidFiles = true;
                            break;
                        }
                    }
                }
                
                if (!hasValidFiles) {
                    hideUploadDiv();
                }
            }
        });
    });
    
    // CSS for animations and visual feedback
    const style = document.createElement('style');
    style.textContent = `
        /* Animation styles */
        #CDSFeed-comment-uploader {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        #CDSFeed-comment-uploader.show {
            max-height: 600px;
            opacity: 1;
            padding: 15px 0;
        }
        
        /* Drag active state */
        #CDSFeed-comment-uploader.drag-active .CDSFeed-upload-area {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.05);
            transform: scale(1.02);
            transition: all 0.2s ease;
        }
        
        /* Pulse animation when dragging */
        #CDSFeed-comment-uploader.drag-active .CDSFeed-upload-icon {
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Optional: Highlight the upload area */
        .CDSFeed-upload-area {
            transition: all 0.2s ease;
        }
    `;
    document.head.appendChild(style);
})();
</script>


<!-- Comments -->
<script>

loadComments(true,'{{ $record->unique_id }}');

function loadMoreComments() {
    if (hasMorePages && !isLoading) {
        loadComments(false,'{{$record->unique_id}}','older_comment');
    }
}
function showViewMoreButton() {
    var viewMoreHtml = '<div class="CDSFeed-view-more">';
    viewMoreHtml += '<button onclick="loadMoreComments()" class="CDSFeed-btn CdsTYButton-btn-primary CdsTYButton-border-thick">';
    viewMoreHtml += 'View More <i class="fa fa-chevron-down"></i>';
    viewMoreHtml += '</button>';
    viewMoreHtml += '</div>';
    
    // Remove any existing view more button
    $(".CDSFeed-view-more").remove();
    
    // Append the new button
    $(".CDSFeed-comments-list").after(viewMoreHtml);
}

function feedEdit(url){
    $.ajax({
        url: url,
        type: "get",
        dataType: "json",
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            if (response.status == true) {
                hideLoader();
                $(".CDSFeed-edit").html(response.contents);
            } else {
                $(".CDSFeed-edit").html('');
            }
            // Scroll to the CDSFeed-edit element
            $('html, body').animate({
                scrollTop: $(".CDSFeed-edit").offset().top
            }, 500); // 500ms for smooth scrolling
        },
        error: function() {
            hideLoader();
            internalError();
        }
    });
}
function deleteComment(e){
    var url = $(e).data("href");
    var comment_id = $(e).data('comment-id');
    Swal.fire({
        title: "Are you sure to delete comment?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if(result.value){
            $.ajax({
                url: url,
                type: "get",
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    if (response.status == true) {
                        hideLoader();
                    }
                },
                error: function() {
                    hideLoader();
                    internalError();
                }
            });
        }
    });
}


function cdsFeedOpenPreview(e) {
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

@endsection
