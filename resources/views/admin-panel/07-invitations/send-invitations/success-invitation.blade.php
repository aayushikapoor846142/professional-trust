<style>
.cdsSendInvitationPreview-success-container {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    max-width: 600px;
    width: 100%;
    padding: 3rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin: auto;
}

.cdsSendInvitationPreview-confetti {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
}

.cdsSendInvitationPreview-confetti-piece {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #f0f;
    animation: cdsSendInvitationPreview-confetti-fall 3s ease-out infinite;
}

@keyframes cdsSendInvitationPreview-confetti-fall {
    0% {
        transform: translateY(-100vh) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh) rotate(720deg);
        opacity: 0;
    }
}

.cdsSendInvitationPreview-confetti-piece:nth-child(1) {
    left: 10%;
    animation-delay: 0s;
    background: #f39c12;
}

.cdsSendInvitationPreview-confetti-piece:nth-child(2) {
    left: 20%;
    animation-delay: 0.3s;
    background: #00c0ef;
}

.cdsSendInvitationPreview-confetti-piece:nth-child(3) {
    left: 30%;
    animation-delay: 0.6s;
    background: #e74c3c;
}

.cdsSendInvitationPreview-confetti-piece:nth-child(4) {
    left: 40%;
    animation-delay: 0.9s;
    background: #3c8dbc;
}

.cdsSendInvitationPreview-confetti-piece:nth-child(5) {
    left: 50%;
    animation-delay: 1.2s;
    background: #f39c12;
}

.cdsSendInvitationPreview-confetti-piece:nth-child(6) {
    left: 60%;
    animation-delay: 1.5s;
    background: #00a65a;
}

.cdsSendInvitationPreview-confetti-piece:nth-child(7) {
    left: 70%;
    animation-delay: 1.8s;
    background: #e74c3c;
}

.cdsSendInvitationPreview-confetti-piece:nth-child(8) {
    left: 80%;
    animation-delay: 2.1s;
    background: #f012be;
}

.cdsSendInvitationPreview-confetti-piece:nth-child(9) {
    left: 90%;
    animation-delay: 2.4s;
    background: #3c8dbc;
}

.cdsSendInvitationPreview-success-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: #d4edda;
    color: #28a745;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    font-size: 3rem;
    position: relative;
    animation: cdsSendInvitationPreview-success-bounce 0.6s ease-out;
}

@keyframes cdsSendInvitationPreview-success-bounce {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.cdsSendInvitationPreview-success-title {
    font-size: 26px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 1rem;
}

.cdsSendInvitationPreview-success-message {
    font-size: 1.125rem;
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 2rem;
}

.cdsSendInvitationPreview-success-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 12px;
}

.cdsSendInvitationPreview-stat-item {
    text-align: center;
}

.cdsSendInvitationPreview-stat-value {
    font-size: 2rem;
    font-weight: 600;
    color: #0066ff;
    margin-bottom: 0.25rem;
}

.cdsSendInvitationPreview-stat-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.cdsSendInvitationPreview-btn {
    padding: 0.875rem 2rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s;
    font-weight: 600;
}

.cdsSendInvitationPreview-btn-primary {
    background-color: #0066ff;
    color: white;
    width: 100%;
    margin-bottom: 1rem;
}

.cdsSendInvitationPreview-btn-primary:hover {
    background-color: #0052cc;
}

.cdsSendInvitationPreview-btn-secondary {
    background-color: transparent;
    color: #6c757d;
    border: 1px solid #ced4da;
    width: 100%;
}

.cdsSendInvitationPreview-btn-secondary:hover {
    background-color: #f8f9fa;
    color: #495057;
}

/* Review Submitted Success */
.cdsSendInvitationPreview-review-success {
    display: none;
}

.cdsSendInvitationPreview-points-earned {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background-color: #fff3cd;
    color: #856404;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
    margin-bottom: 1rem;
}

.cdsSendInvitationPreview-next-steps {
    background-color: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    text-align: left;
}

.cdsSendInvitationPreview-next-steps-title {
    font-weight: 600;
    margin-bottom: 1rem;
    color: #212529;
}

