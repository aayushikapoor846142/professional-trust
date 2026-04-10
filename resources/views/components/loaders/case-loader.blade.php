<div class="case-skeleton-loader">
    @for ($i = 0; $i < 5; $i++)
        <div class="CDSPostCaseNotifications-list-view-case-container">
            <div class="CDSPostCaseNotifications-list-view-case-row" style="display: flex; align-items: stretch;">
                <div class="CDSPostCaseNotifications-list-view-case-info" style="flex:2; padding-right:16px;">
                    <div class="skeleton skeleton-title" style="width: 60%; height: 20px; margin-bottom: 8px; border-radius: 4px;"></div>
                    <div class="skeleton skeleton-desc" style="width: 90%; height: 16px; margin-bottom: 8px; border-radius: 4px;"></div>
                    <div class="CDSPostCaseNotifications-list-view-case-tags" style="display:flex; gap:8px;">
                        <span class="skeleton skeleton-tag" style="width: 60px; height: 18px; border-radius: 8px;"></span>
                        <span class="skeleton skeleton-tag" style="width: 60px; height: 18px; border-radius: 8px;"></span>
                    </div>
                </div>
                <div class="CDSPostCaseNotifications-list-view-status-cell" style="flex:1; display:flex; align-items:center; justify-content:center;">
                    <span class="skeleton skeleton-status" style="width: 60px; height: 20px; border-radius: 10px;"></span>
                </div>
                <div class="CDSPostCaseNotifications-list-view-client-cell" style="flex:1.5; display:flex; align-items:center; gap:8px;">
                    <span class="skeleton skeleton-avatar" style="width:32px; height:32px; border-radius:50%;"></span>
                    <div>
                        <div class="skeleton skeleton-client-name" style="width: 80px; height: 14px; margin-bottom: 4px; border-radius: 4px;"></div>
                        <div class="skeleton skeleton-client-time" style="width: 60px; height: 10px; border-radius: 4px;"></div>
                    </div>
                </div>
                <div class="CDSPostCaseNotifications-list-view-proposals-cell" style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                    <span class="skeleton skeleton-proposal-count" style="width: 24px; height: 16px; border-radius: 4px; margin-bottom: 4px;"></span>
                    <span class="skeleton skeleton-proposal-label" style="width: 40px; height: 10px; border-radius: 4px;"></span>
                </div>
                <div class="CDSPostCaseNotifications-list-view-actions-cell" style="flex:1.5; display:flex; align-items:center; gap:8px; justify-content:center;">
                    <span class="skeleton skeleton-action-btn" style="width: 70px; height: 24px; border-radius: 12px;"></span>
                    <span class="skeleton skeleton-action-btn" style="width: 70px; height: 24px; border-radius: 12px;"></span>
                </div>
            </div>
        </div>
    @endfor
</div>
<style>
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
    background-size: 400% 100%;
    animation: skeleton-loading 1.2s ease-in-out infinite;
    display: inline-block;
}
@keyframes skeleton-loading {
    0% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0 50%;
    }
}
</style>
