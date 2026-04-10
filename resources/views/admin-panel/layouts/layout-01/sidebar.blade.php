<link rel="stylesheet" href="{{ url('assets/css/01-CDS-sidebar-menu.css') }}">
<div class="CdsDashboardNavVertical-menu {{ getSidebarStatus() ? 'CdsDashboardNavVertical-menu--collapsed' : '' }}" id="cdsSidebar">

    <!-- Menu Content -->
    <div class="CdsDashboardNavVertical-menu__content" id="cdsSidebarContent">
        @foreach(menuItems() as $menu)
            @php
                $visibleSubmenus = [];
                if (isset($menu['submenu'])) {
                    $visibleSubmenus = array_filter($menu['submenu'], function ($submenu) {
                        // Check both role privileges and subscription features
                        $hasPrivilege = !isset($submenu['privileges']) || checkPrivilege($submenu['privileges']);
                        
                        return $hasPrivilege;
                    });
                }
                
                $isActive = request()->routeIs($menu['route'] ?? '');
                $hasActiveChild = false;
                
                if (!empty($visibleSubmenus)) {
                    foreach ($visibleSubmenus as $submenu) {
                        if (request()->routeIs($submenu['route'] ?? '')) {
                            $hasActiveChild = true;
                            break;
                        }
                    }
                }
            @endphp

            @if(!isset($menu['privileges']) || checkPrivilege($menu['privileges']))
                @if(empty($menu['submenu']) || count($visibleSubmenus) > 0)
                    <div class="CdsDashboardNavVertical-menu__item-wrapper {{ $hasActiveChild ? 'CdsDashboardNavVertical-menu__item-wrapper--expanded' : '' }}"
                         data-menu-name="{{ $menu['menu-name'] }}"
                         data-has-submenu="{{ !empty($visibleSubmenus) ? 'true' : 'false' }}">
                        
                        @if(!empty($visibleSubmenus))
                            <!-- Parent Menu Item with Submenu -->
                            <div class="CdsDashboardNavVertical-menu__item {{ $isActive || $hasActiveChild ? 'CdsDashboardNavVertical-menu__item--active' : '' }}">
                                <div class="CdsDashboardNavVertical-menu__item-content">
                                    <span class="CdsDashboardNavVertical-menu__icon">
                                        <i class="{{ $menu['icon'] }}"></i>
                                    </span>
                                    <span class="CdsDashboardNavVertical-menu__label">{{ $menu['title'] }}</span>
                                </div>
                                <span class="CdsDashboardNavVertical-menu__arrow">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </span>
                            </div>
                            
                            <!-- Submenu -->
                            <div class="CdsDashboardNavVertical-menu__submenu">
                                @foreach($visibleSubmenus as $submenu)
                                    <a href="{{ $submenu['url'] }}" 
                                       class="CdsDashboardNavVertical-menu__submenu-item {{ request()->routeIs($submenu['route'] ?? '') ? 'CdsDashboardNavVertical-menu__submenu-item--active' : '' }}">
                                        {!! $submenu['title'] !!}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <!-- Regular Menu Item -->
                            <a href="{{ $menu['url'] }}" 
                               class="CdsDashboardNavVertical-menu__item {{ $isActive ? 'CdsDashboardNavVertical-menu__item--active' : '' }}">
                                <div class="CdsDashboardNavVertical-menu__item-content">
                                    <span class="CdsDashboardNavVertical-menu__icon">
                                        <i class="{{ $menu['icon'] }}"></i>
                                    </span>
                                    <span class="CdsDashboardNavVertical-menu__label">{{ $menu['title'] }}</span>
                                </div>
                            </a>
                        @endif
                    </div>
                @endif
            @endif
        @endforeach
    </div>
    
    <!-- More Indicator -->
    <div class="CdsDashboardNavVertical-menu__more-indicator" id="cdsSidebarMoreIndicator">
        +0 more items
    </div>
    
    <!-- Toggle Button -->
    <button class="CdsDashboardNavVertical-menu__toggle" id="cdsSidebarToggle">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
</div>

