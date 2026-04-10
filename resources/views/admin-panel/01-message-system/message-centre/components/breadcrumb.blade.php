<section class="cdsTYOnboardingDashboard-breadcrumb-section">
    <div class="cdsTYOnboardingDashboard-breadcrumb-section-header">
        <div class="cdsTYOnboardingDashboard-page-title">
            <h2>Message Center</h2>
        </div>
        <div class="breadcrumb-container">
            <ol class="breadcrumb">
                <i class="fa-grid-2 fa-regular"></i>
                <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('/') }}">Dashboard</a></li>
                <li class="active breadcrumb-item"><a class="breadcrumb-link"
                        href="{{ baseUrl('/message-centre') }}">Message Center</a></li>
            </ol>
        </div>
    </div>
    <div class="cds-dashboard-chat-main-container-header">
        @include('admin-panel.01-message-system.message-centre.invite-users')
    </div>
</section> 