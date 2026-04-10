
    <!-- Close Button (top-right corner) -->
    <button class="CDSFeed-close-btn" onclick="toggleFeedSidebar()"><i class="fa-solid fa-xmark"></i></button>

    <div class="CDSFeed-profile-card">
        {!! getProfileImage(auth()->user()->unique_id) !!}
        <div class="CDSFeed-profile-name">{{ auth()->user()->first_name." ".auth()->user()->last_name }}</div>
        <div class="CDSFeed-profile-role">{{ auth()->user()->professionalLicense->title??''}}</div>
    </div>

    <div class="CDSFeed-stats-grid">
        <div class="CDSFeed-stat-card">
            <div class="CDSFeed-stat-value">{{ totalPostedFeed(auth()->user()->id) }}</div>
            <div class="CDSFeed-stat-label">My Feeds</div>
        </div>
        <div class="CDSFeed-stat-card">
            <div class="CDSFeed-stat-value">{{ draftFeeds(auth()->user()->id) }}</div>
            <div class="CDSFeed-stat-label">Draft Feeds</div>
        </div>
        <div class="CDSFeed-stat-card">
            <div class="CDSFeed-stat-value">{{ commentedFeed(auth()->user()->id) }}</div>
            <div class="CDSFeed-stat-label">Commented In</div>
        </div>
        <div class="CDSFeed-stat-card">
            <div class="CDSFeed-stat-value">{{ scheduledFeed(auth()->user()->id) }}</div>
            <div class="CDSFeed-stat-label">Scheduled</div>
        </div>
    </div>

    <nav class="CDSFeed-nav-menu">
        <div class="CDSFeed-nav-item {{ request()->is('*/my-feeds') && !request()->is('*/my-feeds/status/*') ? 'active' : '' }}">
            <a href="{{ baseUrl('my-feeds') }}" class="CDSFeed-nav-link">
                <div class="CDSFeed-nav-content">
                    <span class="CDSFeed-nav-icon">📄</span>
                    <span>All Feeds</span>
                </div>
                <span class="CDSFeed-nav-counter">{{ totalPostedFeed() }}</span>
            </a>
        </div>
        
        <div class="CDSFeed-nav-item {{ request()->is('*/my-feeds/status/my-feeds') ? 'active' : '' }}">
            <a href="{{ baseUrl('my-feeds/status/my-feeds') }}" class="CDSFeed-nav-link">
                <div class="CDSFeed-nav-content">
                    <span class="CDSFeed-nav-icon">📊</span>
                    <span>My Feeds</span>
                </div>
                <span class="CDSFeed-nav-counter">{{ totalPostedFeed(auth()->user()->id) ?? 0 }}</span>
            </a>
        </div>
        
        <div class="CDSFeed-nav-item {{ request()->is('*/my-feeds/status/draft') ? 'active' : '' }}">
            <a href="{{ baseUrl('my-feeds/status/draft') }}" class="CDSFeed-nav-link">
                <div class="CDSFeed-nav-content">
                    <span class="CDSFeed-nav-icon">✏️</span>
                    <span>Drafts</span>
                </div>
                <span class="CDSFeed-nav-counter">{{ draftFeeds(auth()->user()->id) ?? 0 }}</span>
            </a>
        </div>
        
        <div class="CDSFeed-nav-item {{ request()->is('*/my-feeds/status/scheduled') ? 'active' : '' }}">
            <a href="{{ baseUrl('my-feeds/status/scheduled') }}" class="CDSFeed-nav-link">
                <div class="CDSFeed-nav-content">
                    <span class="CDSFeed-nav-icon">📅</span>
                    <span>Scheduled</span>
                </div>
                <span class="CDSFeed-nav-counter">{{ scheduledFeed(auth()->user()->id) ?? 0 }}</span>
            </a>
        </div>
        
        <!-- Optional: Add more menu items with counters -->
        
        <div class="CDSFeed-nav-item {{ request()->is('*/my-feeds/status/commented') ? 'active' : '' }}">
            <a href="{{ baseUrl('my-feeds/status/commented') }}" class="CDSFeed-nav-link">
                <div class="CDSFeed-nav-content">
                    <span class="CDSFeed-nav-icon">💬</span>
                    <span>Commented</span>
                </div>
                <span class="CDSFeed-nav-counter">{{ commentedFeed(auth()->user()->id) ?? 0 }}</span>
            </a>
        </div>
        
        <div class="CDSFeed-nav-item {{ request()->is('*/my-feeds/status/pinned') ? 'active' : '' }}">
            <a href="{{ baseUrl('my-feeds/status/pinned') }}" class="CDSFeed-nav-link">
                <div class="CDSFeed-nav-content">
                    <span class="CDSFeed-nav-icon">📌</span>
                    <span>Pinned</span>
                </div>
                <span class="CDSFeed-nav-counter">{{ pinnedFeeds(auth()->user()->id) ?? 0 }}</span>
            </a>
        </div>

          <div class="CDSFeed-nav-item {{ request()->is('*/my-feeds/status/liked') ? 'active' : '' }}">
            <a href="{{ baseUrl('my-feeds/status/liked') }}" class="CDSFeed-nav-link">
                <div class="CDSFeed-nav-content">
                    <span class="CDSFeed-nav-icon">  <i class="fas fa-thumbs-up"></i> </span>
                    <span>Liked</span>
                </div>
                <span class="CDSFeed-nav-counter">{{ likedFeeds(auth()->user()->id) ?? 0 }}</span>
            </a>
        </div>
        
        
        <div class="CDSFeed-nav-item {{ request()->is('*/my-feeds/status/favorites') ? 'active' : '' }}">
            <a href="{{ baseUrl('my-feeds/status/favorites') }}" class="CDSFeed-nav-link">
                <div class="CDSFeed-nav-content">
                    <span class="CDSFeed-nav-icon">⭐</span>
                    <span>Favourite</span>
                </div>
                <span class="CDSFeed-nav-counter">{{ favoriteFeeds(auth()->user()->id) ?? 0 }}</span>
            </a>
        </div>
    </nav>