.cdsSendInvitationPreview-next-step-item {
    display: flex;
    align-items: start;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.cdsSendInvitationPreview-step-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background-color: #e7f3ff;
    color: #0066ff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.875rem;
    font-weight: 600;
}

.cdsSendInvitationPreview-step-text {
    color: #495057;
    line-height: 1.5;
}

/* Account Created Success */
.cdsSendInvitationPreview-account-success {
    display: none;
}

.cdsSendInvitationPreview-welcome-message {
    background: linear-gradient(135deg, #0066ff 0%, #0052cc 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.cdsSendInvitationPreview-welcome-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.cdsSendInvitationPreview-welcome-text {
    opacity: 0.9;
    line-height: 1.5;
}

/* Invitations Sent Success */
.cdsSendInvitationPreview-invitations-success {
    /* This is the default displayed state */
}

.cdsSendInvitationPreview-timeline-preview {
    background-color: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    text-align: left;
}

.cdsSendInvitationPreview-timeline-title {
    font-weight: 600;
    margin-bottom: 1rem;
    color: #212529;
    font-size: 26px;
}

.cdsSendInvitationPreview-timeline-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    position: relative;
}

.cdsSendInvitationPreview-timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 20px;
    top: 40px;
    width: 1px;
    height: 20px;
    background-color: #dee2e6;
}

.cdsSendInvitationPreview-timeline-dot {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.25rem;
}

.cdsSendInvitationPreview-timeline-dot.cdsSendInvitationPreview-active {
    background-color: #d4edda;
    color: #28a745;
}

.cdsSendInvitationPreview-timeline-dot.cdsSendInvitationPreview-upcoming {
    background-color: #e9ecef;
    color: #6c757d;
}

.cdsSendInvitationPreview-timeline-content {
    flex: 1;
}

.cdsSendInvitationPreview-timeline-label {
    font-weight: 500;
    color: #212529;
    margin-bottom: 0.125rem;
}

.cdsSendInvitationPreview-timeline-date {
    font-size: 0.875rem;
    color: #6c757d;
}

.cdsSendInvitationPreview-share-section {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    margin-top: 2rem;
}

.cdsSendInvitationPreview-share-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 1px solid #e9ecef;
    background-color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.cdsSendInvitationPreview-share-btn:hover {
    border-color: #0066ff;
    background-color: #e7f3ff;
    transform: translateY(-2px);
}

.cdsSendInvitationPreview-logo {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #0066ff;
}

