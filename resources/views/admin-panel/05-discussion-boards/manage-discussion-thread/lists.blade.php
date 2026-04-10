@extends('admin-panel.layouts.app')
@section('page-submenu')
@php 
$page_arr = [
    'page_title' => 'Discussion Centre',
    'page_description' => 'Manage individual and group messages via message centre.',
    'page_type' => 'discussion-board',
    'canAddThread' => $canAddThread ?? false,
];
@endphp
{!! pageSubMenu('all-threads',$page_arr) !!}
@endsection
@section("styles")
<link rel="stylesheet" href="{{ url('assets/css/18-CDS-discussion-threads.css') }}">
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">
<link rel="stylesheet" href="{{ url('assets/css/edit-discussion-modal.css') }}">

@endsection

@section('content')

<div class="CDSDashboardContainer-container CDSDashboardContainer-has-sidebar" id="CDSDashboardContainer-dashboardContainer">
<div class="CDSDashboardContainer-main-content cdsfeedoverflow">

  <div class="CdsDiscussionThread-main-content">
     @if(!$canAddThread)
        <div class="alert alert-danger mb-3">
            <strong>⚠ Thread Management</strong><br>
            {{ $threadFeatureStatus['message']  }}
        </div>
    @else
        <div class="alert alert-warning mb-3">
                <strong>⚠ Thread Management</strong><br>
            {{ $threadFeatureStatus['message'] }}
        </div>
    @endif
 <div class="CdsDiscussionThread-main-content-header">
@include("admin-panel.05-discussion-boards.components.discussion-header", ['canAddThread' => $canAddThread])

  </div>
   @include("admin-panel.05-discussion-boards.components.header-search")
   <div class="CdsDiscussionThread-main-content-body">
   <div class="CdsDiscussionThread-wrapper">
    <div id="discussion-list-container">
                <!-- Discussion threads will be loaded here -->
            </div>
            
            <!-- Loading Spinner -->
            <div class="CdsDiscussionThread-loading" id="loading-spinner">
                <div class="CdsDiscussionThread-spinner"></div>
                <p style="margin-top: 10px; color: var(--CdsDiscussionThread-text-secondary);">Loading more discussions...</p>
            </div>
            
            <!-- Load More Button -->
            <div class="CdsDiscussionThread-load-more" id="load-more-container">
                <button class="CdsDiscussionThread-load-more-btn" id="load-more-btn">
                    <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Load More Discussions
                </button>
            </div>
            
            <!-- No More Data Message -->
            <div class="CdsDiscussionThread-no-more" id="no-more-data" >
                <p>You've reached the end of discussions</p>
            </div>
   </div>
   </div>
 </div><!-- Sidebar (Optional) -->
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
                  @include("admin-panel.05-discussion-boards.manage-discussion-thread.right-side-panel") </div>
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


</div>
@php
$loader_html = minify_html(view("components.skelenton-loader.discussion-comment-loader")->render());
@endphp
@endsection

@push('scripts')

