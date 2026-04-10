<div class="CdsCaseRequest-skeleton-loader">
    <!-- Skeleton for multiple case items -->
    <div class="CdsCaseRequest-skeleton-item">
        <div class="CdsCaseRequest-skeleton-header">
            <div class="CdsCaseRequest-skeleton-header-top">
                <div class="CdsCaseRequest-skeleton-title-section">
                    <div class="CdsCaseRequest-skeleton-title"></div>
                    <div class="CdsCaseRequest-skeleton-id"></div>
                </div>
                <div class="CdsCaseRequest-skeleton-actions">
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                </div>
            </div>
            <div class="CdsCaseRequest-skeleton-meta">
                <div class="CdsCaseRequest-skeleton-meta-item">
                    <div class="CdsCaseRequest-skeleton-icon"></div>
                    <div class="CdsCaseRequest-skeleton-text"></div>
                </div>
                <div class="CdsCaseRequest-skeleton-status"></div>
                <div class="CdsCaseRequest-skeleton-avatar"></div>
            </div>
        </div>
    </div>

    <!-- Second skeleton item -->
    <div class="CdsCaseRequest-skeleton-item">
        <div class="CdsCaseRequest-skeleton-header">
            <div class="CdsCaseRequest-skeleton-header-top">
                <div class="CdsCaseRequest-skeleton-title-section">
                    <div class="CdsCaseRequest-skeleton-title"></div>
                    <div class="CdsCaseRequest-skeleton-id"></div>
                </div>
                <div class="CdsCaseRequest-skeleton-actions">
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                </div>
            </div>
            <div class="CdsCaseRequest-skeleton-meta">
                <div class="CdsCaseRequest-skeleton-meta-item">
                    <div class="CdsCaseRequest-skeleton-icon"></div>
                    <div class="CdsCaseRequest-skeleton-text"></div>
                </div>
                <div class="CdsCaseRequest-skeleton-status"></div>
                <div class="CdsCaseRequest-skeleton-avatar"></div>
            </div>
        </div>
    </div>

    <!-- Third skeleton item -->
    <div class="CdsCaseRequest-skeleton-item">
        <div class="CdsCaseRequest-skeleton-header">
            <div class="CdsCaseRequest-skeleton-header-top">
                <div class="CdsCaseRequest-skeleton-title-section">
                    <div class="CdsCaseRequest-skeleton-title"></div>
                    <div class="CdsCaseRequest-skeleton-id"></div>
                </div>
                <div class="CdsCaseRequest-skeleton-actions">
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                    <div class="CdsCaseRequest-skeleton-btn"></div>
                </div>
            </div>
            <div class="CdsCaseRequest-skeleton-meta">
                <div class="CdsCaseRequest-skeleton-meta-item">
                    <div class="CdsCaseRequest-skeleton-icon"></div>
                    <div class="CdsCaseRequest-skeleton-text"></div>
                </div>
                <div class="CdsCaseRequest-skeleton-status"></div>
                <div class="CdsCaseRequest-skeleton-avatar"></div>
            </div>
        </div>
    </div>

    <style>
        /* Skeleton Loader Styles */
        .CdsCaseRequest-skeleton-loader {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .CdsCaseRequest-skeleton-item {
            background: #ffffff;
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
            animation: CdsCaseRequest-skeletonFadeIn 0.6s ease-out;
        }

        .CdsCaseRequest-skeleton-item:nth-child(1) { animation-delay: 0.1s; }
        .CdsCaseRequest-skeleton-item:nth-child(2) { animation-delay: 0.2s; }
        .CdsCaseRequest-skeleton-item:nth-child(3) { animation-delay: 0.3s; }

        @keyframes CdsCaseRequest-skeletonFadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .CdsCaseRequest-skeleton-header-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .CdsCaseRequest-skeleton-title-section {
            flex: 1;
        }

        .CdsCaseRequest-skeleton-title {
            height: 2rem;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            animation: CdsCaseRequest-skeletonShimmer 1.5s infinite;
        }

        .CdsCaseRequest-skeleton-id {
            height: 1.125rem;
            width: 60%;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 0.25rem;
            animation: CdsCaseRequest-skeletonShimmer 1.5s infinite;
        }

        .CdsCaseRequest-skeleton-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .CdsCaseRequest-skeleton-btn {
            width: 80px;
            height: 36px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 0.5rem;
            animation: CdsCaseRequest-skeletonShimmer 1.5s infinite;
        }

        .CdsCaseRequest-skeleton-meta {
            display: flex;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .CdsCaseRequest-skeleton-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .CdsCaseRequest-skeleton-icon {
            width: 20px;
            height: 20px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 50%;
            animation: CdsCaseRequest-skeletonShimmer 1.5s infinite;
        }

        .CdsCaseRequest-skeleton-text {
            width: 120px;
            height: 0.875rem;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 0.25rem;
            animation: CdsCaseRequest-skeletonShimmer 1.5s infinite;
        }

        .CdsCaseRequest-skeleton-status {
            width: 100px;
            height: 32px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 2rem;
            animation: CdsCaseRequest-skeletonShimmer 1.5s infinite;
        }

        .CdsCaseRequest-skeleton-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 50%;
            animation: CdsCaseRequest-skeletonShimmer 1.5s infinite;
        }

        @keyframes CdsCaseRequest-skeletonShimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .CdsCaseRequest-skeleton-loader {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .CdsCaseRequest-skeleton-header-top {
                text-align: center;
                display: block;
            }

            .CdsCaseRequest-skeleton-actions {
                justify-content: center;
                margin-top: 1rem;
            }

            .CdsCaseRequest-skeleton-meta {
                gap: 1rem;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .CdsCaseRequest-skeleton-actions {
                flex-direction: column;
                align-items: center;
            }

            .CdsCaseRequest-skeleton-btn {
                width: 120px;
            }

            .CdsCaseRequest-skeleton-meta {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
        }
    </style>
</div>
