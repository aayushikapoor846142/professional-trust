<link rel="stylesheet" href="{{ asset('assets/css/27-CDS-page-submenu.css') }}">
@if(!empty($sub_menus))
<!-- Title Section -->
@php
/*
$page_arr = [
'page_title' => 'Groups Listing',
'page_description' => 'Navigate through the available options below',
'page_type' => 'group-list',
];
*/
@endphp
<section class="CDSDashboardSubmenu-title-section">
    <div class="CDSDashboardSubmenu-title-container">
        <div class="CDSDashboardSubmenu-title-content">
            <h1>
                @if(isset($page_arr['page_title']) && !empty($page_arr['page_title']))
                {{ $page_arr['page_title'] }}
                @else
                {{ $parentTitle }}
                @endif
            </h1>
            @if(isset($page_arr['page_description']))
            <p>{!! $page_arr['page_description'] !!}</p>
            @endif
        </div>
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'group-listings')
        @if(isset($page_arr['canCreateGroup']) && $page_arr['canCreateGroup'])
        <div class="CDSDashboardSubmenu-links">
            <button class="CdsTYButton-btn-primary"
                onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('group/add-new-group') ?>">
                <i class="fa-solid fa-plus"></i> Create Group
            </button>
        </div>
        @endif
        @endif
        
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'forms-management')
        @if(isset($page_arr['canCreateForm']) && $page_arr['canCreateForm'])
        <div class="CDSDashboardSubmenu-links">
            <button class="CdsTYButton-btn-primary"
                onclick="window.location.href='<?php echo baseUrl('forms/add') ?>'">
                <i class="fa-solid fa-plus"></i> Create New Form
            </button>
        </div>
        @endif
        @if(isset($page_arr['canGenerateAI']) && $page_arr['canGenerateAI'])
        <div class="CDSDashboardSubmenu-links">
            <button class="CdsTYButton-btn-primary"
                onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('forms/generate-via-ai') ?>">
                <i class="fa-solid fa-robot"></i> Generate Via AI
            </button>
        </div>
        @endif
        @if(isset($page_arr['canUseTemplates']) && $page_arr['canUseTemplates'])
        <div class="CDSDashboardSubmenu-links">
            <a class="CdsTYButton-btn-primary" href="<?php echo baseUrl('forms/predefined-templates') ?>">
                <i class="fa-solid fa-layer-group"></i> Predefined Templates
            </a>
        </div>
        @endif
        @endif

        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'message-center')
        <div class="CDSDashboardSubmenu-links">
            <button class="CdsTYButton-btn-primary"
                onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('individual-chats/compose-message') ?>">+ Compose
                Message</button>
        </div>
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'invite-centre')
        <div class="CDSDashboardSubmenu-links">
            <a href="javascript:;" onclick="showRightSlidePanel(this)"
                data-href="{{ baseUrl('connections/invitations/add') }}" class="CdsTYButton-btn-primary">
                <i class="fa-paper-plane fa-regular me-1"></i>
                Send Invite
            </a>
        </div>
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'group-messages')
        <div class="CDSDashboardSubmenu-links">
            <a class="CdsTYButton-btn-primary" href="javascript:;"
                onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('group/add-new-group') ?>">
                + Create Group
            </a>
        </div>
        @endif

        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'discussion-board-details')

        <div class="CDSDashboardSubmenu-links">
            <a class="CdsTYButton-btn-secondary" href="{{ baseUrl('manage-discussion-threads')}}">
                <i class="fa-solid fa-arrow-left"></i> back
            </a>
        </div>
        @endif
        
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'support-tickets')

        <div class="CDSDashboardSubmenu-links">
            <button type="button" onclick="openCustomPopup(this)" data-href="{{ route('panel.tickets.create-modal') }}"
                class="CdsTicket-btn CdsTYButton-btn-primary" @if($page_arr['ticket']==5) disabled @endif>
                <i class="fas fa-plus"></i> Raise New Ticket
            </button>
        </div>
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'global-invoices')

        <div class="CDSDashboardSubmenu-links">


            @if(checkPrivilege([ 'route_prefix' => 'panel.invoices', 'module' => 'professional-invoices', 'action' =>
            'add' ]))
            <a href="{{ baseUrl('invoices/add') }}" class="CdsTYButton-btn-primary">
                <i class="fa-solid fa-plus"></i>
                Add New
            </a>
            @endif
        </div>
        @endif



        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'feeds-details')

        <div class="CDSDashboardSubmenu-links">
            <a class="CdsTYButton-btn-secondary" href="javascript:;">
                <i class="fa-solid fa-arrow-left"></i> back
            </a>
        </div>
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'quick-case')
        <div class="CDSDashboardSubmenu-links">
            <a class="CdsTYButton-btn-primary" href="{{ baseUrl('cases/post-case') }}">+ Post Case</a>
        </div>
        @endif
     

        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'discussion-board')
        @if(isset($page_arr['canAddThread']) && $page_arr['canAddThread'])

        <div class="CDSDashboardSubmenu-links">
            <a onclick="openCustomPopup(this)" data-href="{{baseUrl('manage-discussion-threads/add/thread/modal')}}" href="javascript:;" class="CdsTYButton-btn-primary">
                ✨ New Thread
            </a>
        </div>

        @endif
        @endif

        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'feeds')
        @if(isset($page_arr['canAddFeed']) && $page_arr['canAddFeed'])  
        <div class="CDSDashboardSubmenu-links">
            <a href="javascript:;" onclick="openCustomPopup(this)" data-href="{{ baseUrl('my-feeds/add-new-feed') }}" class="CdsTYButton-btn-primary">
                + Create Feed</a>
            </a>
        </div>
        @endif
        @endif

        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'roles')
        @if(isset($page_arr['canAddRoles']) && $page_arr['canAddRoles'])  
        <div class="CDSDashboardSubmenu-links">
           <a onclick="openCustomPopup(this)" data-href="{{ baseUrl('roles/add') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-plus fa-solid me-1"></i>
                        Add New
                    </a>
        </div>
        @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'roles-previleges')
        
                    <a href="{{ baseUrl('/') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1" aria-hidden="true"></i>
                        Back
                    </a>
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'active-staff')
                    @if(isset($page_arr['canAddStaff']) && $page_arr['canAddStaff'])  
                        <a href="{{ baseUrl('staff/add') }}" class="CdsTYButton-btn-primary">
                            <i class="fa-plus fa-solid me-1"></i>
                            Add New
                            @if(isset($page_arr['staffFeatureStatus']['remaining']) && $page_arr['staffFeatureStatus']['remaining'] != -1)
                                ({{ $page_arr['staffFeatureStatus']['remaining'] }} left)
                            @endif
                        </a>
                    @else
                        <button class="btn btn-secondary" disabled title="{{ $page_arr['staffFeatureStatus']['message'] ?? 'Upgrade required' }}">
                            <i class="fa-plus fa-solid me-1"></i>
                            Add New (Upgrade Required)
                    </button>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'cases-overview')
                    @if(isset($page_arr['canViewCases']) && $page_arr['canViewCases'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('cases') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-left fa-solid me-1"></i>
                                Back
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['casesFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-left fa-solid me-1"></i>
                                Back (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'cases')
                    @if(isset($page_arr['canViewCases']) && $page_arr['canViewCases'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('cases/post-case') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-plus fa-solid me-1"></i>
                                Post New Case
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['casesFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-plus fa-solid me-1"></i>
                                Post New Case (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'case-with-professionals')
                    @if(isset($page_arr['canViewCases']) && $page_arr['canViewCases'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('case-with-professionals/add-request') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-plus fa-solid me-1"></i>
                                Add New Request
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['casesFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-plus fa-solid me-1"></i>
                                Add New Request (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'add-predefined-case-stage')
                    @if(isset($page_arr['canAddPredefinedCaseStages']) && $page_arr['canAddPredefinedCaseStages'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('predefined-case-stages') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-left fa-solid me-1"></i>
                                Back
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['predefinedCaseStagesFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-left fa-solid me-1"></i>
                                Back (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'predefined-case-stages')
                    @if(isset($page_arr['canAddPredefinedCaseStages']) && $page_arr['canAddPredefinedCaseStages'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('predefined-case-stages/add') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-plus fa-solid me-1"></i>
                                Add New
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['predefinedCaseStagesFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-plus fa-solid me-1"></i>
                                Add New (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'document-folders')
                    @if(isset($page_arr['canAddDocumentFolders']) && $page_arr['canAddDocumentFolders'])
                        <div class="CDSDashboardSubmenu-links">
                            <a onclick="openCustomPopup(this)" data-href="{{ baseUrl('document-folders/add') }}"  class="CdsTYButton-btn-primary">
                                <i class="fa-plus fa-solid me-1"></i>
                                Add New
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['documentFoldersFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-plus fa-solid me-1"></i>
                                Add New (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'add-document-folder')
                    @if(isset($page_arr['canAddDocumentFolders']) && $page_arr['canAddDocumentFolders'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('document-folders') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-left fa-solid me-1"></i>
                                Back
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['documentFoldersFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-left fa-solid me-1"></i>
                                Back (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'edit-predefined-case-stage')
                    @if(isset($page_arr['canEditPredefinedCaseStages']) && $page_arr['canEditPredefinedCaseStages'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('predefined-case-stages') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-left fa-solid me-1"></i>
                                Back
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['predefinedCaseStagesFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-left fa-solid me-1"></i>
                                Back (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'edit-document-folder')
                    @if(isset($page_arr['canEditDocumentFolders']) && $page_arr['canEditDocumentFolders'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('document-folders') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-left fa-solid me-1"></i>
                                Back
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['documentFoldersFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-left fa-solid me-1"></i>
                                Back (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'appointment-booking-flow')
                    @if(isset($page_arr['canAddAppointmentBookingFlow']) && $page_arr['canAddAppointmentBookingFlow'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('appointments/appointment-booking-flow/add') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-plus fa-solid me-1"></i>
                                Add New
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['appointmentBookingFlowFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-plus fa-solid me-1"></i>
                                Add New (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'add-appointment-booking-flow')
                    @if(isset($page_arr['canAddAppointmentBookingFlow']) && $page_arr['canAddAppointmentBookingFlow'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('appointments/appointment-booking-flow') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-left fa-solid me-1"></i>
                                Back
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['appointmentBookingFlowFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-left fa-solid me-1"></i>
                                Back (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'appointment-calendar')
                    @if(isset($page_arr['canViewAppointments']) && $page_arr['canViewAppointments'])
                        @if(isset($page_arr['canViewBlockDates']) && $page_arr['canViewBlockDates'])
                            <div class="CDSDashboardSubmenu-links">
                                <a href="{{ baseUrl('appointments/block-dates/add') }}" class="CdsTYButton-btn-primary">
                                    <i class="fa-plus fa-solid me-1"></i>
                                    Add Block Date
                                </a>
                            </div>
                        @else
                            <div class="CDSDashboardSubmenu-links">
                                <button class="btn btn-secondary" disabled title="Block dates access restricted">
                                    <i class="fa-plus fa-solid me-1"></i>
                                    Add Block Date (Access Restricted)
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['appointmentsFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-plus fa-solid me-1"></i>
                                Add Block Date (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'add-block-date')
                    @if(isset($page_arr['canAddBlockDates']) && $page_arr['canAddBlockDates'])
                        <div class="CDSDashboardSubmenu-links">
                            <a href="{{ baseUrl('appointments/appointment-booking/calendar') }}" class="CdsTYButton-btn-primary">
                                <i class="fa-left fa-solid me-1"></i>
                                Back
                            </a>
                        </div>
                    @else
                        <div class="CDSDashboardSubmenu-links">
                            <button class="btn btn-secondary" disabled title="{{ $page_arr['appointmentsFeatureStatus']['message'] ?? 'Access restricted' }}">
                                <i class="fa-left fa-solid me-1"></i>
                                Back (Access Restricted)
                            </button>
                        </div>
                    @endif
        @endif
        @if(isset($page_arr['page_type']) && ($page_arr['page_type'] == 'add-staff' || $page_arr['page_type'] == 'edit-staff' || $page_arr['page_type'] == 'change-password'))

        <a href="{{ baseUrl('staff') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                    @endif

     
    </div>
</section>

<!-- Placeholder for sticky submenu (MOVED BEFORE the actual menu) -->
<div class="CDSDashboardSubmenu-submenu-placeholder" id="submenuPlaceholder"></div>
@php
$show_submenu = false;
$active_submenu = '';
foreach($sub_menus as $sub_menu){
if(isset($sub_menu['sub_submenu'])){
if(isset($sub_menu['url']) && request()->url() == $sub_menu['url']){
$show_submenu = true;
}
foreach($sub_menu['sub_submenu'] as $sub_sub_menu){
if(isset($sub_sub_menu['url']) && request()->url() == $sub_sub_menu['url']){
$show_submenu = true;
$active_submenu = $sub_menu['title'];
break;
}
}
}
}
@endphp
<!-- Submenu Wrapper -->
<div class="CDSDashboardSubmenu-wrapper" id="submenuWrapper">
    <div class="CDSDashboardSubmenu-submenu-container">
        <!-- Active Menu Label (Mobile) -->
        <div class="CDSDashboardSubmenu-active-label" id="activeLabel">
            @php
            $activeMenu = collect($sub_menus)->first(function($menu) {
            // First try to match by route name
            if (isset($menu['route']) && Route::currentRouteName() == $menu['route']) {
            return true;
            }
            // Fallback: check if current URL matches menu URL
            if (isset($menu['url']) && request()->url() == $menu['url']) {
            return true;
            }
            // Additional fallback: check if current URL contains menu URL path
            if (isset($menu['url']) && str_contains(request()->url(), parse_url($menu['url'], PHP_URL_PATH))) {
            return true;
            }
            return false;
            });
            //echo $activeMenu ? $activeMenu['title'] : $sub_menus[0]['title'] ?? '';
            @endphp
        </div>

        <!-- Submenu -->
        <div class="CDSDashboardSubmenu-submenu" id="submenu">
            @foreach($sub_menus as $sub_menu)
            @php
            $isActive = false;
            // Check if this menu item is active
            if (isset($sub_menu['route']) && Route::currentRouteName() == $sub_menu['route']) {
            $isActive = true;
            } elseif (isset($sub_menu['url']) && request()->url() == $sub_menu['url']) {
            $isActive = true;
            } elseif (isset($sub_menu['url']) && str_contains(request()->url(), parse_url($sub_menu['url'],
            PHP_URL_PATH))) {
            $isActive = true;
            }elseif($active_submenu == $sub_menu['title']){
            $isActive = true;
            }
            @endphp

            <a href="{{ $sub_menu['url'] }}"
                class="CDSDashboardSubmenu-submenu-item {{ $isActive ? 'active' : '' }} {{ isset($sub_menu['menu-type']) && $sub_menu['menu-type'] == 'external_sub_menu' ? 'external-submenu' : '' }}"
                data-route="{{ $sub_menu['route'] ?? '' }}" data-url="{{ $sub_menu['url'] }}"
                data-menu-title="{{ $sub_menu['title'] }}"
                onclick="setActive(this, '{{ $sub_menu['title'] }}', '{{ $sub_menu['url'] }}')">
                @if(isset($sub_menu['icon']))
                <span class="submenu-icon">
                    <!-- <img src="{{ url('assets/images/menu-icons/'.$sub_menu['icon']) }}" width="20" alt="{{ $sub_menu['title'] }}"> -->
                </span>
                @endif
                <span class="submenu-text">{!! $sub_menu['title'] !!}</span>
            </a>
            @endforeach
        </div>

        <!-- Burger Button (Mobile) -->
        <button class="CDSDashboardSubmenu-burger-btn" id="burgerBtn" onclick="toggleMobileMenu()">
            <div class="CDSDashboardSubmenu-burger-icon">
                <span class="CDSDashboardSubmenu-burger-line"></span>
                <span class="CDSDashboardSubmenu-burger-line"></span>
                <span class="CDSDashboardSubmenu-burger-line"></span>
            </div>
        </button>
    </div>
</div>


@foreach($sub_menus as $sub_menu)
@if(isset($sub_menu['sub_submenu']))
@if($show_submenu)
<div class="CDSDashboardSubmenu-submenu-list" id="submenu2">
    <div class="CDSDashboardSubmenu-submenu-item-sub">
        <div class="CDSDashboardSubmenu-submenu-item-sub-content">
            @foreach($sub_menu['sub_submenu'] as $sub_sub_menu)
            <a href="{{ $sub_sub_menu['url'] }}"
                class="CDSDashboardSubmenu-submenu-item-sub-item {{ request()->url() == $sub_sub_menu['url'] ? 'active' : '' }}">
                @if(isset($sub_sub_menu['icon']))
                <span class="submenu-icon">
                    {{ $sub_sub_menu['icon'] }}
                </span>
                @endif
                {{ $sub_sub_menu['title'] }}
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif
@endif
@endforeach

<!-- Mobile Overlay -->
<div class="CDSDashboardSubmenu-mobile-overlay" id="mobileOverlay" onclick="closeMobileMenu()"></div>


<!-- JavaScript for functionality -->
<script>
    // ============================
    // ELEMENTS
    // ============================
    const submenuWrapper = document.getElementById('submenuWrapper');
    const submenuPlaceholder = document.getElementById('submenuPlaceholder');
    const burgerBtn = document.getElementById('burgerBtn');
    const submenu = document.getElementById('submenu');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const activeLabel = document.getElementById('activeLabel');

    // ============================
    // ACTIVE STATE MANAGEMENT
    // ============================
    function updateActiveState() {
        const currentUrl = window.location.href;
        const currentPath = window.location.pathname;
        var active_submenu = "{{ $active_submenu }}";
        // Remove active class from all items
        const items = document.querySelectorAll('.CDSDashboardSubmenu-submenu-item');
        items.forEach(item => item.classList.remove('active'));

        // Find and activate the current page's menu item
        let activeItem = null;
        items.forEach(item => {
            const itemUrl = item.getAttribute('href');
            const itemRoute = item.getAttribute('data-route');
            const menuTitle = item.getAttribute('data-menu-title');
            // Check if this item matches the current page
            if (itemUrl === currentUrl ||
                itemUrl === currentPath ||
                // currentUrl.includes(itemUrl) ||
                (itemRoute && window.location.pathname.includes(itemRoute))) {
                activeItem = item;
                item.classList.add('active');
            } else if (menuTitle == active_submenu) {
                activeItem = item;
                item.classList.add('active');
            }
        });

        // Update mobile active label
        if (activeItem) {
            const label = activeItem.querySelector('.submenu-text').textContent.trim();
            activeLabel.textContent = label;
        }
    }

    // ============================
    // STICKY SUBMENU FUNCTIONALITY - COMPLETELY REVISED
    // ============================
    let stickyOffset = null;
    let menuHeight = 0;
    let headerHeight = 55; // Adjust this if your header has different height
    let isStickyActive = false;

    // Function to get the absolute offset of an element
    function getAbsoluteOffset(element) {
        let top = 0;
        let el = element;
        while (el && el !== document.body) {
            top += el.offsetTop;
            el = el.offsetParent;
        }
        return top;
    }

    // Initialize sticky offset
    function initializeStickyOffset() {
        if (submenuWrapper && !isStickyActive) {
            // Get the original position when menu is not sticky
            stickyOffset = getAbsoluteOffset(submenuWrapper);
            menuHeight = submenuWrapper.offsetHeight;

            // Store original position as data attribute for reference
            submenuWrapper.setAttribute('data-original-offset', stickyOffset);

            console.log('Sticky offset initialized:', stickyOffset); // Debug log
        }
    }

    // Handle sticky behavior
    function handleStickyMenu() {
        if (!submenuWrapper || stickyOffset === null) return;

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const triggerPoint = stickyOffset - headerHeight;

        // Debug logging (remove in production)
        // console.log('Scroll:', scrollTop, 'Trigger:', triggerPoint);

        if (scrollTop > triggerPoint) {
            // Should be sticky
            if (!isStickyActive) {
                submenuWrapper.classList.add('sticky');
                submenuPlaceholder.style.height = menuHeight + 'px';
                submenuPlaceholder.style.display = 'block';
                isStickyActive = true;

                // Ensure sticky positioning
                submenuWrapper.style.position = 'fixed';
                submenuWrapper.style.top = headerHeight + 'px';
                submenuWrapper.style.left = '0';
                submenuWrapper.style.right = '0';
                submenuWrapper.style.zIndex = '999';
            }
        } else {
            // Should not be sticky
            if (isStickyActive) {
                submenuWrapper.classList.remove('sticky');
                submenuPlaceholder.style.height = '0';
                submenuPlaceholder.style.display = 'none';
                isStickyActive = false;

                // Remove inline styles
                submenuWrapper.style.position = '';
                submenuWrapper.style.top = '';
                submenuWrapper.style.left = '';
                submenuWrapper.style.right = '';
                submenuWrapper.style.zIndex = '';
            }
        }
    }

    // Debounce function for scroll performance
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

    // Optimized scroll handler
    var handleScroll = debounce(handleStickyMenu, 10);

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function () {
        // Small delay to ensure all styles are applied
        setTimeout(() => {
            initializeStickyOffset();
            updateActiveState();
            closeMobileMenu();
            handleStickyMenu(); // Check initial scroll position
        }, 100);
    });

    // Also initialize on window load (for images and fonts)
    window.addEventListener('load', function () {
        if (stickyOffset === null) {
            initializeStickyOffset();
        }
        handleStickyMenu();
    });

    // Attach scroll listener
    window.addEventListener('scroll', handleScroll, {
        passive: true
    });

    // Recalculate on resize
    window.addEventListener('resize', debounce(function () {
        if (!isStickyActive) {
            initializeStickyOffset();
        }
        handleStickyMenu();
    }, 250));

    // ============================
    // MOBILE MENU FUNCTIONALITY
    // ============================
    function toggleMobileMenu() {
        const isOpen = submenu.classList.contains('open');

        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    function openMobileMenu() {
        submenu.classList.add('open');
        burgerBtn.classList.add('active');
        mobileOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling when menu is open
    }

    function closeMobileMenu() {
        submenu.classList.remove('open');
        burgerBtn.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }

    // ============================
    // SUBMENU ITEM SELECTION
    // ============================
    function setActive(element, label, url) {
        // Remove active class from all items
        const items = document.querySelectorAll('.CDSDashboardSubmenu-submenu-item');
        items.forEach(item => item.classList.remove('active'));

        // Add active class to clicked item
        element.classList.add('active');

        // Update active label for mobile
        activeLabel.textContent = label;

        // Close mobile menu after selection
        if (window.innerWidth <= 640) {
            closeMobileMenu();
        }

        // Store the active state in sessionStorage for persistence
        sessionStorage.setItem('activeSubmenuItem', url);
    }

    // ============================
    // RESPONSIVE HANDLER
    // ============================
    let previousWidth = window.innerWidth;

    window.addEventListener('resize', function () {
        const currentWidth = window.innerWidth;

        // Close mobile menu when resizing to desktop
        if (previousWidth <= 640 && currentWidth > 640) {
            closeMobileMenu();
        }

        previousWidth = currentWidth;
    });

    // Listen for navigation changes (for SPA-like behavior)
    window.addEventListener('popstate', updateActiveState);

    // Update active state when URL changes without page reload
    let currentUrl = window.location.href;
    new MutationObserver(() => {
        if (window.location.href !== currentUrl) {
            currentUrl = window.location.href;
            setTimeout(updateActiveState, 100); // Small delay to ensure DOM is updated
        }
    }).observe(document, {
        subtree: true,
        childList: true
    });

    // ============================
    // DEBUG HELPER (Remove in production)
    // ============================
    // Add this to check values in console
    window.debugSticky = function () {
        console.log({
            stickyOffset: stickyOffset,
            currentScroll: window.pageYOffset,
            isSticky: isStickyActive,
            menuWrapper: submenuWrapper,
            placeholder: submenuPlaceholder
        });
    };

</script>

<!-- Additional inline styles to ensure sticky behavior -->
<style>
    /* Ensure placeholder has no default styling that could interfere */
    .CDSDashboardSubmenu-submenu-placeholder {
        display: none;
        width: 100%;
    }

</style>
@else
@include('components.page-horizontal-main-menu')
@endif