<script>
    // Pagination variables
    let currentPage = 1;
    let isLoading = false;
    let hasMoreData = true;
    let autoLoadCount = 0;
    const maxAutoLoads = 3;
    let searchTimeout;

    // DOM elements
    const discussionContainer = document.getElementById('discussion-list-container');
    const discussionContainerHeader = document.getElementById('CdsDiscussionThread-list-head');
    const loadingSpinner = document.getElementById('loading-spinner');
    const loadMoreContainer = document.getElementById('load-more-container');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const noMoreData = document.getElementById('no-more-data');
    const searchInput = document.getElementById('searchInput');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadData(1);
        initializeInfiniteScroll();
        initializeSearch();
        initializeLoadMoreButton();
    });

    // Initialize infinite scroll
    function initializeInfiniteScroll() {
        window.addEventListener('scroll', handleScroll);
    }

    // Handle scroll event
    function handleScroll() {
        if (isLoading || !hasMoreData) return;
        
        // Check if we've reached the bottom
        const scrollPosition = window.innerHeight + window.scrollY;
        const threshold = document.documentElement.offsetHeight - 200; // 200px before bottom
        
        if (scrollPosition >= threshold && autoLoadCount < maxAutoLoads) {
            loadMoreData();
        }
    }

    // Initialize search
    function initializeSearch() {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                resetPagination();
                loadData(1);
            }, 500); // Debounce search
        });
    }

    // Initialize load more button
    function initializeLoadMoreButton() {
        loadMoreBtn.addEventListener('click', function() {
            loadMoreData();
        });
    }

    // Load data function
    function loadData(page = 1) {
        if (isLoading) return;
        
        isLoading = true;
        const searchValue = $("#searchInput").val();
        
        // Show loading spinner
        if (page > 1) {
            loadingSpinner.classList.add('active');
        }
        
        var filterData = {};
    
    
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
                filterData.discussion_type = selectedValue;
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
    
    // Check if Discussion Category checkboxes are active
    var discussionCategoryCheckboxes = document.querySelectorAll('input[data-category="service"]:checked');
    if (discussionCategoryCheckboxes.length > 0) {
        filterData.discussion_categories = Array.from(discussionCategoryCheckboxes).map(cb => cb.value);
    }
    
        $.ajax({
            type: 'post',
            url: BASEURL + "/manage-discussion-threads/ajax-list?page=" + page,
            dataType: 'json',
            data: {
                _token: csrf_token,
                list_type:'{{ $list_type??'all' }}',
                search: searchValue,
                category_id : '{{$category_id}}',
                  ...filterData
            },
            beforeSend: function() {
                if (page === 1) {
                    showLoader();
                }
                loadMoreBtn.disabled = true;
            },
            success: function(data) {
                // Update current page
                currentPage = page;
                
                // Handle response
                if (data.contents) {
                    if (page === 1) {
                        discussionContainer.innerHTML = data.contents;
                    } else {
                        discussionContainer.insertAdjacentHTML('beforeend', data.contents);
                        autoLoadCount++;
                    }
                    
                    // Check if there's more data
                    hasMoreData = data.has_more_data || (data.current_page < data.last_page);
                    
                    // Update UI based on state
                    updateLoadMoreUI();
                    
                    // Initialize new elements
                    initializeElements();
                } else {
                    hasMoreData = false;
                    updateLoadMoreUI();
                }
                
                if (page === 1) {
                    hideLoader();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching discussions:', error);
                if (page === 1) {
                    hideLoader();
                }
                hasMoreData = false;
                updateLoadMoreUI();
            },
            complete: function() {
                isLoading = false;
                loadingSpinner.classList.remove('active');
                loadMoreBtn.disabled = false;
            }
        });
    }

    // Load more data
    function loadMoreData() {
        if (!isLoading && hasMoreData) {
            loadData(currentPage + 1);
        }
    }

    // Reset pagination
    function resetPagination() {
        currentPage = 1;
        autoLoadCount = 0;
        hasMoreData = true;
        discussionContainer.innerHTML = '';
        loadMoreContainer.classList.remove('active');
        noMoreData.classList.remove('active');
    }
    
    // Function to reset pagination when filters change
    function resetPaginationForFilters() {
        currentPage = 1;
        autoLoadCount = 0;
        hasMoreData = true;
        discussionContainer.innerHTML = '';
        loadMoreContainer.classList.remove('active');
        noMoreData.classList.remove('active');
    }

    // Update load more UI
    function updateLoadMoreUI() {
        // Hide loading spinner
        loadingSpinner.classList.remove('active');
        
        // Check if there are any discussions in the container
        const hasDiscussions = discussionContainer.querySelector('.CdsDiscussionThread-glass-card') !== null;
        
        if (!hasMoreData) {
            // No more data
            loadMoreContainer.classList.remove('active');
            // Only show "end of discussions" if there are actually discussions
            if (hasDiscussions) {
                noMoreData.classList.add('active');
            } else {
                noMoreData.classList.remove('active');
            }
        } else if (autoLoadCount >= maxAutoLoads) {
            // Show load more button after max auto loads
            loadMoreContainer.classList.add('active');
            noMoreData.classList.remove('active');
        } else {
            // Hide both (auto-loading mode)
            loadMoreContainer.classList.remove('active');
            noMoreData.classList.remove('active');
        }
    }

    // Initialize dropdown menus
    function cdsDiscussionThreadInitDropdowns() {
        const dropdownTriggers = document.querySelectorAll('.CdsDiscussionThread-dropdown-trigger');

        dropdownTriggers.forEach(trigger => {
            // Remove existing listeners to avoid duplicates
            const newTrigger = trigger.cloneNode(true);
            trigger.parentNode.replaceChild(newTrigger, trigger);
            
            newTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdown = this.nextElementSibling;

                // Close all other dropdowns
                document.querySelectorAll('.CdsDiscussionThread-dropdown-menu').forEach(menu => {
                    if (menu !== dropdown) {
                        menu.classList.remove('active');
                    }
                });

                dropdown.classList.toggle('active');
            });
        });
    }

    // Initialize navigation links
    // function cdsDiscussionThreadInitNavLinks() {
    //     const navLinks = document.querySelectorAll('.CdsDiscussionThread-nav-link');

    //     navLinks.forEach(link => {
    //         link.addEventListener('click', function(e) {
    //             e.preventDefault();

    //             // Remove active class from all links
    //             navLinks.forEach(l => l.classList.remove('active'));

    //             // Add active class to clicked link
    //             this.classList.add('active');

    //             // Reset pagination and reload
    //             // resetPagination();
    //             // loadData(1);
    //         });
    //     });
    // }

    // Initialize action items
    function cdsDiscussionThreadInitActionItems() {
        const actionItems = document.querySelectorAll('.CdsDiscussionThread-action-item');

        actionItems.forEach(item => {
            // Skip if already initialized
            if (item.dataset.initialized) return;
            
            item.dataset.initialized = 'true';
            item.addEventListener('click', function() {
                const icon = this.querySelector('.CdsDiscussionThread-icon');
                if (icon) {
                    icon.style.transform = 'scale(1.2) rotate(15deg)';
                    setTimeout(() => {
                        icon.style.transform = 'scale(1) rotate(0)';
                    }, 300);
                }
            });
        });
    }

    // Initialize all elements
    function initializeElements() {
        cdsDiscussionThreadInitDropdowns();
        cdsDiscussionThreadInitActionItems();
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.CdsDiscussionThread-dropdown-menu').forEach(menu => {
            menu.classList.remove('active');
        });
    });

   

    // window.confirmAnyAction = function(element) {
    //     const action = element.getAttribute('data-action');
    //     if (confirm(`Are you sure you want to ${action}?`)) {
    //         window.location.href = element.getAttribute('data-href');
    //     }
    // };

    // Add ripple animation CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);

    // Initialize navigation links on page load
    // cdsDiscussionThreadInitNavLinks();

    function confirmDiscussionAction(e) {
        var url = $(e).attr("data-href");
        Swal.fire({
            title: "Are you sure to delete?",
            text: "Deleted Discussion related all comments",
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
                redirect(url);
            }
        });
    }
</script>
<link href="{{ url('assets/css/16-CDS-case-document-preview.css') }}" rel="stylesheet" />
<script>
    function cdsDiscussionMainOpenPreview(e) {
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
                    $("#cdsDiscussionPreviewOverlay").html(response.contents);
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
@endpush