<style>
.skeleton-loader {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.skeleton-item {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 18px 20px;
    position: relative;
    overflow: hidden;
    min-height: 70px;
}
.skeleton-checkbox {
    width: 22px;
    height: 22px;
    border-radius: 4px;
    background: #e0e0e0;
    margin-right: 18px;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}
.skeleton-content {
    flex: 1;
}
.skeleton-title {
    width: 180px;
    height: 18px;
    border-radius: 4px;
    background: #e0e0e0;
    margin-bottom: 10px;
    position: relative;
    overflow: hidden;
}
.skeleton-meta {
    display: flex;
    gap: 12px;
}
.skeleton-meta-item {
    width: 80px;
    height: 12px;
    border-radius: 4px;
    background: #e0e0e0;
    position: relative;
    overflow: hidden;
}
.skeleton-actions {
    display: flex;
    gap: 8px;
    margin-left: 18px;
}
.skeleton-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e0e0e0;
    position: relative;
    overflow: hidden;
}
/* Shimmer animation */
.skeleton-loader .shimmer {
    animation: shimmer 1.5s infinite linear;
    background: linear-gradient(90deg, #e0e0e0 25%, #f3f3f3 50%, #e0e0e0 75%);
    background-size: 200% 100%;
}
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}
</style>

<div class="skeleton-loader">
    @for($i = 0; $i < 5; $i++)
    <div class="skeleton-item">
        <div class="skeleton-checkbox shimmer"></div>
        <div class="skeleton-content">
            <div class="skeleton-title shimmer"></div>
            <div class="skeleton-meta">
                <div class="skeleton-meta-item shimmer"></div>
                <div class="skeleton-meta-item shimmer"></div>
                <div class="skeleton-meta-item shimmer"></div>
                <div class="skeleton-meta-item shimmer"></div>
            </div>
        </div>
        <div class="skeleton-actions">
            <div class="skeleton-action-btn shimmer"></div>
            <div class="skeleton-action-btn shimmer"></div>
            <div class="skeleton-action-btn shimmer"></div>
        </div>
    </div>
    @endfor
</div>
