        <div class="cdsTYDashboard-integrated-container-component" data-container-id="1">
        <div class="cdsTYDashboard-integrated-container-header">
            <div class="cdsTYDashboard-integrated-header-left">
                <h1 class="cdsTYDashboard-integrated-container-title">Review Overview</h1>
            </div>
            <div class="cdsTYDashboard-integrated-header-controls">
                <button class="cdsTYDashboard-integrated-sidebar-toggle" aria-label="Toggle Sidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
                <button class="cdsTYDashboard-integrated-minimize-btn" aria-label="Minimize Container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 15l7-7 7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <section id="overview" class="cdsTYDashboard-integrated-section-header">
        <!-- <h2>Review Overview</h2> -->
        <p>Welcome to your minimalist dashboard. You can collapse the sidebar using the arrow button for more screen space, or minimize entire containers using the chevron button in the header.</p>
    </section>
    <div class="CdsDashboardReviews-compact-list-container">
        <div class="CdsDashboardReviews-compact-list-header">
           
            <div class="CdsDashboardReviews-compact-list-header-item CdsDashboardReviews-compact-list-professional">
                Professional Details
                <svg class="CdsDashboardReviews-compact-list-sort-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                </svg>
            </div>
            <div class="CdsDashboardReviews-compact-list-header-item CdsDashboardReviews-compact-list-status">
                Status
                <svg class="CdsDashboardReviews-compact-list-sort-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                </svg>
            </div>
            <div class="CdsDashboardReviews-compact-list-header-item CdsDashboardReviews-compact-list-sent-date">
                Sent Date
            </div>
            <div class="CdsDashboardReviews-compact-list-header-item CdsDashboardReviews-compact-list-response">
                Response
            </div>
            <div class="CdsDashboardReviews-compact-list-header-item CdsDashboardReviews-compact-list-created">
                Created At
                <svg class="CdsDashboardReviews-compact-list-sort-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                </svg>
            </div>
            <div class="CdsDashboardReviews-compact-list-header-item CdsDashboardReviews-compact-list-actions">
                Actions
            </div>
        </div>

        <div class="CdsDashboardReviews-compact-list-reviews-list">
            @if(count($reviews) > 0) 
                @foreach($reviews as $key => $record)
                    <!-- Review 1 -->
                    <div class="CdsDashboardReviews-compact-list-review-wrapper">
                        <div class="CdsDashboardReviews-compact-list-review-item">
                            <div class="CdsDashboardReviews-compact-list-professional-cell">
                                <!-- <div class="CdsDashboardReviews-compact-list-avatar"> -->
                                    {!! getProfileImage($record->user->unique_id) !!}
                                <!-- </div> -->
                                <div class="CdsDashboardReviews-compact-list-professional-info">
                                    <div class="CdsDashboardReviews-compact-list-professional-email">{{ $record->user->email ?? '' }}</div>
                                    <div class="CdsDashboardReviews-compact-list-invitation-number">Invitation #{{ $record->id }}</div>
                                </div>
                            </div>
                            <div class="CdsDashboardReviews-compact-list-status-cell">
                                <span class="CdsDashboardReviews-compact-list-status-badge">
                                    <span class="CdsDashboardReviews-compact-list-status-dot"></span>
                                    @if($record->status == 'pending')
                                        Pending Approval
                                    @elseif($record->status == 'review_given')
                                        Review Given
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    @endif
                                </span>
                            </div>
                            <div class="CdsDashboardReviews-compact-list-sent-date-cell">
                                {{ \Carbon\Carbon::parse($record->created_at)->format('M d, Y') }}{{ \Carbon\Carbon::parse($record->created_at)->format('h:i A') }}
                            </div>
                            <div class="CdsDashboardReviews-compact-list-response-cell">
                                <div class="CdsDashboardReviews-compact-list-star-rating">
                                    @if(isset($record->rating))
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="CdsDashboardReviews-compact-list-star {{ $i <= $record->rating ? '' : 'CdsDashboardReviews-compact-list-empty' }}"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    @endif
                                </div>
                            </div>
                            <div class="CdsDashboardReviews-compact-list-created-cell">
                                {{ \Carbon\Carbon::parse($record->created_at)->format('M d, Y') }}{{ \Carbon\Carbon::parse($record->created_at)->format('h:i A') }}
                            </div>
                            <div class="CdsDashboardReviews-compact-list-actions-cell">
                                <a class="CdsDashboardReviews-compact-list-show-review-btn" href="{{ baseUrl('reviews/review-received') }}?review_given={{ $record->id }}">
                                    Show Review
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
            <!-- Additional review items can be added here -->
        </div>
    </div>

    <script>
        function toggleReview(button) {
            const wrapper = button.closest('.CdsDashboardReviews-compact-list-review-wrapper');
            const panel = wrapper.querySelector('.CdsDashboardReviews-compact-list-review-panel');
            const arrow = button.querySelector('.CdsDashboardReviews-compact-list-arrow-icon');
            
            panel.classList.toggle('CdsDashboardReviews-compact-list-show');
            arrow.classList.toggle('CdsDashboardReviews-compact-list-rotated');
            button.classList.toggle('CdsDashboardReviews-compact-list-expanded');
            
            // Update button text
            if (panel.classList.contains('CdsDashboardReviews-compact-list-show')) {
                button.firstChild.textContent = 'Hide Review';
            } else {
                button.firstChild.textContent = 'Show Review';
            }
        }

        function toggleMenu(button) {
            const menu = button.nextElementSibling;
            const allMenus = document.querySelectorAll('.CdsDashboardReviews-compact-list-dropdown-menu');
            
            // Close all other menus
            allMenus.forEach(m => {
                if (m !== menu) {
                    m.classList.remove('CdsDashboardReviews-compact-list-show');
                }
            });
            
            menu.classList.toggle('CdsDashboardReviews-compact-list-show');
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.CdsDashboardReviews-compact-list-menu-btn')) {
                document.querySelectorAll('.CdsDashboardReviews-compact-list-dropdown-menu').forEach(menu => {
                    menu.classList.remove('CdsDashboardReviews-compact-list-show');
                });
            }
        });

        // Align header with content
        function alignHeaderWithContent() {
            const container = document.querySelector('.CdsDashboardReviews-compact-list-container');
            const firstReviewItem = document.querySelector('.CdsDashboardReviews-compact-list-review-item');
            const header = document.querySelector('.CdsDashboardReviews-compact-list-header');
            const headerItems = document.querySelectorAll('.CdsDashboardReviews-compact-list-header-item');
            
            if (!firstReviewItem || !header || headerItems.length === 0) return;
            
            // Only align on desktop view
            if (window.innerWidth <= 1024) {
                headerItems.forEach(item => {
                    item.style.width = '';
                    item.style.flex = '';
                });
                return;
            }
            
            // Get all the main sections in the review item
            const sections = [
                firstReviewItem.querySelector('.CdsDashboardReviews-compact-list-checkbox-cell'),
                firstReviewItem.querySelector('.CdsDashboardReviews-compact-list-professional-cell'),
                firstReviewItem.querySelector('.CdsDashboardReviews-compact-list-status-cell'),
                firstReviewItem.querySelector('.CdsDashboardReviews-compact-list-sent-date-cell'),
                firstReviewItem.querySelector('.CdsDashboardReviews-compact-list-response-cell'),
                firstReviewItem.querySelector('.CdsDashboardReviews-compact-list-created-cell'),
                firstReviewItem.querySelector('.CdsDashboardReviews-compact-list-actions-cell')
            ];
            
            // Get computed widths of each section
            const sectionWidths = sections.map(section => {
                if (section) {
                    return section.getBoundingClientRect().width;
                }
                return 0;
            });
            
            // Apply exact widths to header items
            headerItems.forEach((header, index) => {
                if (sectionWidths[index] > 0) {
                    header.style.flex = 'none';
                    header.style.width = sectionWidths[index] + 'px';
                }
            });
        }
        
        // Debounce function for resize events
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Run alignment with proper timing
        document.addEventListener('DOMContentLoaded', () => {
            alignHeaderWithContent();
            
            // Re-run after fonts load
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(() => {
                    alignHeaderWithContent();
                });
            }
        });
        
        // Handle window resize with debouncing
        window.addEventListener('resize', debounce(() => {
            alignHeaderWithContent();
        }, 250));
        
        // Fallback alignment after everything loads
        window.addEventListener('load', () => {
            setTimeout(alignHeaderWithContent, 100);
        });
        // dynamic
         // Toggle inline review
    function toggleInlineReview(id) {
        alert('hello');
        const mobileReview = document.getElementById('inline-review-' + id);
         const mobileReply = document.getElementById('inline-reply-review-' + id);
        const desktopReview = document.getElementById('desktop-review-' + id);
         const desktopReply = document.getElementById('desktop-review-reply-' + id);
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
        
             // ✅ Toggle desktop reply
    if (mobileReply) {
        if (mobileReply.style.display === 'none' || mobileReply.style.display === '') {
              mobileReply.style.display = 'block';
            // mobileReply.style.display = 'table-row';
        } else {
            mobileReply.style.display = 'none';
        }
    }

        // Toggle desktop review
        if (desktopReview) {
            if (desktopReview.style.display === 'none' || desktopReview.style.display === '') {
                  desktopReview.style.display = 'block';
                // desktopReview.style.display = 'table-row';
                if (desktopIcon) desktopIcon.style.transform = 'rotate(180deg)';
            } else {
                desktopReview.style.display = 'none';
                if (desktopIcon) desktopIcon.style.transform = 'rotate(0deg)';
            }
        }

        // ✅ Toggle desktop reply
    if (desktopReply) {
        if (desktopReply.style.display === 'none' || desktopReply.style.display === '') {
            desktopReply.style.display = 'block';
        } else {
            desktopReply.style.display = 'none';
        }
    }
    }
   
    </script>