@media (max-width: 639px) {
    .cdsSendInvitationPreview-success-container {padding: 2rem;}
    .cdsSendInvitationPreview-timeline-preview {padding: 0;}
}
</style>
<div class="cdsSendInvitationPreview-success-container">
    
    <!-- Confetti Animation -->
    <div class="cdsSendInvitationPreview-confetti">
        <div class="cdsSendInvitationPreview-confetti-piece"></div>
        <div class="cdsSendInvitationPreview-confetti-piece"></div>
        <div class="cdsSendInvitationPreview-confetti-piece"></div>
        <div class="cdsSendInvitationPreview-confetti-piece"></div>
        <div class="cdsSendInvitationPreview-confetti-piece"></div>
        <div class="cdsSendInvitationPreview-confetti-piece"></div>
        <div class="cdsSendInvitationPreview-confetti-piece"></div>
        <div class="cdsSendInvitationPreview-confetti-piece"></div>
        <div class="cdsSendInvitationPreview-confetti-piece"></div>
    </div>
    
    <!-- Invitations Sent Success (Default) -->
    <div class="cdsSendInvitationPreview-invitations-success">
        <div class="cdsSendInvitationPreview-success-icon">
            <svg width="60" height="60" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
            </svg>
        </div>
             @if(!empty($skippedInvitations))
                <div class="alert alert-warning mt-3">
                    {{ $skippedInvitations }}
                </div>
            @endif
        <h1 class="cdsSendInvitationPreview-success-title">Invitations Sent Successfully!</h1>
        <p class="cdsSendInvitationPreview-success-message">
            Your review invitations are on their way. We'll track their progress and notify you when clients respond.
        </p>
        
        <div class="cdsSendInvitationPreview-timeline-preview">
            <h3 class="cdsSendInvitationPreview-timeline-title">What Happens Next</h3>
            <div class="cdsSendInvitationPreview-timeline-item">
                <div class="cdsSendInvitationPreview-timeline-dot cdsSendInvitationPreview-active">✉️</div>
                <div class="cdsSendInvitationPreview-timeline-content">
                    <div class="cdsSendInvitationPreview-timeline-label">Invitations Delivered</div>
                    <div class="cdsSendInvitationPreview-timeline-date">Within next hour</div>
                </div>
            </div>
            <div class="cdsSendInvitationPreview-timeline-item">
                <div class="cdsSendInvitationPreview-timeline-dot cdsSendInvitationPreview-upcoming">👀</div>
                <div class="cdsSendInvitationPreview-timeline-content">
                    <div class="cdsSendInvitationPreview-timeline-label">Track Opens & Clicks</div>
                    <div class="cdsSendInvitationPreview-timeline-date">Real-time tracking</div>
                </div>
            </div>
            <!-- <div class="cdsSendInvitationPreview-timeline-item">
                <div class="cdsSendInvitationPreview-timeline-dot cdsSendInvitationPreview-upcoming">🔔</div>
                <div class="cdsSendInvitationPreview-timeline-content">
                    <div class="cdsSendInvitationPreview-timeline-label">Automatic Reminder</div>
                    <div class="cdsSendInvitationPreview-timeline-date">After 7 days if no response</div>
                </div>
            </div> -->
        </div>
        
        <a href="{{ baseUrl('reviews/send-invitations') }}" class="cdsSendInvitationPreview-btn cdsSendInvitationPreview-btn-primary">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            Back to List
        </a>
        <!-- <a href="{{ baseUrl('send-invitations') }}" class="cdsSendInvitationPreview-btn cdsSendInvitationPreview-btn-secondary">Back to List</a> -->
    </div>
    
    <!-- Review Submitted Success (Hidden by default) -->
    <div class="cdsSendInvitationPreview-review-success">
        <div class="cdsSendInvitationPreview-success-icon">
            <svg width="60" height="60" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </div>
        
        <div class="cdsSendInvitationPreview-points-earned">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            You earned 50 points!
        </div>
        
        <h1 class="cdsSendInvitationPreview-success-title">Thank You for Your Review!</h1>
        <p class="cdsSendInvitationPreview-success-message">
            Your feedback has been submitted successfully. Dr. Sarah Johnson and future patients appreciate your honest review.
        </p>
        
        <div class="cdsSendInvitationPreview-next-steps">
            <h3 class="cdsSendInvitationPreview-next-steps-title">Your Review Status</h3>
            <div class="cdsSendInvitationPreview-next-step-item">
                <div class="cdsSendInvitationPreview-step-icon">✓</div>
                <div class="cdsSendInvitationPreview-step-text">Your review has been published and is now visible to others</div>
            </div>
            <div class="cdsSendInvitationPreview-next-step-item">
                <div class="cdsSendInvitationPreview-step-icon">✉️</div>
                <div class="cdsSendInvitationPreview-step-text">You'll receive an email if the professional responds</div>
            </div>
            <div class="cdsSendInvitationPreview-next-step-item">
                <div class="cdsSendInvitationPreview-step-icon">📊</div>
                <div class="cdsSendInvitationPreview-step-text">Track how many people find your review helpful</div>
            </div>
        </div>
        
        <a href="#" class="cdsSendInvitationPreview-btn cdsSendInvitationPreview-btn-primary">View My Reviews</a>
        <button class="cdsSendInvitationPreview-btn cdsSendInvitationPreview-btn-secondary">Write Another Review</button>
        
        <div class="cdsSendInvitationPreview-share-section">
            <button class="cdsSendInvitationPreview-share-btn">
                <svg width="20" height="20" fill="#1877F2" viewBox="0 0 20 20">
                    <path d="M20 10.06C20 4.48 15.52 0 10 0S0 4.48 0 10.06c0 5.04 3.66 9.21 8.44 9.94v-7.03H5.9v-2.91h2.54V7.84c0-2.52 1.49-3.9 3.78-3.9 1.09 0 2.24.2 2.24.2v2.46H13.2c-1.24 0-1.63.78-1.63 1.57v1.9h2.78l-.45 2.9h-2.33V20A10.04 10.04 0 0020 10.06z"/>
                </svg>
            </button>
            <button class="cdsSendInvitationPreview-share-btn">
                <svg width="20" height="20" fill="#1DA1F2" viewBox="0 0 20 20">
                    <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/>
                </svg>
            </button>
            <button class="cdsSendInvitationPreview-share-btn">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11 7a1 1 0 012 0v2.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414L11 9.586V7z"/>
                    <path d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Account Created Success (Hidden by default) -->
    <div class="cdsSendInvitationPreview-account-success">
        <div class="cdsSendInvitationPreview-success-icon">
            <svg width="60" height="60" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
            </svg>
        </div>
        
        <p class="cdsSendInvitationPreview-success-message">
            Your account has been created successfully. You're now part of our trusted review community.
        </p>
        
        <div class="cdsSendInvitationPreview-welcome-message">
            <h3 class="cdsSendInvitationPreview-welcome-title">What You Can Do Now</h3>
            <p class="cdsSendInvitationPreview-welcome-text">
                Write reviews for professionals you've visited, track your review history, and help others make informed decisions about their healthcare providers.
            </p>
        </div>
        
        <div class="cdsSendInvitationPreview-next-steps">
            <h3 class="cdsSendInvitationPreview-next-steps-title">Get Started</h3>
            <div class="cdsSendInvitationPreview-next-step-item">
                <div class="cdsSendInvitationPreview-step-icon">1</div>
                <div class="cdsSendInvitationPreview-step-text">Complete your profile to build trust with the community</div>
            </div>
            <div class="cdsSendInvitationPreview-next-step-item">
                <div class="cdsSendInvitationPreview-step-icon">2</div>
                <div class="cdsSendInvitationPreview-step-text">Write your first review for Dr. Sarah Johnson</div>
            </div>
            <div class="cdsSendInvitationPreview-next-step-item">
                <div class="cdsSendInvitationPreview-step-icon">3</div>
                <div class="cdsSendInvitationPreview-step-text">Explore reviews from other verified patients</div>
            </div>
        </div>
        
        <a href="#" class="cdsSendInvitationPreview-btn cdsSendInvitationPreview-btn-primary">Continue to Write Review</a>
        <button class="cdsSendInvitationPreview-btn cdsSendInvitationPreview-btn-secondary">Complete My Profile</button>
    </div>
