@extends('admin-panel.layouts.app',['dashboard_layout'=>'layout-01'])
<link rel="stylesheet" href="{{ asset('assets/css/42-CDS-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/20-1-CDS-cases-list.css') }}">
<link href="{{ url('assets/css/25-CDS-transaction-overview.css') }}" rel="stylesheet" />
@section('content')
@include('admin-panel.dashboard-tabs.common.dashboard-nav', ['activeTab' => 'overview'])

<div class="CDSDashboardContainer-main-content-inner-header-dashboard">
    <!-- Action Bar -->
    <div class="cdsTYDashboard-main-action-bar">
        <div class="cdsTYDashboard-main-action-text">
            <h2>Quick Actions</h2>
            <p>Manage your daily operations efficiently</p>
        </div>
        <div class="cdsTYDashboard-main-action-buttons">
            <button class="cdsTYDashboard-main-btn cdsTYDashboard-main-btn-white" onclick="handleAction('add-case')">
                <span>➕</span>
                <span>Add New Case</span>
            </button>
            <button class="cdsTYDashboard-main-btn cdsTYDashboard-main-btn-transparent" onclick="handleAction('book-appointment')">
                <span>📅</span>
                <span>Book Appointment</span>
            </button>
            <button class="cdsTYDashboard-main-btn cdsTYDashboard-main-btn-transparent" onclick="handleAction('send-invoice')">
                <span>📧</span>
                <span>Send Invoice</span>
            </button>
            <button class="cdsTYDashboard-main-btn cdsTYDashboard-main-btn-transparent" onclick="handleAction('add-staff')">
                <span>👥</span>
                <span>Add Staff</span>
            </button>
        </div>
    </div>
</div>

<div class="cdsTYDashboard-integrated-container-outer">		 
    <!-- <div class="cdsTYDashboard-integrated-container-component" data-container-id="1">
        <div class="cdsTYDashboard-integrated-container-header">
            <div class="cdsTYDashboard-integrated-header-left">
                <h1 class="cdsTYDashboard-integrated-container-title">Dashboard</h1>
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
        </div> -->
        
        <div class="cdsTYDashboard-integrated-container-wrapper">
            <main class="cdsTYDashboard-integrated-content">
                <div id="overview" class="tab-content">
                    @include('admin-panel.dashboard-tabs.overview') 
                </div>  
                <div id="cases" class="tab-content"> 
                    @include('admin-panel.dashboard-tabs.cases')    
                </div>
                <div id="appointments" class="tab-content">
                    @include('admin-panel.dashboard-tabs.appointments')        
                </div>
                <div id="messages" class="tab-content">
                    @include('admin-panel.dashboard-tabs.messages')                   
                </div>
                <div id="review" class="tab-content">
                    @include('admin-panel.dashboard-tabs.review')
                </div>
                <div id="transactions" class="tab-content">
                    @include('admin-panel.dashboard-tabs.transactions')
                </div>
                <div id="reports" class="tab-content">
                    @include('admin-panel.dashboard-tabs.reports')
                </div>
            </main>
        </div>
    </div>
    <button class="cdsTYDashboard-integrated-menu-toggle" aria-label="Toggle Menu">☰</button>
</div>		 
			 
 

@endsection

