<section class="CDSDashboardProfessional-main-container-body-inner-0">
    <div class="cdsTYOnboardingDashboard-chat-main-container">
        @php
        $openfor= request()->get('openfor');
        @endphp
        @include('admin-panel.01-message-system.message-centre.chat_sidebar_header_common')
        <div class="chat-container" id="chat-container">
            @include('admin-panel.01-message-system.message-centre.components.sidebar-new')
            <!-- Chat Messages -->
            <div class="message-container">
                @include("admin-panel.01-message-system.message-centre.components.message-container-new")
            </div>
        </div>
    </div>
</section> 