@extends('admin-panel.layouts.app')

@section('page-submenu')
{!! pageSubMenu('reviews') !!}
@endsection

@section('styles')
<link href="{{ url('assets/css/21-CDS-review-overview.css') }}" rel="stylesheet" />
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <h1 class="CdsReviewOverview-page-title mb-0">{{ $pageTitle }}</h1>
<!-- Stats Grid -->
        <div class="CdsReviewOverview-stats-grid">
            <div class="CdsReviewOverview-stat-card">
                <div class="CdsReviewOverview-stat-content">
                    <div class="CdsReviewOverview-stat-icon">📊</div>
                    <div class="CdsReviewOverview-stat-info">
                        <div class="CdsReviewOverview-stat-value" id="total-invitations">{{ $totalReviews ?? 0 }}</div>
                        <div class="CdsReviewOverview-stat-label">Total Reviews</div>
                    </div>
                </div>
            </div>
            <div class="CdsReviewOverview-stat-card">
                <div class="CdsReviewOverview-stat-content">
                    <div class="CdsReviewOverview-stat-icon">⏳</div>
                    <div class="CdsReviewOverview-stat-info">
                        <div class="CdsReviewOverview-stat-value" id="pending-invitations">{{ $pendingReviews ?? 0 }}</div>
                        <div class="CdsReviewOverview-stat-label">Pending</div>
                    </div>
                </div>
            </div>
            <div class="CdsReviewOverview-stat-card">
                <div class="CdsReviewOverview-stat-content">
                    <div class="CdsReviewOverview-stat-icon">✅</div>
                    <div class="CdsReviewOverview-stat-info">
                        <div class="CdsReviewOverview-stat-value" id="reviews-given">{{ $reviewsGiven ?? 0 }}</div>
                        <div class="CdsReviewOverview-stat-label">Reviews Received</div>
                    </div>
                </div>
            </div>
        </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
      <!-- Reviews Section -->
        <div class="CdsReviewOverview-reviews-section">
            <h2 class="CdsReviewOverview-section-title">Recent Reviews</h2>
            <div class="CdsReviewOverview-reviews-list" id="reviewsList">
                @forelse($recentReviews as $index => $review)
                <div class="CdsReviewOverview-review-item">
                    <span class="CdsReviewOverview-review-email">{{ $review->professional->email ?? '-' }}</span>
                    <span class="CdsReviewOverview-review-date">{{ \Carbon\Carbon::parse($review->created_at)->format('M d, Y') }}</span>
                </div>
                @empty
                <div class="CdsReviewOverview-review-item">
                    <span class="text-muted">No recent reviews found.</span>
                </div>
                @endforelse
            </div>
            <a href="{{ baseUrl('reviews/review-received') }}" class="CdsReviewOverview-btn CdsReviewOverview-btn-primary">
                View All Reviews
            </a>
        </div>
			</div>
	
	</div>
  </div>
</div>

@endsection

@section('javascript')
<script>
    // Animate reviews on load
    window.addEventListener('load', () => {
        const reviewItems = document.querySelectorAll('.CdsReviewOverview-review-item');
        reviewItems.forEach((item, index) => {
            setTimeout(() => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.3s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, 50);
            }, index * 100);
        });
    });

    function cdsReviewOverviewOpenReview(id) {
        // Placeholder function; implement modal or redirect as needed
        alert(`Opening review ${id}`);
    }

    function cdsReviewOverviewViewAllReviews() {
        window.location.href = "{{ baseUrl('review-received') }}";
    }

    function cdsReviewOverviewAnimateStats() {
        const statValues = document.querySelectorAll('.CdsReviewOverview-stat-value');
        statValues.forEach((stat) => {
            const finalValue = parseInt(stat.textContent);
            let currentValue = 0;
            const increment = finalValue / 20;
            
            const counter = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    stat.textContent = finalValue;
                    clearInterval(counter);
                } else {
                    stat.textContent = Math.floor(currentValue);
                }
            }, 50);
        });
    }

    setTimeout(cdsReviewOverviewAnimateStats, 500);
</script>
@endsection