</div>
@push("send_invitation_scripts")
<script>
    // Simulate different success states based on URL hash
    function cdsSendInvitationPreviewShowSuccessState() {
        const hash = window.location.hash;
        
        // Hide all success states
        document.querySelector('.cdsSendInvitationPreview-invitations-success').style.display = 'none';
        document.querySelector('.cdsSendInvitationPreview-review-success').style.display = 'none';
        document.querySelector('.cdsSendInvitationPreview-account-success').style.display = 'none';
        
        // Show the appropriate state
        switch(hash) {
            case '#review-submitted':
                document.querySelector('.cdsSendInvitationPreview-review-success').style.display = 'block';
                break;
            case '#account-created':
                document.querySelector('.cdsSendInvitationPreview-account-success').style.display = 'block';
                break;
            default:
                document.querySelector('.cdsSendInvitationPreview-invitations-success').style.display = 'block';
        }
    }
    
    // Check on load and hash change
    cdsSendInvitationPreviewShowSuccessState();
    window.addEventListener('hashchange', cdsSendInvitationPreviewShowSuccessState);
    
    // Share functionality
    document.querySelectorAll('.cdsSendInvitationPreview-share-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Sharing...');
            // Implement share functionality
        });
    });
    
    // Button click handlers
    document.querySelectorAll('.cdsSendInvitationPreview-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (this.textContent.includes('Dashboard')) {
                e.preventDefault();
                window.location.href = '#dashboard';
            } else if (this.textContent.includes('Track Invitation')) {
                e.preventDefault();
                window.location.href = '#invitation-tracking';
            } else if (this.textContent.includes('View My Reviews')) {
                e.preventDefault();
                window.location.href = '#my-reviews';
            }
        });
    });
</script>
@endpush