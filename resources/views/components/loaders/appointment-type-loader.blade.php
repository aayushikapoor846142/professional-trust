<style>
.appointment-type-skeleton {
    animation: skeleton-loading 1.5s ease-in-out infinite;
}

.appointment-type-skeleton-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    margin-bottom: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.appointment-type-skeleton-checkbox {
    width: 18px;
    height: 18px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    flex-shrink: 0;
}

.appointment-type-skeleton-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.appointment-type-skeleton-title {
    height: 20px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    width: 60%;
}

.appointment-type-skeleton-meta {
    display: flex;
    align-items: center;
    gap: 8px;
}

.appointment-type-skeleton-meta-icon {
    width: 16px;
    height: 16px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    flex-shrink: 0;
}

.appointment-type-skeleton-meta-text {
    height: 14px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    width: 40%;
}

.appointment-type-skeleton-actions {
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 6px;
    flex-shrink: 0;
}

@keyframes skeleton-loading {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .appointment-type-skeleton-item {
        padding: 12px;
        gap: 12px;
    }
    
    .appointment-type-skeleton-title {
        width: 80%;
    }
    
    .appointment-type-skeleton-meta-text {
        width: 60%;
    }
}
</style>

<!-- Skeleton loader items -->
<div class="appointment-type-skeleton-item appointment-type-skeleton">
    <div class="appointment-type-skeleton-checkbox"></div>
    <div class="appointment-type-skeleton-content">
        <div class="appointment-type-skeleton-title"></div>
        <div class="appointment-type-skeleton-meta">
            <div class="appointment-type-skeleton-meta-icon"></div>
            <div class="appointment-type-skeleton-meta-text"></div>
        </div>
    </div>
    <div class="appointment-type-skeleton-actions"></div>
</div>

<div class="appointment-type-skeleton-item appointment-type-skeleton">
    <div class="appointment-type-skeleton-checkbox"></div>
    <div class="appointment-type-skeleton-content">
        <div class="appointment-type-skeleton-title"></div>
        <div class="appointment-type-skeleton-meta">
            <div class="appointment-type-skeleton-meta-icon"></div>
            <div class="appointment-type-skeleton-meta-text"></div>
        </div>
    </div>
    <div class="appointment-type-skeleton-actions"></div>
</div>

<div class="appointment-type-skeleton-item appointment-type-skeleton">
    <div class="appointment-type-skeleton-checkbox"></div>
    <div class="appointment-type-skeleton-content">
        <div class="appointment-type-skeleton-title"></div>
        <div class="appointment-type-skeleton-meta">
            <div class="appointment-type-skeleton-meta-icon"></div>
            <div class="appointment-type-skeleton-meta-text"></div>
        </div>
    </div>
    <div class="appointment-type-skeleton-actions"></div>
</div>

<div class="appointment-type-skeleton-item appointment-type-skeleton">
    <div class="appointment-type-skeleton-checkbox"></div>
    <div class="appointment-type-skeleton-content">
        <div class="appointment-type-skeleton-title"></div>
        <div class="appointment-type-skeleton-meta">
            <div class="appointment-type-skeleton-meta-icon"></div>
            <div class="appointment-type-skeleton-meta-text"></div>
        </div>
    </div>
    <div class="appointment-type-skeleton-actions"></div>
</div>

<div class="appointment-type-skeleton-item appointment-type-skeleton">
    <div class="appointment-type-skeleton-checkbox"></div>
    <div class="appointment-type-skeleton-content">
        <div class="appointment-type-skeleton-title"></div>
        <div class="appointment-type-skeleton-meta">
            <div class="appointment-type-skeleton-meta-icon"></div>
            <div class="appointment-type-skeleton-meta-text"></div>
        </div>
    </div>
    <div class="appointment-type-skeleton-actions"></div>
</div>
