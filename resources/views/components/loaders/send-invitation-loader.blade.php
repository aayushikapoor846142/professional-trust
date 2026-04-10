<style>
.skeleton {
    background: #e2e5e7;
    border-radius: 4px;
    position: relative;
    overflow: hidden;
}
.skeleton::after {
    content: "";
    display: block;
    position: absolute;
    left: -150px;
    top: 0;
    height: 100%;
    width: 150px;
    background: linear-gradient(90deg, transparent, #f5f5f5 50%, transparent);
    animation: loading 1.2s infinite;
}
@keyframes loading {
    0% { left: -150px; }
    100% { left: 100%; }
}
.skeleton-avatar { width: 40px; height: 40px; border-radius: 50%; }
.skeleton-checkbox { width: 18px; height: 18px; border-radius: 4px; }
.skeleton-text { height: 14px; margin-bottom: 6px; }
.skeleton-badge, .skeleton-action, .skeleton-stars { border-radius: 8px; }
</style>

@for($i = 0; $i < 5; $i++)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox skeleton skeleton-checkbox"></div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Client Details">
        <div class="d-block d-md-flex align-items-center">
            <div class="CdsSendInvitation-client-avatar skeleton skeleton-avatar"></div>
            <div class="CdsSendInvitation-client-info ms-2">
                <div class="CdsSendInvitation-client-email skeleton skeleton-text" style="width: 120px;"></div>
                <div class="CdsSendInvitation-client-meta skeleton skeleton-text" style="width: 80px;"></div>
            </div>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">
        <div class="skeleton skeleton-badge" style="width: 70px; height: 20px;"></div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Sent Date">
        <div class="d-flex gap-2 align-items-start">
            <div class="CdsSendInvitation-date skeleton skeleton-text" style="width: 70px;"></div>
            <div class="CdsSendInvitation-time skeleton skeleton-text" style="width: 50px;"></div>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Response">
        <div class="skeleton skeleton-stars" style="width: 60px; height: 20px;"></div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Actions">
        <div class="skeleton skeleton-action" style="width: 30px; height: 20px;"></div>
    </div>
</div>
@endfor
