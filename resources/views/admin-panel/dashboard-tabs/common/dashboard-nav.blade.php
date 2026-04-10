<!-- Dashboard CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/42-CDS-dashboard.css') }}">

<!-- Mobile Menu Overlay -->
<div class="cdsTYDashboard-main-mobile-menu-overlay" onclick="toggleMobileMenu()"></div>

<!-- Header -->
<div class="cdsTYDashboard-main-header">
    <div class="cdsTYDashboard-main-header-top">
        <div class="cdsTYDashboard-main-logo-section">
            <div class="cdsTYDashboard-main-logo">D</div>
            <h1 class="cdsTYDashboard-main-welcome-text">Welcome, {{ auth()->user()->first_name ?? 'Staff' }}</h1>
        </div>
        <button class="cdsTYDashboard-main-mobile-menu-toggle" onclick="toggleMobileMenu()">
            <div class="cdsTYDashboard-main-hamburger"></div>
        </button>
    </div>
    <div class="cdsTYDashboard-main-tab-nav">

         <a class="cdsTYDashboard-main-tab-item {{ request('tab') == 'overview' || request('tab') == '' ? 'cdsTYDashboard-main-active' : '' }} " data-tab="overview">Overview</a>
        <a class="cdsTYDashboard-main-tab-item {{ request('tab') == 'cases' ? 'cdsTYDashboard-main-active' : '' }}" data-tab= "cases">Cases ({{getUnreadCase()}})</a>
        <a class="cdsTYDashboard-main-tab-item {{ request('tab') == 'appointments' ? 'cdsTYDashboard-main-active' : '' }}" data-tab= "appointments">Appointments</a>
        <a class="cdsTYDashboard-main-tab-item {{ request('tab') == 'messages' ? 'cdsTYDashboard-main-active' : '' }}" data-tab= "messages">Messages ({{getUnreadMessage()}})</a>
        <a class="cdsTYDashboard-main-tab-item {{ request('tab') == 'review' ? 'cdsTYDashboard-main-active' : '' }}" data-tab= "review">Review</a>
        <a class="cdsTYDashboard-main-tab-item {{ request('tab') == 'transactions' ? 'cdsTYDashboard-main-active' : '' }}" data-tab= "transactions">Transactions</a>
        <a class="cdsTYDashboard-main-tab-item {{ request('tab') == 'reports' ? 'cdsTYDashboard-main-active' : '' }}" data-tab= "reports">Reports</a>
        <!-- <a class="cdsTYDashboard-main-tab-item" data-tab= "reports">Reports</a> -->
        <!-- <a class="cdsTYDashboard-main-tab-item"  data-tab= "settings">Settings</a> -->
    </div>
</div>

<!-- Mobile Navigation -->
<div class="cdsTYDashboard-main-mobile-nav">
    <div class="cdsTYDashboard-main-mobile-nav-header">
        <div class="cdsTYDashboard-main-profile-avatar">{{ substr(auth()->user()->first_name ?? 'S', 0, 1) }}{{ substr(auth()->user()->last_name ?? 'T', 0, 1) }}</div>
        <div>
            <div style="font-weight: 600;">{{ auth()->user()->first_name ?? 'Staff' }} {{ auth()->user()->last_name ?? '' }}</div>
            <div style="font-size: 12px; color: #6b7280;">{{ auth()->user()->role ?? 'Administrator' }}</div>
        </div>
    </div>
    <div class="cdsTYDashboard-main-mobile-nav-item {{ $activeTab === 'overview' ? 'cdsTYDashboard-main-active' : '' }}" onclick="mobileNavigate('overview')">
        <span>📊</span>
        <span>Overview</span>
    </div>
    <div class="cdsTYDashboard-main-mobile-nav-item {{ $activeTab === 'cases' ? 'cdsTYDashboard-main-active' : '' }}" onclick="mobileNavigate('cases')">
        <span>📁</span>
        <span>Cases</span>
    </div>
    <div class="cdsTYDashboard-main-mobile-nav-item {{ $activeTab === 'appointments' ? 'cdsTYDashboard-main-active' : '' }}" onclick="mobileNavigate('appointments')">
        <span>📅</span>
        <span>Appointments</span>
    </div>
    <div class="cdsTYDashboard-main-mobile-nav-item {{ $activeTab === 'messages' ? 'cdsTYDashboard-main-active' : '' }}" onclick="mobileNavigate('messages')">
        <span>💬</span>
        <span>Messages ({{ $unreadMessages ?? 0 }})</span>
    </div>
    <div class="cdsTYDashboard-main-mobile-nav-item {{ $activeTab === 'invoices' ? 'cdsTYDashboard-main-active' : '' }}" onclick="mobileNavigate('invoices')">
        <span>💳</span>
        <span>Invoices</span>
    </div>
    <div class="cdsTYDashboard-main-mobile-nav-item {{ $activeTab === 'reports' ? 'cdsTYDashboard-main-active' : '' }}" onclick="mobileNavigate('reports')">
        <span>📈</span>
        <span>Reports</span>
    </div>
    <div class="cdsTYDashboard-main-mobile-nav-item {{ $activeTab === 'settings' ? 'cdsTYDashboard-main-active' : '' }}" onclick="mobileNavigate('settings')">
        <span>⚙️</span>
        <span>Settings</span>
    </div>
    <div class="cdsTYDashboard-main-mobile-nav-item" onclick="mobileNavigate('search')">
        <span>🔍</span>
        <span>Search</span>
    </div>
    <div class="cdsTYDashboard-main-mobile-nav-item" onclick="mobileNavigate('notifications')">
        <span>🔔</span>
        <span>Notifications (3)</span>
    </div>
    <div class="cdsTYDashboard-main-mobile-nav-item" onclick="mobileNavigate('logout')">
        <span>🚪</span>
        <span>Logout</span>
    </div>
</div>