@section('javascript')
<script src="{{ asset('assets/js/transaction-overview/revenue-overview-chart.js') }}"></script>
<script src="{{ asset('assets/js/transaction-overview/transaction-types-chart.js') }}"></script>
  <script>
        class CollapsibleContainer {
            constructor(containerElement) {
                this.container = containerElement;
                this.sidebar = this.container.querySelector('.cdsTYDashboard-integrated-sidebar');
                this.sidebarOverlay = this.container.querySelector('.cdsTYDashboard-integrated-sidebar-overlay');
                this.sidebarClose = this.container.querySelector('.cdsTYDashboard-integrated-sidebar-close');
                this.sidebarItems = this.container.querySelectorAll('.cdsTYDashboard-integrated-sidebar-item');
                this.content = this.container.querySelector('.cdsTYDashboard-integrated-content');
                this.wrapper = this.container.querySelector('.cdsTYDashboard-integrated-container-wrapper');
                this.minimizeBtn = this.container.querySelector('.cdsTYDashboard-integrated-minimize-btn');
                this.sidebarToggle = this.container.querySelector('.cdsTYDashboard-integrated-sidebar-toggle');
                this.sidebarCollapseToggle = this.container.querySelector('.cdsTYDashboard-integrated-sidebar-collapse-toggle');
                
                this.isMinimized = false;
                this.isSidebarCollapsed = localStorage.getItem(`sidebar-collapsed-${this.container.dataset.containerId}`) === 'true';
                this.sections = [];
                this.isScrolling = false;
                
                this.init();
            }

            init() {
                // Apply saved sidebar state
                if (this.isSidebarCollapsed && window.innerWidth > 768) {
                    this.wrapper.classList.add('cdsTYDashboard-integrated-sidebar-collapsed');
                }

                // Minimize button
                if (this.minimizeBtn) {
                    this.minimizeBtn.addEventListener('click', () => this.toggleMinimize());
                }

                // Sidebar toggle button (header)
                if (this.sidebarToggle) {
                    this.sidebarToggle.addEventListener('click', () => this.toggleSidebarCollapse());
                }

                // Sidebar collapse toggle (inside sidebar)
                if (this.sidebarCollapseToggle) {
                    this.sidebarCollapseToggle.addEventListener('click', () => this.toggleSidebarCollapse());
                }

                // Handle sidebar item clicks
                this.sidebarItems.forEach(item => {
                    item.addEventListener('click', (e) => this.handleSidebarClick(e));
                });

                // Close sidebar button (mobile)
                if (this.sidebarClose) {
                    this.sidebarClose.addEventListener('click', () => this.closeSidebar());
                }

                // Close sidebar when clicking overlay
                if (this.sidebarOverlay) {
                    this.sidebarOverlay.addEventListener('click', () => this.closeSidebar());
                }

                // Initialize scroll spy
                this.initScrollSpy();
            }

            initScrollSpy() {
                // Collect all sections that have corresponding sidebar items
                this.sidebarItems.forEach(item => {
                    const sectionId = item.dataset.section;
                    const section = this.content.querySelector(`#${sectionId}`);
                    if (section) {
                        this.sections.push({
                            id: sectionId,
                            element: section,
                            menuItem: item
                        });
                    }
                });

                // Use Intersection Observer for better performance
                if (this.sections.length > 0) {
                    const options = {
                        root: null,
                        rootMargin: '-20% 0px -70% 0px',
                        threshold: 0
                    };

                    const observer = new IntersectionObserver((entries) => {
                        if (this.isScrolling) return;
                        
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const section = this.sections.find(s => s.element === entry.target);
                                if (section) {
                                    this.setActiveMenuItem(section.menuItem);
                                }
                            }
                        });
                    }, options);

                    // Observe all sections
                    this.sections.forEach(section => {
                        observer.observe(section.element);
                    });
                }
            }

            setActiveMenuItem(activeItem) {
                this.sidebarItems.forEach(item => {
                    item.classList.remove('cdsTYDashboard-integrated-active');
                });
                activeItem.classList.add('cdsTYDashboard-integrated-active');
            }

            toggleMinimize() {
                this.isMinimized = !this.isMinimized;
                this.container.classList.toggle('cdsTYDashboard-integrated-minimized');
                
                // Smooth scroll to container header when minimizing
                if (this.isMinimized) {
                    this.container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            toggleSidebarCollapse() {
                if (window.innerWidth <= 768) return;
                
                this.isSidebarCollapsed = !this.isSidebarCollapsed;
                this.wrapper.classList.toggle('cdsTYDashboard-integrated-sidebar-collapsed');
                
                // Save state to localStorage
                localStorage.setItem(
                    `sidebar-collapsed-${this.container.dataset.containerId}`, 
                    this.isSidebarCollapsed
                );
            }

            handleSidebarClick(e) {
                e.preventDefault();
                
                const clickedItem = e.target.closest('.cdsTYDashboard-integrated-sidebar-item');
                
                // Set active class immediately
                this.setActiveMenuItem(clickedItem);
                
                // Get section data
                const sectionId = clickedItem.dataset.section;
                const targetSection = this.content.querySelector(`#${sectionId}`);
                
                if (targetSection) {
                    // Temporarily disable scroll spy during programmatic scroll
                    this.isScrolling = true;
                    
                    // Calculate offset for better positioning
                    const containerTop = this.container.getBoundingClientRect().top + window.pageYOffset;
                    const sectionTop = targetSection.getBoundingClientRect().top + window.pageYOffset;
                    const offset = sectionTop - containerTop - 100; // 100px offset from top
                    
                    window.scrollTo({
                        top: containerTop + offset,
                        behavior: 'smooth'
                    });
                    
                    // Re-enable scroll spy after scrolling completes
                    setTimeout(() => {
                        this.isScrolling = false;
                    }, 1000);
                }
                
                // Close sidebar on mobile after selection
                if (window.innerWidth <= 768) {
                    this.closeSidebar();
                }
            }

            openSidebar() {
                this.sidebar.classList.add('cdsTYDashboard-integrated-active');
                this.sidebarOverlay.classList.add('cdsTYDashboard-integrated-active');
                document.body.style.overflow = 'hidden';
            }

            closeSidebar() {
                this.sidebar.classList.remove('cdsTYDashboard-integrated-active');
                this.sidebarOverlay.classList.remove('cdsTYDashboard-integrated-active');
                document.body.style.overflow = '';
            }
        }

        class GlobalMenuToggle {
            constructor() {
                this.menuToggle = document.querySelector('.cdsTYDashboard-integrated-menu-toggle');
                this.containers = document.querySelectorAll('.cdsTYDashboard-integrated-container-component');
                this.currentContainer = null;
                
                this.init();
            }

            init() {
                if (this.menuToggle) {
                    this.menuToggle.addEventListener('click', () => this.toggleCurrentSidebar());
                }

                // Update menu toggle position based on scroll
                window.addEventListener('scroll', () => this.updateMenuPosition());
                
                // Close sidebars on window resize
                window.addEventListener('resize', () => {
                    if (window.innerWidth > 768) {
                        this.closeAllSidebars();
                    }
                });
            }

            toggleCurrentSidebar() {
                const currentContainer = this.getCurrentContainer();
                if (currentContainer) {
                    const sidebar = currentContainer.querySelector('.cdsTYDashboard-integrated-sidebar');
                    const overlay = currentContainer.querySelector('.cdsTYDashboard-integrated-sidebar-overlay');
                    
                    if (sidebar.classList.contains('cdsTYDashboard-integrated-active')) {
                        sidebar.classList.remove('cdsTYDashboard-integrated-active');
                        overlay.classList.remove('cdsTYDashboard-integrated-active');
                        document.body.style.overflow = '';
                        this.menuToggle.classList.remove('cdsTYDashboard-integrated-active');
                    } else {
                        this.closeAllSidebars();
                        sidebar.classList.add('cdsTYDashboard-integrated-active');
                        overlay.classList.add('cdsTYDashboard-integrated-active');
                        document.body.style.overflow = 'hidden';
                        this.menuToggle.classList.add('cdsTYDashboard-integrated-active');
                    }
                }
            }

            getCurrentContainer() {
                const scrollPos = window.scrollY + 100;
                
                for (let container of this.containers) {
                    const rect = container.getBoundingClientRect();
                    const top = rect.top + window.scrollY;
                    const bottom = top + rect.height;
                    
                    if (scrollPos >= top && scrollPos < bottom) {
                        return container;
                    }
                }
                
                return this.containers[0];
            }

            closeAllSidebars() {
                this.containers.forEach(container => {
                    const sidebar = container.querySelector('.cdsTYDashboard-integrated-sidebar');
                    const overlay = container.querySelector('.cdsTYDashboard-integrated-sidebar-overlay');
                    sidebar.classList.remove('cdsTYDashboard-integrated-active');
                    overlay.classList.remove('cdsTYDashboard-integrated-active');
                });
                document.body.style.overflow = '';
                this.menuToggle.classList.remove('cdsTYDashboard-integrated-active');
            }

            updateMenuPosition() {
                // Optional: Add logic to update menu button appearance based on current section
            }
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize each container
            const containers = document.querySelectorAll('.cdsTYDashboard-integrated-container-component');
            containers.forEach(container => {
                new CollapsibleContainer(container);
            });

            // Initialize global menu toggle
            new GlobalMenuToggle();
        });
    </script>

