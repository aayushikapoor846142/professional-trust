<style>
.skeleton-loader-table-row {
    display: flex;
    align-items: center;
    min-height: 48px;
    margin-bottom: 4px;
}
.skeleton-loader-table-cell {
    height: 20px;
    border-radius: 4px;
    background: #e2e2e2;
    margin-right: 12px;
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}
.skeleton-loader-table-cell.name { width: 18%; }
.skeleton-loader-table-cell.amount { width: 13%; }
.skeleton-loader-table-cell.tax { width: 13%; }
.skeleton-loader-table-cell.total { width: 13%; }
.skeleton-loader-table-cell.created { width: 16%; }
.skeleton-loader-table-cell.status { width: 12%; }
.skeleton-loader-table-cell.action { width: 10%; margin-right: 0; }

.skeleton-shimmer {
    animation: shimmer 1.2s infinite linear;
    background: linear-gradient(90deg, #e2e2e2 25%, #f5f5f5 50%, #e2e2e2 75%);
    background-size: 200% 100%;
}
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}
</style>
<div>
    @for($i = 0; $i < 2; $i++)
    <div class="skeleton-loader-table-row">
        <div class="skeleton-loader-table-cell name skeleton-shimmer"></div>
        <div class="skeleton-loader-table-cell amount skeleton-shimmer"></div>
        <div class="skeleton-loader-table-cell tax skeleton-shimmer"></div>
        <div class="skeleton-loader-table-cell total skeleton-shimmer"></div>
        <div class="skeleton-loader-table-cell created skeleton-shimmer"></div>
        <div class="skeleton-loader-table-cell status skeleton-shimmer"></div>
        <div class="skeleton-loader-table-cell action skeleton-shimmer"></div>
    </div>
    @endfor
</div>
