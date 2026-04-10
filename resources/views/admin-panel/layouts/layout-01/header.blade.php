<header class="CDSCTNav-navbar">
    <div class="CDSCTNav-container">
        <div class="CDSMainNavigation-logo">
            <a class="navbar-brand" href="{{url('/')}}/">
                <img src="{{url('/')}}/assets/images/logo-c.png" alt="Logo" class="img-fluid logo-img">
            </a></div>

       <div class="CDSCTNav-icons-wrapper">
            <!-- Burger -->
            <button id="burgerMenu" aria-label="Toggle navigation" aria-controls="mainNav" aria-expanded="false">
                <svg width="24" height="24" fill="#333" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 6h18M3 12h18M3 18h18" stroke="#333" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
 <button class="cdsTYDashboard-main-header-btn" >
                <span>💬</span>
                <span class="cdsTYDashboard-main-notification-count">{{ $unreadMessages ?? 0 }}</span>
            </button>
            <!-- Notification -->
            <div id="notificationTrigger" class="cdsTYDashboard-main-header-btn" title="Notifications">
                🔔
                <span class="cdsTYDashboard-main-notification-count" id="badgeCount"
                    style="display: inline-flex !important; ">{{getChatNotification()->where('is_read',0)->count()}}</span>

            </div>
            @if(auth()->check())
            <!-- Profile -->
            <div id="profileTrigger" class="CDSCTNav-image-button" title="Profile">
                {!! getProfileImage(auth()->user()->unique_id) !!} 
            </div>
            @endif

        </div>

    </div>
</header>

<!-- Floating Panel -->
<div id="floatingPanel" class="CDSCTNav-panel">
    <div class="CDSCTNav-close-btn" id="panelCloseBtn">×</div>
    <div class="CDSCTNav-tab-header">
        <button id="tabNotifications" class="active">Notifications</button>
        <button id="tabSettings">Settings</button>
    </div>
    <div class="CDSCTNav-tab-content">@if(auth()->check())
        <div id="panelNotifications" class="CDSCTNav-tab-pane active">
            @include("components.notification") <a href="{{baseUrl('/notifications')}}"
                class="btn btn-sm btn-primary">View All</a>
        </div>
        <div id="panelSettings" class="CDSCTNav-tab-pane">

            <!-- Profile Panel Slide-In -->
            <div class="CDSMainNavigation-profilePanel-wrap">


                <p><strong>Welcome {{auth()->user()->first_name." ".auth()->user()->last_name}}</strong></p>
                @if(auth()->check() && auth()->user()->role == 'supporter')
                <p><a href="{{ baseUrl('/') }}" class="logout-link">Logout</a></p>
                @endif
                <p><a
                        href="@if(auth()->user()->role=='client'){{ baseUrl('profile')}}@else{{ baseUrl('profile')}}@endif">Edit
                        Profile</a></p>
                <p><a href="{{ baseUrl('change-password/'. auth()->user()->unique_id)}}">Change Password</a></p>
                <p><a href="javascript:;" class="logout-link">Logout</a></p>
            </div>
        </div>@else Nothing here @endif
    </div>
</div>

<!-- Backdrop -->
<div id="backdropOverlay" class="CDSCTNav-backdrop-overlay"></div>