@include('admin-panel.dashboard-tabs.common.dashboard-scripts')

<script>
// Animated counter for summary cards
function animateCounter(element, target, duration = 1500) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const updateCounter = () => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString();
        } else {
            element.textContent = Math.floor(current).toLocaleString();
            requestAnimationFrame(updateCounter);
        }
    };
    
    updateCounter();
}

// Handle Card Click
function handleCardClick(type) {
    console.log(`Card clicked: ${type}`);
    switch(type) {
        case 'cases':
            window.location.href = '{{ baseUrl("/dashboard/cases") }}';
            break;
        case 'appointments':
            window.location.href = '{{ baseUrl("/dashboard/appointments") }}';
            break;
        case 'messages':
            window.location.href = '{{ baseUrl("/dashboard/messages") }}';
            break;
        case 'invoices':
            window.location.href = '{{ baseUrl("/dashboard/invoices") }}';
            break;
    }
}

// Handle Action Buttons
function handleAction(action) {
    console.log(`Action triggered: ${action}`);
    switch(action) {
        case 'add-case':
            window.location.href = '{{ baseUrl("/dashboard/cases/create") }}';
            break;
        case 'book-appointment':
            window.location.href = '{{ baseUrl("/dashboard/appointments/create") }}';
            break;
        case 'send-invoice':
            window.location.href = '{{ baseUrl("/dashboard/invoices/create") }}';
            break;
        case 'add-staff':
            window.location.href = '{{ baseUrl("/dashboard/staff/create") }}';
            break;
    }
}

