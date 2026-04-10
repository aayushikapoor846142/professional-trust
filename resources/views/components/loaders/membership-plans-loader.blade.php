<div class="membership-plans-skeleton-loader">
    <style>
        .membership-plans-skeleton-loader {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px 0;
        }
        
        .skeleton-plan-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            min-height: 200px;
            position: relative;
            overflow: hidden;
        }
        
        .skeleton-plan-card::before {
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
        
        .skeleton-title {
            height: 24px;
            background: #e0e0e0;
            border-radius: 4px;
            margin-bottom: 12px;
            width: 80%;
        }
        
        .skeleton-subtitle {
            height: 16px;
            background: #e0e0e0;
            border-radius: 4px;
            margin-bottom: 20px;
            width: 100%;
        }
        
        .skeleton-price-container {
            margin-bottom: 24px;
        }
        
        .skeleton-price {
            height: 32px;
            background: #e0e0e0;
            border-radius: 4px;
            margin-bottom: 8px;
            width: 60%;
        }
        
        .skeleton-period {
            height: 14px;
            background: #e0e0e0;
            border-radius: 4px;
            width: 40%;
        }
        
        .skeleton-button {
            height: 44px;
            background: #e0e0e0;
            border-radius: 8px;
            width: 100%;
        }
        
        .skeleton-error {
            height: 16px;
            background: #e0e0e0;
            border-radius: 4px;
            width: 100%;
            margin-top: 12px;
        }
        
        /* Animation delays for staggered effect */
        .skeleton-plan-card:nth-child(1) {
            animation-delay: 0.1s;
        }
        
        .skeleton-plan-card:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .skeleton-plan-card:nth-child(3) {
            animation-delay: 0.3s;
        }
        
        .skeleton-plan-card:nth-child(4) {
            animation-delay: 0.4s;
        }
        
        .skeleton-plan-card:nth-child(5) {
            animation-delay: 0.5s;
        }
        
        .skeleton-plan-card:nth-child(6) {
            animation-delay: 0.6s;
        }
    </style>
    
    <!-- Skeleton Plan Card 1 -->
    <div class="skeleton-plan-card">
        <div class="skeleton-title"></div>
        <div class="skeleton-subtitle"></div>
        <div class="skeleton-price-container">
            <div class="skeleton-price"></div>
            <div class="skeleton-period"></div>
        </div>
        <div class="skeleton-button"></div>
    </div>
    
    <!-- Skeleton Plan Card 2 -->
    <div class="skeleton-plan-card">
        <div class="skeleton-title"></div>
        <div class="skeleton-subtitle"></div>
        <div class="skeleton-price-container">
            <div class="skeleton-price"></div>
            <div class="skeleton-period"></div>
        </div>
        <div class="skeleton-button"></div>
    </div>
    
    <!-- Skeleton Plan Card 3 -->
    <div class="skeleton-plan-card">
        <div class="skeleton-title"></div>
        <div class="skeleton-subtitle"></div>
        <div class="skeleton-price-container">
            <div class="skeleton-price"></div>
            <div class="skeleton-period"></div>
        </div>
        <div class="skeleton-button"></div>
    </div>
    
    <!-- Skeleton Plan Card 4 -->
    <div class="skeleton-plan-card">
        <div class="skeleton-title"></div>
        <div class="skeleton-subtitle"></div>
        <div class="skeleton-price-container">
            <div class="skeleton-price"></div>
            <div class="skeleton-period"></div>
        </div>
        <div class="skeleton-button"></div>
    </div>
    
    <!-- Skeleton Plan Card 5 -->
    <div class="skeleton-plan-card">
        <div class="skeleton-title"></div>
        <div class="skeleton-subtitle"></div>
        <div class="skeleton-price-container">
            <div class="skeleton-price"></div>
            <div class="skeleton-period"></div>
        </div>
        <div class="skeleton-button"></div>
    </div>
    
    <!-- Skeleton Plan Card 6 -->
    <div class="skeleton-plan-card">
        <div class="skeleton-title"></div>
        <div class="skeleton-subtitle"></div>
        <div class="skeleton-price-container">
            <div class="skeleton-price"></div>
            <div class="skeleton-period"></div>
        </div>
        <div class="skeleton-button"></div>
    </div>
</div> 