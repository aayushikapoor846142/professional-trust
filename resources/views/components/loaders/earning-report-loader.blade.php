<style>
.skeleton-loader {
    animation: skeleton-loading 1.5s ease-in-out infinite;
}

@keyframes skeleton-loading {
    0% {
        background-color: #f0f0f0;
    }
    50% {
        background-color: #e0e0e0;
    }
    100% {
        background-color: #f0f0f0;
    }
}

.skeleton-checkbox {
    width: 16px;
    height: 16px;
    border-radius: 3px;
    background-color: #f0f0f0;
}

.skeleton-text {
    height: 16px;
    border-radius: 4px;
    background-color: #f0f0f0;
    margin-bottom: 4px;
}

.skeleton-text-small {
    height: 12px;
    width: 60%;
}

.skeleton-text-medium {
    height: 14px;
    width: 80%;
}

.skeleton-text-large {
    height: 16px;
    width: 90%;
}

.skeleton-amount {
    height: 14px;
    width: 70px;
    border-radius: 4px;
    background-color: #f0f0f0;
    margin-bottom: 2px;
}

.skeleton-date {
    height: 14px;
    width: 80px;
    border-radius: 4px;
    background-color: #f0f0f0;
}

.skeleton-cell {
    padding: 12px 8px;
    border-bottom: 1px solid #e9ecef;
}

.skeleton-row {
    display: flex;
    align-items: center;
    background-color: #fff;
    border-bottom: 1px solid #e9ecef;
}

.skeleton-row:hover {
    background-color: #f8f9fa;
}
</style>

<!-- Skeleton loader for earning reports table -->
<div class="cdsTYDashboard-table-body">
    @for($i = 0; $i < 3; $i++)
    <div class="cdsTYDashboard-table-row skeleton-row">
        <!-- Checkbox column -->
        <div class="cdsTYDashboard-table-cell cdsCheckbox skeleton-cell">
            <div class="skeleton-checkbox skeleton-loader"></div>
        </div>
        
        <!-- Invoice No column -->
        <div class="cdsTYDashboard-table-cell skeleton-cell">
            <div class="skeleton-text skeleton-text-medium skeleton-loader"></div>
        </div>
        
        <!-- Client Name column -->
        <div class="cdsTYDashboard-table-cell skeleton-cell">
            <div class="skeleton-text skeleton-text-large skeleton-loader"></div>
            <div class="skeleton-text skeleton-text-small skeleton-loader"></div>
        </div>
        
        <!-- Case Title column -->
        <div class="cdsTYDashboard-table-cell skeleton-cell">
            <div class="skeleton-text skeleton-text-large skeleton-loader"></div>
        </div>
        
        <!-- Earning Amount column -->
        <div class="cdsTYDashboard-table-cell skeleton-cell">
            <div class="skeleton-amount skeleton-loader"></div>
            <div class="skeleton-amount skeleton-loader"></div>
            <div class="skeleton-amount skeleton-loader"></div>
        </div>
        
        <!-- Paid Date column -->
        <div class="cdsTYDashboard-table-cell skeleton-cell">
            <div class="skeleton-date skeleton-loader"></div>
        </div>
        
        <!-- Created Date column -->
        <div class="cdsTYDashboard-table-cell skeleton-cell">
            <div class="skeleton-date skeleton-loader"></div>
        </div>
    </div>
    @endfor
</div>
