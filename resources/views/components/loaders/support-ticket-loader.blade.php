@for($i = 0; $i < 2; $i++)
<div class="CdsTicket-ticket-card skeleton-loader">
    <div class="CdsTicket-card-header">
        <div class="CdsTicket-header-top">
            <div class="CdsTicket-ticket-meta">
                <div class="CdsTicket-checkbox-wrapper">
                    <span class="skeleton-box" style="width: 18px; height: 18px; border-radius: 4px;"></span>
                </div>
                <div class="CdsTicket-ticket-id">
                    <span class="CdsTicket-ticket-icon skeleton-box" style="width: 24px; height: 24px; border-radius: 50%;"></span>
                    <span class="skeleton-box" style="width: 60px; height: 16px;"></span>
                </div>
            </div>
            <span class="CdsTicket-status-badge skeleton-box" style="width: 80px; height: 20px;"></span>
        </div>
        <h3 class="CdsTicket-ticket-subject">
            <span class="skeleton-box" style="width: 70%; height: 18px;"></span>
        </h3>
        <div class="CdsTicket-user-info">
            <div class="CdsTicket-user-avatar skeleton-box" style="width: 32px; height: 32px; border-radius: 50%;"></div>
            <div class="CdsTicket-user-details">
                <span class="CdsTicket-user-name skeleton-box" style="width: 100px; height: 14px;"></span>
                <span class="CdsTicket-user-email skeleton-box" style="width: 140px; height: 12px;"></span>
            </div>
        </div>
    </div>
    <div class="CdsTicket-card-body">
        <div class="CdsTicket-info-item">
            <span class="CdsTicket-info-label skeleton-box" style="width: 60px; height: 12px;"></span>
            <span class="CdsTicket-info-value skeleton-box" style="width: 80px; height: 12px;"></span>
        </div>
        <div class="CdsTicket-info-item">
            <span class="CdsTicket-info-label skeleton-box" style="width: 60px; height: 12px;"></span>
            <span class="CdsTicket-priority-badge skeleton-box" style="width: 60px; height: 16px;"></span>
        </div>
        <div class="CdsTicket-info-item">
            <span class="CdsTicket-info-label skeleton-box" style="width: 80px; height: 12px;"></span>
            <span class="CdsTicket-info-value skeleton-box" style="width: 100px; height: 12px;"></span>
        </div>
        <div class="CdsTicket-info-item">
            <span class="CdsTicket-info-label skeleton-box" style="width: 60px; height: 12px;"></span>
            <span class="CdsTicket-info-value skeleton-box" style="width: 80px; height: 12px;"></span>
        </div>
    </div>
    <div class="CdsTicket-card-footer">
        <div class="CdsTicket-date-info skeleton-box" style="width: 120px; height: 14px;"></div>
        <div class="CdsTicket-action-buttons">
            <span class="CdsTicket-btn skeleton-box" style="width: 90px; height: 32px; border-radius: 6px;"></span>
        </div>
    </div>
</div>
@endfor

<style>
.skeleton-loader .skeleton-box {
    display: inline-block;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
    background-size: 400% 100%;
    animation: skeleton-loading 1.2s ease-in-out infinite;
}
@keyframes skeleton-loading {
    0% { background-position: 100% 50%; }
    100% { background-position: 0 50%; }
}
</style>