<!-- Mobile Toggle Button -->
<!-- <button class="CdsDashboardNavVertical-menu__mobile-toggle" id="cdsSidebarMobileToggle">
    <i class="fa-solid fa-bars"></i>
</button> -->

<!-- Mobile Overlay -->
<div class="CdsDashboardNavVertical-menu__overlay" id="cdsSidebarOverlay"></div>

<!-- Floating Submenu -->
<div class="CdsDashboardNavVertical-menu__floating-submenu" id="cdsSidebarFloatingSubmenu"></div>


@push('scripts')

@endpush

<!-- Mobile Toggle Button -->
<!-- <button class="cds-sidebar__mobile-toggle" id="cdsSidebarMobileToggle">
    <i class="fa-solid fa-bars"></i>
</button> -->

<!-- Mobile Overlay -->
<div class="cds-sidebar__overlay" id="cdsSidebarOverlay"></div>

<!-- Floating Submenu -->
<div class="cds-sidebar__floating-submenu" id="cdsSidebarFloatingSubmenu"></div>


@push('scripts')
<script>
    // Configuration
    const CONFIG = {
        STORAGE_KEY: 'sidebar_collapsed',
        MOBILE_BREAKPOINT: 1024,
        HOVER_DELAY: 300,
        ANIMATION_DURATION: 300
    };

    // State
    let state = {
        isCollapsed: {{ getSidebarStatus() ? 'true' : 'false' }},
        isMobile: window.innerWidth <= CONFIG.MOBILE_BREAKPOINT,
        hoverTimeout: null,
        activeSubmenu: null
    };
    // DOM Elements
    let elements = {};

    // Initialize
    document.addEventListener('DOMContentLoaded', init);

    function init() {
        cacheDOMElements();
        loadSavedState();
        setupEventListeners();
        checkResponsive();
        updateMoreIndicator();
        initializeActiveStates();
    }

    // Cache DOM elements
    function cacheDOMElements() {
        elements = {
            layoutSidenav: document.getElementById('layoutSidenav_nav'),
            sidebar: document.getElementById('cdsSidebar'),
            content: document.getElementById('cdsSidebarContent'),
            moreIndicator: document.getElementById('cdsSidebarMoreIndicator'),
            toggleBtn: document.getElementById('cdsSidebarToggle'),
            mobileToggle: document.getElementById('cdsSidebarMobileToggle'),
            overlay: document.getElementById('cdsSidebarOverlay'),
            floatingSubmenu: document.getElementById('cdsSidebarFloatingSubmenu'),
            mainContainer: document.querySelector('.CDSDashboardProfessional-main-container'),
            menuWrappers: document.querySelectorAll('.CdsDashboardNavVertical-menu__item-wrapper')
        };
    }

    // Load saved state from localStorage
    function loadSavedState() {
    if (state.isMobile) return;

    // Get the initial state from your PHP variable
    state.isCollapsed = {{ getSidebarStatus() ? 'true' : 'false' }};
    
    // Apply the state
    if (state.isCollapsed) {
        elements.sidebar.classList.add('CdsDashboardNavVertical-menu--collapsed');
        if (elements.mainContainer) {
            elements.mainContainer.classList.add('sidebar-collapsed');
        }
    } else {
        elements.sidebar.classList.remove('CdsDashboardNavVertical-menu--collapsed');
        if (elements.mainContainer) {
            elements.mainContainer.classList.remove('sidebar-collapsed');
        }
    }
    
    updateToggleIcon();
}

    // Save state to localStorage
    function saveState() {
            fetch('{{ route("panel.sidebar.status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: state.isCollapsed ? 0 : 1
        })
    }).catch(err => console.error('Sidebar state DB update failed:', err));

        // localStorage.setItem(CONFIG.STORAGE_KEY, state.isCollapsed);
        
        // // Also save to server via AJAX (optional)
        // if (typeof axios !== 'undefined') {
        //     axios.post('/api/user/preferences', {
        //         sidebar_collapsed: state.isCollapsed
        //     }).catch(err => console.error('Failed to save preference:', err));
        // }
    }

    // Setup event listeners
    function setupEventListeners() {
        // Toggle button
        elements.toggleBtn?.addEventListener('click', toggleSidebar);

        // Mobile toggle
        elements.mobileToggle?.addEventListener('click', toggleMobileSidebar);

        // Overlay click
        elements.overlay?.addEventListener('click', closeMobileSidebar);

        // Menu items
        elements.menuWrappers.forEach(wrapper => {
            const hasSubmenu = wrapper.dataset.hasSubmenu === 'true';
            const menuItem = wrapper.querySelector('.CdsDashboardNavVertical-menu__item');

            if (hasSubmenu && menuItem) {
                // Click handler
                menuItem.addEventListener('click', (e) => {
                    e.preventDefault();
                    toggleSubmenu(wrapper);
                });

                // Hover handlers for collapsed state
                wrapper.addEventListener('mouseenter', () => {
                    if (state.isCollapsed && !state.isMobile) {
                        showFloatingSubmenu(wrapper);
                    }
                });

                wrapper.addEventListener('mouseleave', () => {
                    if (state.isCollapsed && !state.isMobile) {
                        hideFloatingSubmenu();
                    }
                });
            }
        });

        // Floating submenu hover
        elements.floatingSubmenu?.addEventListener('mouseenter', () => {
            clearTimeout(state.hoverTimeout);
        });

        elements.floatingSubmenu?.addEventListener('mouseleave', () => {
            hideFloatingSubmenu();
        });

        // Content scroll
        elements.content?.addEventListener('scroll', updateMoreIndicator);

        // Window resize
        window.addEventListener('resize', debounce(checkResponsive, 250));

        // More indicator click
        elements.moreIndicator?.addEventListener('click', scrollToBottom);
    }

    // Toggle sidebar collapsed state
    function toggleSidebar() {
        state.isCollapsed = !state.isCollapsed;

        elements.sidebar.classList.toggle('CdsDashboardNavVertical-menu--collapsed');
        if (elements.mainContainer) {
            elements.mainContainer.classList.toggle('sidebar-collapsed');
        }

        updateToggleIcon();
        saveState();

        // Close all expanded submenus when collapsing
        if (state.isCollapsed) {
            closeAllSubmenus();
        }
    }

    /* only work 1025px to 1299px screen size */
    function handleResponsiveSidebar() {
        const width = window.innerWidth;
        if (width >= 1025 && width <= 1299) {
            if (!state.isCollapsed) {
                state.isCollapsed = true;
                elements.sidebar.classList.add("CdsDashboardNavVertical-menu--collapsed");
                if (elements.mainContainer) {
                    elements.mainContainer.classList.add("sidebar-collapsed");
                }
                updateToggleIcon();
                saveState();
                closeAllSubmenus();
            }
        } else {
            if (state.isCollapsed) {
                state.isCollapsed = false;
                elements.sidebar.classList.remove("CdsDashboardNavVertical-menu--collapsed");
                if (elements.mainContainer) {
                    elements.mainContainer.classList.remove("sidebar-collapsed");
                }
                updateToggleIcon();
                saveState();
            }
        }
    }    /* # only work 1025px to 1299px screen size */
        // function handleResponsiveSidebar() {
        //     if (window.innerWidth >= 320 && window.innerWidth <= 767) {
        //         if (elements.sidebar) {
        //             elements.sidebar.classList.add("CdsDashboardNavVertical-menu--collapsed");
        //         }
        //     } else {
        //         if (elements.sidebar) {
        //             elements.sidebar.classList.remove("CdsDashboardNavVertical-menu--collapsed");
        //         }
        //     }
        // }


