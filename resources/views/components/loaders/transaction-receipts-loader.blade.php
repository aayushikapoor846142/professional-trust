<style>
.skeleton-slide-row {
    animation: slideIn 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}
@keyframes slideIn {
    0% {
        opacity: 0;
        transform: translateX(-40px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}
.skeleton-slide {
    display: inline-block;
    height: 18px;
    width: 100%;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    border-radius: 4px;
    animation: skeleton-loading 1.2s infinite linear;
}
@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
.skeleton-name { width: 70%; height: 18px; margin-bottom: 4px; }
.skeleton-amount, .skeleton-tax, .skeleton-type { width: 50%; height: 16px; margin-bottom: 4px; }
.skeleton-date { width: 60%; height: 16px; margin-bottom: 4px; }
.skeleton-status { width: 40%; height: 16px; margin-bottom: 4px; }
.skeleton-download, .skeleton-view { width: 32px; height: 16px; margin: 0 auto; }
</style>

@for($i = 0; $i < 5; $i++)
<div class="cdsTYDashboard-table-row skeleton-slide-row">
    <div class="cdsTYDashboard-table-cell"><div class="skeleton-slide skeleton-name"></div></div>
    <div class="cdsTYDashboard-table-cell"><div class="skeleton-slide skeleton-amount"></div></div>
    <div class="cdsTYDashboard-table-cell"><div class="skeleton-slide skeleton-tax"></div></div>
    <div class="cdsTYDashboard-table-cell"><div class="skeleton-slide skeleton-date"></div></div>
    <div class="cdsTYDashboard-table-cell"><div class="skeleton-slide skeleton-date"></div></div>
    <div class="cdsTYDashboard-table-cell"><div class="skeleton-slide skeleton-status"></div></div>
    <div class="cdsTYDashboard-table-cell"><div class="skeleton-slide skeleton-type"></div></div>
    <div class="cdsTYDashboard-table-cell"><div class="skeleton-slide skeleton-download"></div></div>
    <div class="cdsTYDashboard-table-cell"><div class="skeleton-slide skeleton-view"></div></div>
</div>
@endfor
