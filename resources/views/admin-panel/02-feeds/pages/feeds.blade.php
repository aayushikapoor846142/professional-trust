@extends('admin-panel.layouts.app')
@section('page-submenu')
@php 
$page_arr = [
    'page_title' => 'Feeds Panel',
    'page_description' => 'Manage individual and group messages via message centre.',
    'page_type' => 'feeds',
    'canAddFeed' => $canAddFeed,
];
@endphp
{!! pageSubMenu('my-profile',$page_arr) !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/14-CDS-feeds.css') }}">
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">
@endsection
@php 
if(!isset($user)){
    $user = auth()->user();
}
@endphp
@section('content') <div class="CDSDashboardContainer-container CDSDashboardContainer-has-sidebar" id="CDSDashboardContainer-dashboardContainer">
<div class="CDSDashboardContainer-main-content cdsfeedoverflow">
          @if(!$canAddFeed)
                      <div class="alert alert-danger mb-3">
                          <strong>⚠ Feed Management</strong><br>
                          {{ $feedFeatureStatus['message']  }}
                      </div>
                  @else
                      <div class="alert alert-warning mb-3">
                              <strong>⚠ Feed Management</strong><br>
                          {{ $feedFeatureStatus['message'] }}
                      </div>
                  @endif
                    <div class="CDSFeed-main-content">
					 
              
                    <div class="CDSFeed-main-content-header">
					@include("admin-panel.02-feeds.components.feed-header")
                    </div>
                    
                     <div class="CDSFeed-main-content-body">
                   
        
                    <div class="CDSFeed-feeds-grid">
            
                    </div> <div class="CdsCaseDocumentPreview-overlay" id="cdsFeedPreviewOverlay"></div>
					</div>
                </div>
       
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
<div class="CDSDashboardContainer-sidebar-inner">
                    @include("admin-panel.02-feeds.components.feed-right-panel")
         </div>          </div>

                <!-- Mobile Menu Toggle -->
                <button class="CDSDashboardContainer-menu-toggle" id="navigationMenuToggle" aria-label="Toggle Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- Overlay -->
                <div class="CDSDashboardContainer-overlay" id="overlay"></div>
    
     </div>
    
@endsection
@section("javascript")
<script src="{{ url('assets/js/manage-feeds.js') }}"></script>
<script>
var page = 1;
var isLoading = false;
var autoLoadCount = 0;
var maxAutoLoads = 2;
var hasMorePages = true;

// Initial load
loadData(page);

function loadData(pageNum = 1, isAutoLoad = false) {
    
    // If this is page 1 (first page) or filter change, always allow loading
    // Only check hasMorePages for subsequent pages
    if (isLoading || (pageNum > 1 && !hasMorePages)) return;
   
    isLoading = true;
    var search = $("#searchInput").val();
    
    // Get current filter data from header-search component
    var filterData = {};
    
    // Check if Most Like filter is active
    var mostLikeBtn = document.getElementById('mostLikeOptionsBtn');
    if (mostLikeBtn && mostLikeBtn.classList.contains('active')) {
        filterData.like = 1;
    }
    
    // Check if Most Trending Comments filter is active
    var mostTrendingCommentsBtn = document.getElementById('mostTrendingCommentsBtn');
    if (mostTrendingCommentsBtn && mostTrendingCommentsBtn.classList.contains('active')) {
        filterData.trending_comments = 1;
    }
    
    // Check if Feed Post By filter is active
    var feedPostByDropdown = document.querySelector('.cdsTYDashboardDropdownsDropdown');
    if (feedPostByDropdown) {
        const selectedItem = feedPostByDropdown.querySelector('.cdsTYDashboardDropdownsDropdownItem.cdsTYDashboardDropdownsActive');
        if (selectedItem) {
            const selectedValue = selectedItem.getAttribute('data-value');
            if (selectedValue !== 'all') {
                filterData.feed_post_by = selectedValue;
            }
        }
    }
    
    // Check if Form Type filter is active
    var formTypeSelect = document.getElementById('form_type');
    if (formTypeSelect && formTypeSelect.value) {
        filterData.form_type = formTypeSelect.value;
    }
    
    // Check if date range filters are active
    var startDate = document.getElementById('startDate');
    var endDate = document.getElementById('endDate');
    if (startDate && startDate.value) {
        filterData.start_date = startDate.value;
    }
    if (endDate && endDate.value) {
        filterData.end_date = endDate.value;
    }
    
    // Check if seller category filters are active (checkboxes)
    var sellerCheckboxes = document.querySelectorAll('input[data-category="seller"]:checked');
    if (sellerCheckboxes.length > 0) {
        filterData.seller_filters = Array.from(sellerCheckboxes).map(cb => cb.value);
    }
    
    $.ajax({
        type: "POST",
        url: BASEURL + '/my-feeds/ajax-list?page=' + pageNum,
        data: {
            _token: csrf_token,
            search: search,
            status:'{{ $status??'all' }}',
            ...filterData
        },
        dataType: 'json',
        beforeSend: function () {
            // Show loader
            var loader = '<div id="feed-loader" class="CDSFeed-loader">';
            loader += '<div class="spinner-border" role="status">';
            loader += '<span class="sr-only"></span>';
            loader += '</div>';
            loader += '<div>Loading...</div>';
            loader += '</div>';
            
            if (pageNum === 1) {
                $(".CDSFeed-feeds-grid").html(loader);
            } else {
                // Remove any existing loader or view more button
                $("#feed-loader").remove();
                $(".CDSFeed-view-more").remove();
                $(".CDSFeed-feeds-grid").append(loader);
            }
        },
        success: function (data) {
            $("#feed-loader").remove();
            
            if (pageNum == 1) {
                $(".CDSFeed-feeds-grid").html(data.contents);
            } else {
                // Remove the view more button if it exists
                $(".CDSFeed-view-more").remove();
                $(".CDSFeed-feeds-grid").append(data.contents);
            }

            if (data.total_records > 0) {
                // Update page info
                var pageinfo = data.current_page + " of " + data.last_page +
                    " <small class='text-danger'>(" + data.total_records + " records)</small>";
                $("#pageinfo").html(pageinfo);
                $("#pageno").val(data.current_page);
                
                // Update page number
                page = data.current_page;
                
                // Check if there are more pages
                hasMorePages = data.current_page < data.last_page;
                
                // Update navigation buttons
                if (hasMorePages) {
                    $(".next").removeClass("disabled");
                } else {
                    $(".next").addClass("disabled", "disabled");
                }
                if (data.current_page > 1) {
                    $(".previous").removeClass("disabled");
                } else {
                    $(".previous").addClass("disabled", "disabled");
                }
                $("#pageno").attr("max", data.last_page);
                
                // If this was an auto-load, increment the counter
                if (isAutoLoad) {
                    autoLoadCount++;
                }
                
                // If we've reached the auto-load limit and there are more pages, show the View More button
                if (autoLoadCount >= maxAutoLoads && hasMorePages) {
                    showViewMoreButton();
                }
                
            } else if (pageNum == 1) {
                // No records found
                var html = '<div class="text-center text-danger norecord">No records available</div>';
                $(".CDSFeed-feeds-grid").html(html);
                hasMorePages = false;
            }
            
            isLoading = false;
        },
        error: function() {
            $("#feed-loader").remove();
            isLoading = false;
            console.error('Error loading feeds');
        }
    });
}

// Function to show View More button
function showViewMoreButton() {
    var viewMoreHtml = '<div class="CDSFeed-view-more ">';
    viewMoreHtml += '<button onclick="loadMoreFeeds()" class="CDSFeed-btn CdsTYButton-btn-primary CdsTYButton-border-thick">';
    viewMoreHtml += 'View More <i class="fa fa-chevron-down"></i>';
    viewMoreHtml += '</button>';
    viewMoreHtml += '</div>';
    
    // Remove any existing view more button
    $(".CDSFeed-view-more").remove();
    
    // Append the new button
    $(".CDSFeed-feeds-grid").after(viewMoreHtml);
}

// Function to load more feeds when button is clicked
function loadMoreFeeds() {
    if (hasMorePages && !isLoading) {
        loadData(page + 1, false);
    }
}

// Function to reset pagination when filters change
function resetPaginationForFilters() {
    page = 1;
    hasMorePages = true;
    autoLoadCount = 0;
    // Remove any existing view more button
    $(".CDSFeed-view-more").remove();
}

// Infinite scroll functionality
$(window).scroll(function() {
    if (autoLoadCount < maxAutoLoads && hasMorePages && !isLoading) {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            loadData(page + 1, true);
        }
    }
});