// Call on page load
handleResponsiveSidebar();

// Call on window resize
window.addEventListener('resize', handleResponsiveSidebar);


    // Update toggle button icon
    function updateToggleIcon() {
        const icon = elements.toggleBtn?.querySelector('i');
        if (icon) {
            icon.className = state.isCollapsed ? 
                'fa-solid fa-chevron-right' : 
                'fa-solid fa-chevron-left';
        }
    }

    // Toggle submenu
    function toggleSubmenu(wrapper) {
        if (state.isCollapsed && !state.isMobile) return;

        const isExpanded = wrapper.classList.contains('CdsDashboardNavVertical-menu__item-wrapper--expanded');
        
        // Close other submenus
        if (!isExpanded) {
            closeAllSubmenus();
        }

        wrapper.classList.toggle('CdsDashboardNavVertical-menu__item-wrapper--expanded');
        state.activeSubmenu = isExpanded ? null : wrapper;
    }

    // Close all submenus
    function closeAllSubmenus() {
        elements.menuWrappers.forEach(wrapper => {
            wrapper.classList.remove('CdsDashboardNavVertical-menu__item-wrapper--expanded');
        });
        state.activeSubmenu = null;
    }

    // Show floating submenu
    // function showFloatingSubmenu(wrapper) {
    //     clearTimeout(state.hoverTimeout);

    //     const menuItem = wrapper.querySelector('.CdsDashboardNavVertical-menu__item');
    //     const submenu = wrapper.querySelector('.CdsDashboardNavVertical-menu__submenu');
        
    //     if (!menuItem || !submenu) return;

    //     const rect = wrapper.getBoundingClientRect();
    //     const menuName = wrapper.dataset.menuName;
    //     const title = menuItem.querySelector('.CdsDashboardNavVertical-menu__label')?.innerHTML || '';

    //     // Build floating submenu content
    //     let html = `<div class="CdsDashboardNavVertical-menu__floating-submenu-title">${title}</div>`;
        
    //     const submenuItems = submenu.querySelectorAll('.CdsDashboardNavVertical-menu__submenu-item');
    //     submenuItems.forEach(item => {
    //         html += `<a href="${item.href}" class="${item.className}">${item.innerHTML}</a>`;
    //     });

    //     elements.floatingSubmenu.innerHTML = html;
    //     elements.floatingSubmenu.style.top = `${rect.top}px`;
    //     elements.floatingSubmenu.style.left = `${rect.right + 10}px`;
    //     elements.floatingSubmenu.style.display = 'block';
    // }
    function showFloatingSubmenu(wrapper) {
        clearTimeout(state.hoverTimeout);

        const menuItem = wrapper.querySelector('.CdsDashboardNavVertical-menu__item');
        const submenu  = wrapper.querySelector('.CdsDashboardNavVertical-menu__submenu');
        if (!menuItem || !submenu) return;

        const rect = wrapper.getBoundingClientRect();
        const title = menuItem.querySelector('.CdsDashboardNavVertical-menu__label')?.innerHTML || '';

        /* ---------- build submenu markup ---------- */
        let html = `<div class="CdsDashboardNavVertical-menu__floating-submenu-title">${title}</div>`;
        submenu.querySelectorAll('.CdsDashboardNavVertical-menu__submenu-item')
            .forEach(item => { html += `<a href="${item.href}" class="${item.className}">${item.innerHTML}</a>`; });

        elements.floatingSubmenu.innerHTML = html;
        elements.floatingSubmenu.style.display = 'block';

        /* ---------- viewport‑aware positioning ---------- */
        const vpH      = window.innerHeight;
        const subH     = elements.floatingSubmenu.offsetHeight;
        const margin   = 8;                                // keep a little breathing room
        let   top      = rect.top;                         // default: align with the hovered icon

        // If submenu would poke below the viewport…
        if (top + subH > vpH - margin) {
            top = vpH - subH - margin;                     // pull it up
        }
        // If it would now poke above the viewport (i.e. VERY tall list)…
        if (top < margin) {
            top = margin;                                  // pin to very top
            elements.floatingSubmenu.style.maxHeight = (vpH - margin * 2) + 'px';
        }

        elements.floatingSubmenu.style.top  = `${top}px`;
        elements.floatingSubmenu.style.left = `${rect.right + 10}px`;
    }


    // Hide floating submenu
    function hideFloatingSubmenu() {
        state.hoverTimeout = setTimeout(() => {
            elements.floatingSubmenu.style.display = 'none';
        }, CONFIG.HOVER_DELAY);
    }

    // Toggle mobile sidebar
    function toggleMobileSidebar() {
        const isOpen = elements.sidebar.classList.contains('CdsDashboardNavVertical-menu--mobile-open');
        
        if (isOpen) {
            closeMobileSidebar();
        } else {
            openMobileSidebar();
        }
    }

    // Open mobile sidebar
    function openMobileSidebar() {
        elements.sidebar.classList.add('CdsDashboardNavVertical-menu--mobile-open');
        elements.overlay.classList.add('CdsDashboardNavVertical-menu__overlay--active');
        updateMobileToggleIcon(true);
        document.body.style.overflow = 'hidden';
    }

    // Close mobile sidebar
    function closeMobileSidebar() {
        elements.sidebar.classList.remove('CdsDashboardNavVertical-menu--mobile-open');
        elements.overlay.classList.remove('CdsDashboardNavVertical-menu__overlay--active');
        updateMobileToggleIcon(false);
        document.body.style.overflow = '';
    }
    

    

    // Update mobile toggle icon
    function updateMobileToggleIcon(isOpen) {
        const icon = elements.mobileToggle?.querySelector('i');
        if (icon) {
            icon.className = isOpen ? 'fa-solid fa-times' : 'fa-solid fa-bars';
        }
    }

    // Update more indicator
    function updateMoreIndicator() {
        if (state.isMobile || !elements.content || !elements.moreIndicator) return;

        const items = elements.content.querySelectorAll('.CdsDashboardNavVertical-menu__item-wrapper');
        let hiddenCount = 0;
        const visibleBottom = elements.content.scrollTop + elements.content.clientHeight;

        items.forEach(item => {
            const itemBottom = item.offsetTop + item.offsetHeight;
            if (itemBottom > visibleBottom) {
                hiddenCount++;
            }
        });

        if (hiddenCount > 0) {
            elements.moreIndicator.style.display = 'block';
            elements.moreIndicator.textContent = `+${hiddenCount} more item${hiddenCount > 1 ? 's' : ''}`;
        } else {
            elements.moreIndicator.style.display = 'none';
        }
    }

    // Scroll to bottom
    function scrollToBottom() {
        if (!elements.content) return;

        elements.content.scrollTo({
            top: elements.content.scrollHeight,
            behavior: 'smooth'
        });

        setTimeout(updateMoreIndicator, CONFIG.ANIMATION_DURATION);
    }

    // Check responsive state
    function checkResponsive() {
        const wasDesktop = !state.isMobile;
        state.isMobile = window.innerWidth <= CONFIG.MOBILE_BREAKPOINT;

        if (wasDesktop && state.isMobile) {
            // Switched to mobile
            elements.sidebar.classList.remove('CdsDashboardNavVertical-menu--collapsed');
            if (elements.mainContainer) {
                elements.mainContainer.classList.remove('sidebar-collapsed');
            }
            state.isCollapsed = false;
        } else if (!wasDesktop && !state.isMobile) {
            // Switched to desktop
            closeMobileSidebar();
            loadSavedState();
        }

        updateMoreIndicator();
    }

    // Initialize active states based on current route
    function initializeActiveStates() {
        // Find active menu items and expand their parent submenus
        const activeItems = document.querySelectorAll('.CdsDashboardNavVertical-menu__item--active, .CdsDashboardNavVertical-menu__submenu-item--active');
        
        activeItems.forEach(item => {
            const wrapper = item.closest('.CdsDashboardNavVertical-menu__item-wrapper');
            if (wrapper && wrapper.dataset.hasSubmenu === 'true') {
                wrapper.classList.add('CdsDashboardNavVertical-menu__item-wrapper--expanded');
            }
        });
    }

    // Utility: Debounce function
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

    // Export for global access if needed
    window.CDSSidebar = {
        toggle: toggleSidebar,
        open: () => {
            if (state.isCollapsed) toggleSidebar();
        },
        close: () => {
            if (!state.isCollapsed) toggleSidebar();
        },
        getState: () => state
    };
</script>
<script>
    /*  close sidebar click on body section */
    if ($(window).width() <= 767) {
        $("#layoutSidenav_content").on("click", function () {
            $(".CdsDashboardNavVertical-menu--mobile-open").removeClass("CdsDashboardNavVertical-menu--mobile-open");
        });
    }
</script>
@endpush