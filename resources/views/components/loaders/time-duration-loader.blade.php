<div class="time-duration-skeleton-loader">
    <!-- Skeleton items - showing 5 items by default -->
    @for($i = 1; $i <= 5; $i++)
    <div class="CdsTYDashboardAppointment-settings-glass-list-item skeleton-item">
        <!-- Checkbox skeleton -->
        <div class="skeleton-checkbox"></div>
        
        <!-- Content skeleton -->
        <div class="CdsTYDashboardAppointment-settings-item-content">
            <!-- Title skeleton -->
            <div class="skeleton-title"></div>
            
            <!-- Meta items skeleton -->
            <div class="CdsTYDashboardAppointment-settings-item-meta">
                <div class="skeleton-meta-item">
                    <div class="skeleton-icon"></div>
                    <div class="skeleton-text"></div>
                </div>
                <div class="skeleton-meta-item">
                    <div class="skeleton-icon"></div>
                    <div class="skeleton-text"></div>
                </div>
                <div class="skeleton-meta-item">
                    <div class="skeleton-icon"></div>
                    <div class="skeleton-text"></div>
                </div>
            </div>
        </div>
        
        <!-- Actions skeleton -->
        <div class="skeleton-actions"></div>
    </div>
    @endfor
</div>

<style>
.time-duration-skeleton-loader {
    width: 100%;
}

.skeleton-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 32px;
    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: 16px;
    margin-bottom: 16px;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
    min-height: 80px;
}

.skeleton-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

.skeleton-checkbox {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.05);
    flex-shrink: 0;
}

.CdsTYDashboardAppointment-settings-item-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.skeleton-title {
    height: 24px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 6px;
    width: 70%;
    animation: pulse 1.5s ease-in-out infinite;
}

.CdsTYDashboardAppointment-settings-item-meta {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}

.skeleton-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.skeleton-icon {
    width: 16px;
    height: 16px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 2px;
    flex-shrink: 0;
    animation: pulse 1.5s ease-in-out infinite;
}

.skeleton-text {
    height: 16px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    width: 100px;
    animation: pulse 1.5s ease-in-out infinite;
}

.skeleton-actions {
    width: 40px;
    height: 40px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    flex-shrink: 0;
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.6;
    }
    50% {
        opacity: 1;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .skeleton-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .CdsTYDashboardAppointment-settings-item-meta {
        gap: 12px;
    }
    
    .skeleton-actions {
        align-self: flex-end;
    }
}

@media (max-width: 480px) {
    .CdsTYDashboardAppointment-settings-item-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .skeleton-meta-item {
        width: 100%;
    }
    
    .skeleton-text {
        width: 120px;
    }
}
</style>
