@extends('admin-panel.layouts.app')

@section('content')
@include('admin-panel.dashboard-tabs.common.dashboard-nav', ['activeTab' => 'settings'])

<!-- Dashboard Container -->
<main class="cdsTYDashboard-main-main-content">
    <div class="dashboard-container">
 
            
            <div class="container mb-4">
                <!-- Settings Grid -->
                <div class="row">
                    <!-- Security Settings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h3>Security Settings</h3>
                            </div>
                            <div class="settings-card-body">
                                <p>Manage your account security, passwords, and authentication settings to keep your account safe.</p>
                                <div class="settings-card-actions">
                                    <a href="{{ baseUrl('/settings/security') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Manage Security
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Settings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <h3>Message Settings</h3>
                            </div>
                            <div class="settings-card-body">
                                <p>Configure your messaging preferences, notifications, and communication settings.</p>
                                <div class="settings-card-actions">
                                    <a href="{{ baseUrl('/message-settings') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Configure Messages
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discussion Settings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <h3>Discussion Settings</h3>
                            </div>
                            <div class="settings-card-body">
                                <p>Manage your discussion board preferences, moderation settings, and community guidelines.</p>
                                <div class="settings-card-actions">
                                    <a href="{{ baseUrl('/settings/discussion') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Manage Discussions
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feed Settings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon">
                                    <i class="fas fa-rss"></i>
                                </div>
                                <h3>Feed Settings</h3>
                            </div>
                            <div class="settings-card-body">
                                <p>Customize your feed preferences, content filtering, and social media integration settings.</p>
                                <div class="settings-card-actions">
                                    <a href="{{ baseUrl('/settings/feeds') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Configure Feeds
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Privacy Settings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <h3>Privacy Settings</h3>
                            </div>
                            <div class="settings-card-body">
                                <p>Control your privacy preferences, data sharing, and visibility settings across the platform.</p>
                                <div class="settings-card-actions">
                                    <a href="{{ baseUrl('/settings/privacy') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Manage Privacy
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Settings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                                <h3>Account Settings</h3>
                            </div>
                            <div class="settings-card-body">
                                <p>Update your personal information, profile details, and account preferences.</p>
                                <div class="settings-card-actions">
                                    <a href="{{ baseUrl('/settings/account') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Edit Account
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Settings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <h3>Payment Settings</h3>
                            </div>
                            <div class="settings-card-body">
                                <p>Manage your payment methods, billing information, and financial preferences.</p>
                                <div class="settings-card-actions">
                                    <a href="{{ baseUrl('/settings/payment') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Manage Payments
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Settings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <h3>Review Settings</h3>
                            </div>
                            <div class="settings-card-body">
                                <p>Configure your review preferences, rating settings, and feedback management.</p>
                                <div class="settings-card-actions">
                                    <a href="{{ baseUrl('/settings/reviews') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Manage Reviews
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Working Hours -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h3>Working Hours</h3>
                            </div>
                            <div class="settings-card-body">
                                <p>Set your availability, working hours, and schedule preferences for appointments.</p>
                                <div class="settings-card-actions">
                                    <a href="{{ baseUrl('/working-hours') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Set Hours
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Settings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <h3>Appointment Settings</h3>
                            </div>
                            <div class="settings-card-body">
                                <p>Configure your appointment booking preferences, availability, and scheduling rules.</p>
                                <div class="settings-card-actions">
                                    <a href="{{ baseUrl('/appointments/settings') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Configure Appointments
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Section -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="quick-actions-section">
                            <h3 class="quick-actions-title">
                                <i class="fas fa-bolt"></i>
                                Quick Actions
                            </h3>
                            <div class="quick-actions-grid">
                                <a href="{{ baseUrl('/settings/security') }}" class="quick-action-item">
                                    <i class="fas fa-key"></i>
                                    <span>Change Password</span>
                                </a>
                                <a href="{{ baseUrl('/settings/account') }}" class="quick-action-item">
                                    <i class="fas fa-user-edit"></i>
                                    <span>Update Profile</span>
                                </a>
                                <a href="{{ baseUrl('/message-settings') }}" class="quick-action-item">
                                    <i class="fas fa-bell"></i>
                                    <span>Notification Settings</span>
                                </a>
                                <a href="{{ baseUrl('/working-hours') }}" class="quick-action-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Set Availability</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Content -->
        </div>
    </div>
</main>
@endsection

@section('javascript')
@include('admin-panel.dashboard-tabs.common.dashboard-scripts')
@endsection

<style>
/* Custom Styles for Settings Page */
.settings-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 24px;
    height: 100%;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.settings-card:hover {
    border-color: #3b82f6;
}

.settings-card-header {
    display: flex;
    align-items: center;
    margin-bottom: 16px;
}

.settings-card-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
    color: white;
    font-size: 20px;
}

.settings-card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.settings-card-body p {
    color: #6b7280;
    margin-bottom: 20px;
    line-height: 1.6;
}

.settings-card-actions .btn {
    width: 100%;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.settings-card-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.quick-actions-section {
    background: #f8fafc;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid #e2e8f0;
}

.quick-actions-title {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    color: #1f2937;
    font-size: 20px;
    font-weight: 600;
}

.quick-actions-title i {
    margin-right: 12px;
    color: #3b82f6;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.quick-action-item {
    display: flex;
    align-items: center;
    padding: 16px;
    background: white;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.quick-action-item:hover {
    background: #3b82f6;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    text-decoration: none;
}

.quick-action-item i {
    margin-right: 12px;
    font-size: 18px;
    width: 20px;
    text-align: center;
}

.quick-action-item span {
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .settings-card {
        padding: 20px;
    }
    
    .settings-card-header {
        flex-direction: column;
        text-align: center;
    }
    
    .settings-card-icon {
        margin-right: 0;
        margin-bottom: 12px;
    }
}
</style>