// Handle window resize
let resizeTimer;
window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
        if (window.innerWidth > 768) {
            closeFeedSidebar();
        }
    }, 250);
});

function toggleDropdown(feedId) {
    const dropdown = $('#dropdown-' + feedId);
    $('.CDSFeed-dropdown-menu').not(dropdown).removeClass('show');
    dropdown.toggleClass('show');
}

// Reset on search
$(document).on('input', '#searchInput', function() {
    // Reset variables
    page = 1;
    autoLoadCount = 0;
    hasMorePages = true;
    isLoading = false;
    
    // Debounce search
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(function() {
        loadData(1);
    }, 300);
});
</script>

<script>
// Initialize all feed interactions
$(document).ready(function() {
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
    
    $(document).on('click', '.CDSFeed-pin', function(e) {
        e.preventDefault();
        const feedId = $(this).data('id');
        const button = $(this);
        
        $.ajax({
            url: $(this).data('href'),
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    $(document).on('click', '.CDSFeed-unpin', function(e) {
        e.preventDefault();
        const feedId = $(this).data('id');
        const button = $(this);
    
        $.ajax({
            url: $(this).data('href'),
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
    // Comment toggle
    $(document).on('click', '.CDSFeed-comment-btn', function(e) {
        e.preventDefault();
        const feedId = $(this).data('id');
        const commentSection = $('#comment-section-' + feedId);
        
        if (commentSection.is(':visible')) {
            commentSection.slideUp();
        } else {
            commentSection.slideDown();
            loadComments(feedId);
        }
    });
    
    // Submit comment
    $(document).on('click', '.CDSFeed-submit-comment', function() {
        const feedId = $(this).data('id');
        const commentInput = $('#comment-input-' + feedId);
        const commentText = commentInput.val();
        const fileInput = $('#comment-file-' + feedId)[0];
        const commentFile = fileInput?.files[0];
        
        if (!commentText.trim() && !commentFile) {
            alert('Please enter a comment or select a file.');
            return;
        }
        
        const formData = new FormData();
        formData.append('comment', commentText);
        formData.append('record_id', feedId);
        if (commentFile) {
            formData.append('file', commentFile);
        }
        
        $.ajax({
            url: BASEURL + `/feeds/${feedId}/comment`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === true) {
                    commentInput.val('');
                    fileInput.value = '';
                    $('#file-preview-' + feedId).html('');
                    loadComments(feedId);
                    successMessage(response.message);
                }
            },
            error: function(xhr) {
                console.error('Error posting comment:', xhr);
            }
        });
    });
    
    // File preview
    $(document).on('change', '.CDSFeed-comment-file', function() {
        const feedId = $(this).attr('id').replace('comment-file-', '');
        const file = this.files[0];
        const previewContainer = $('#file-preview-' + feedId);
        
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.html(`
                    <div class="CDSFeed-preview-wrapper">
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="CDSFeed-remove-preview" onclick="removePreview(${feedId})">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        }
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
});

function confirmRepostFeed(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to repost Feed?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                url: url,
                type: "post",
                data:{
                    _token:csrf_token,
                },
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    if (response.status == true) {
                        hideLoader();
                        location.reload();
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
function copyFeed(e){
    var url = $(e).data("href");
    var comment_id = $(e).data('comment-id');
    Swal.fire({
        title: "Are you sure to copy feed?",
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
                type: "post",
                data:{
                    _token:csrf_token,
                },
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    if (response.status == true) {
                        hideLoader();
                        location.reload();
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

function loadComments(feedId) {
    $('#loader').show();
    $.ajax({
        url: BASEURL + `/feeds/fetch-comment`,
        type: 'POST',
        data: { id: feedId },
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#loader').hide();
            if (response.status) {
                $('#comments-list-' + feedId).html(response.contents);
            }
        },
        error: function(xhr, status, error) {
            $('#loader').hide();
            console.error('Error loading comments:', error);
        }
    });
}

function removePreview(feedId) {
    $('#file-preview-' + feedId).html('');
    $('#comment-file-' + feedId).val('');
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
</script> <script>
        const dashboardContainer = document.getElementById('CDSDashboardContainer-dashboardContainer');
        const navigationMenuToggle = document.getElementById('CDSDashboardContainer-navigationMenuToggle');
        const subSidebar = document.getElementById('CDSDashboardContainer-sidebar');
        const overlay = document.getElementById('CDSDashboardContainer-overlay');
        const collapseBtn = document.getElementById('CDSDashboardContainer-collapseBtn');
        const dragHandle = document.getElementById('CDSDashboardContainer-dragHandle');
        const mainContent = document.querySelector('.CDSDashboardContainer-main-content');
        
        let isOpen = false;
        let isCollapsed = false;
        let isDragging = false;
        let startX = 0;
        let startWidth = 0;
        
        // Min and max width constraints for sidebar
        const MIN_SIDEBAR_WIDTH = 280;
        const MAX_SIDEBAR_WIDTH = 500;

        // Toggle sidebar visibility (enable/disable)
        function toggleSidebarVisibility() {
            const toggleBtn = document.getElementById('CDSDashboardContainer-toggleSidebarBtn');
            
            if (dashboardContainer.classList.contains('CDSDashboardContainer-has-sidebar')) {
                dashboardContainer.classList.remove('CDSDashboardContainer-has-sidebar');
                dashboardContainer.classList.add('CDSDashboardContainer-no-sidebar');
                toggleBtn.textContent = 'Show Sidebar';
            } else {
                dashboardContainer.classList.remove('CDSDashboardContainer-no-sidebar');
                dashboardContainer.classList.add('CDSDashboardContainer-has-sidebar');
                toggleBtn.textContent = 'Hide Sidebar';
            }
        }

        function navigationToggleSide() {
            isOpen = !isOpen;
            
            if (isOpen) {
                subSidebar.classList.add('CDSDashboardContainer-active');
                overlay.classList.add('CDSDashboardContainer-active');
                navigationMenuToggle.classList.add('CDSDashboardContainer-active');
            } else {
                subSidebar.classList.remove('CDSDashboardContainer-active');
                overlay.classList.remove('CDSDashboardContainer-active');
                navigationMenuToggle.classList.remove('CDSDashboardContainer-active');
            }
        }

        // Toggle sidebar collapse on desktop
        function toggleCollapse() {
            if (window.innerWidth > 768) {
                isCollapsed = !isCollapsed;
                
                if (isCollapsed) {
                    subSidebar.classList.add('CDSDashboardContainer-collapsed');
                    mainContent.classList.add('CDSDashboardContainer-expanded');
                } else {
                    subSidebar.classList.remove('CDSDashboardContainer-collapsed');
                    mainContent.classList.remove('CDSDashboardContainer-expanded');
                }
            }
        }

        // Drag functionality
        function startDragging(e) {
            if (window.innerWidth <= 768 || isCollapsed) return;
            
            isDragging = true;
            startX = e.clientX;
            startWidth = subSidebar.offsetWidth;
            
            subSidebar.classList.add('CDSDashboardContainer-dragging');
            dragHandle.classList.add('CDSDashboardContainer-dragging');
            mainContent.classList.add('CDSDashboardContainer-dragging');
            
            // Prevent text selection while dragging
            document.body.style.userSelect = 'none';
            document.body.style.cursor = 'ew-resize';
            
            e.preventDefault();
        }

        function doDrag(e) {
            if (!isDragging) return;
            
            const deltaX = startX - e.clientX;
            let newWidth = startWidth + deltaX;
            
            // Apply constraints
            newWidth = Math.max(MIN_SIDEBAR_WIDTH, Math.min(MAX_SIDEBAR_WIDTH, newWidth));
            
            // Update sidebar width
            subSidebar.style.width = newWidth + 'px';
            mainContent.style.marginRight = newWidth + 'px';
        }

        function stopDragging() {
            if (!isDragging) return;
            
            isDragging = false;
            subSidebar.classList.remove('CDSDashboardContainer-dragging');
            dragHandle.classList.remove('CDSDashboardContainer-dragging');
            mainContent.classList.remove('CDSDashboardContainer-dragging');
            
            // Restore cursor and text selection
            document.body.style.userSelect = '';
            document.body.style.cursor = '';
        }

        // Initialize event listeners only if elements exist
        if (dragHandle) {
            dragHandle.addEventListener('mousedown', startDragging);
            dragHandle.addEventListener('touchstart', (e) => {
                const touch = e.touches[0];
                startDragging({ clientX: touch.clientX, preventDefault: () => {} });
            });
        }

        document.addEventListener('mousemove', doDrag);
        document.addEventListener('mouseup', stopDragging);
        document.addEventListener('touchmove', (e) => {
            if (isDragging) {
                const touch = e.touches[0];
                doDrag({ clientX: touch.clientX });
            }
        });
        document.addEventListener('touchend', stopDragging);

        if (navigationMenuToggle) {
            navigationMenuToggle.addEventListener('click', navigationToggleSide);
        }

        if (collapseBtn) {
            collapseBtn.addEventListener('click', toggleCollapse);
        }

        if (overlay) {
            overlay.addEventListener('click', navigationToggleSide);
        }

        // Handle escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (window.innerWidth <= 768 && isOpen) {
                    navigationToggleSide();
                } else if (window.innerWidth > 768 && !isCollapsed && sidebar) {
                    toggleCollapse();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const toggleBtn = document.getElementById('CDSDashboardContainer-toggleSidebarBtn');
                
                if (window.innerWidth > 768) {
                    if (isOpen && sidebar) {
                        subSidebar.classList.remove('CDSDashboardContainer-active');
                        overlay.classList.remove('CDSDashboardContainer-active');
                        navigationMenuToggle.classList.remove('CDSDashboardContainer-active');
                        isOpen = false;
                    }
                } else {
                    if (isCollapsed && sidebar) {
                        subSidebar.classList.remove('CDSDashboardContainer-collapsed');
                        mainContent.classList.remove('CDSDashboardContainer-expanded');
                        isCollapsed = false;
                    }
                    // Reset custom width on mobile
                    if (sidebar) {
                        subSidebar.style.width = '';
                        mainContent.style.marginRight = '';
                    }
                }
                
                // Update button text based on current state
                if (toggleBtn) {
                    if (dashboardContainer.classList.contains('CDSDashboardContainer-has-sidebar')) {
                        toggleBtn.textContent = 'Hide Sidebar';
                    } else {
                        toggleBtn.textContent = 'Show Sidebar';
                    }
                }
            }, 250);
        });

        // Touch support for mobile swipe
        if (sidebar) {
            let touchStartX = 0;
            let touchEndX = 0;

            subSidebar.addEventListener('touchstart', (e) => {
                if (!e.target.closest('.CDSDashboardContainer-drag-handle')) {
                    touchStartX = e.changedTouches[0].screenX;
                }
            });

            subSidebar.addEventListener('touchend', (e) => {
                if (!e.target.closest('.CDSDashboardContainer-drag-handle')) {
                    touchEndX = e.changedTouches[0].screenX;
                    if (touchEndX - touchStartX > 50 && isOpen) {
                        navigationToggleSide();
                    }
                }
            });
        }

        // Add tooltips
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('CDSDashboardContainer-toggleSidebarBtn');
            
            // Set initial button text
            if (toggleBtn) {
                if (dashboardContainer.classList.contains('CDSDashboardContainer-has-sidebar')) {
                    toggleBtn.textContent = 'Hide Sidebar';
                } else {
                    toggleBtn.textContent = 'Show Sidebar';
                }
            }
            
            if (window.innerWidth > 768) {
                if (collapseBtn) collapseBtn.title = 'Collapse Sidebar';
                if (dragHandle) dragHandle.title = 'Drag to resize';
            }
        });
    </script>

@endsection