// Handle Row Click
function handleRowClick(type, id) {
    console.log(`${type} clicked: ${id}`);
    if (type === 'case') {
        window.location.href = `{{ baseUrl("/cases/view/") }}/${id}`;
    } else if (type === 'invoice') {
        window.location.href = `{{ baseUrl("/invoices/edit/") }}/${id}`;
    }
}

// Handle Points Click
function handlePointsClick() {
    alert('You have earned {{ number_format($userPoints ?? 0) }} points! 🎉\nRedeem them for rewards.');
}

// Initialize counters on page load
document.addEventListener('DOMContentLoaded', () => {
    // Animate counters
    const counters = document.querySelectorAll('[data-count]');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        setTimeout(() => {
            animateCounter(counter, target);
        }, 200);
    });
});
</script> <script>
        function alignHeaderWithContent() {
            const container = document.querySelector('.CDSPostCaseNotifications-compact-list-container');
            const firstCaseItem = document.querySelector('.CDSPostCaseNotifications-compact-list-case-item');
            const header = document.querySelector('.CDSPostCaseNotifications-compact-list-header');
            const headerItems = document.querySelectorAll('.CDSPostCaseNotifications-compact-list-header-item');
            
            if (!firstCaseItem || !header || headerItems.length === 0) return;
            
            // Only align on desktop view
            if (window.innerWidth <= 1024) {
                headerItems.forEach(item => {
                    item.style.width = '';
                    item.style.flex = '';
                });
                return;
            }
            
            // Get all the main sections in the case item
            const sections = [
                firstCaseItem.querySelector('.CDSPostCaseNotifications-compact-list-case-details'),
                firstCaseItem.querySelector('.CDSPostCaseNotifications-compact-list-status'),
                firstCaseItem.querySelector('.CDSPostCaseNotifications-compact-list-client'),
                firstCaseItem.querySelector('.CDSPostCaseNotifications-compact-list-proposals'),
                firstCaseItem.querySelector('.CDSPostCaseNotifications-compact-list-actions')
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
    </script><script>
        function alignHeaderWithContent() {
            const container = document.querySelector('.CdsDashboardAppoinment-compact-list-container');
            const firstAppointmentItem = document.querySelector('.CdsDashboardAppoinment-compact-list-appointment-item');
            const header = document.querySelector('.CdsDashboardAppoinment-compact-list-header');
            const headerItems = document.querySelectorAll('.CdsDashboardAppoinment-compact-list-header-item');
            
            if (!firstAppointmentItem || !header || headerItems.length === 0) return;
            
            // Only align on desktop view
            if (window.innerWidth <= 1024) {
                headerItems.forEach(item => {
                    item.style.width = '';
                    item.style.flex = '';
                });
                return;
            }
            
            // Get all the main sections in the appointment item
            const sections = [
               
                firstAppointmentItem.querySelector('.CdsDashboardAppoinment-compact-list-client-cell'),
               
                firstAppointmentItem.querySelector('.CdsDashboardAppoinment-compact-list-service-cell'),
                firstAppointmentItem.querySelector('.CdsDashboardAppoinment-compact-list-datetime-cell'),
                firstAppointmentItem.querySelector('.CdsDashboardAppoinment-compact-list-status-cell'),
                firstAppointmentItem.querySelector('.CdsDashboardAppoinment-compact-list-payment-cell'),
                firstAppointmentItem.querySelector('.CdsDashboardAppoinment-compact-list-actions-cell')
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
        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'overview'; 
        
            function switchToTab(tab) {
                $('.cdsTYDashboard-main-tab-nav .cdsTYDashboard-main-tab-item').removeClass('cdsTYDashboard-main-active');
                $('.tab-content').addClass('d-none');
                $('.cdsTYDashboard-main-tab-item[data-tab="' + tab + '"]').addClass('cdsTYDashboard-main-active');
                $('#' + tab).removeClass('d-none');
            }

            switchToTab(activeTab);

            $('.cdsTYDashboard-main-tab-item').on('click', function (e) {
                e.preventDefault();
                const tab = $(this).data('tab');
                switchToTab(tab);

                const url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            });
        });
    </script>
@endsection
