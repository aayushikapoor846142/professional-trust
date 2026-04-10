
<!-- Navigation Links -->
<!-- Trending -->
<div class="CdsDiscussionThread-sidebar-card">
    <h3 class="CdsDiscussionThread-sidebar-title">🔥 Trending</h3>
    <div class="CdsDiscussionThread-trending-item">
        <span class="CdsDiscussionThread-trending-number">1</span>
        <div class="CdsDiscussionThread-trending-content">
            <div class="CdsDiscussionThread-trending-title">Topic 1</div>
            <div class="CdsDiscussionThread-trending-meta">34 likes • 12 replies</div>
        </div>
    </div>
    <div class="CdsDiscussionThread-trending-item">
        <span class="CdsDiscussionThread-trending-number">2</span>
        <div class="CdsDiscussionThread-trending-content">
            <div class="CdsDiscussionThread-trending-title">aaaa fdfd ew topic</div>
            <div class="CdsDiscussionThread-trending-meta">0 likes • 0 replies</div>
        </div>
    </div>
    <div class="CdsDiscussionThread-trending-item">
        <span class="CdsDiscussionThread-trending-number">3</span>
        <div class="CdsDiscussionThread-trending-content">
            <div class="CdsDiscussionThread-trending-title">fdsfsd fsdf dsf d</div>
            <div class="CdsDiscussionThread-trending-meta">0 likes • 0 replies</div>
        </div>
    </div>
</div>

<div class="CdsDiscussionThread-sidebar-card">
    <a href="{{ baseUrl('manage-discussion-threads/all-discussion') }}" class="CdsDiscussionThread-nav-link {{ $list_type == 'all-discussion' ? 'active' : '' }}">
        <div class="CdsDiscussionThread-nav-link-content">
            <span class="CdsDiscussionThread-nav-icon">📑</span>
            <span>All Discussions</span>
        </div>
        <span class="CdsDiscussionThread-nav-count">{{ totalAllDiscussion() }}</span>
    </a>
    <a href="{{ baseUrl('manage-discussion-threads/my-discussion') }}" class="CdsDiscussionThread-nav-link {{ $list_type == 'my-discussion' ? 'active' : '' }}">
        <div class="CdsDiscussionThread-nav-link-content">
            <span class="CdsDiscussionThread-nav-icon">📊</span>
            <span>My Discussions</span>
        </div>
        <span class="CdsDiscussionThread-nav-count">{{ totalAllDiscussion(auth()->user()->id ?? 0) }}</span>
    </a>
    <a href="{{ baseUrl('manage-discussion-threads/discussion-connected') }}" class="CdsDiscussionThread-nav-link {{ $list_type == 'discussion-connected' ? 'active' : '' }}">
        <div class="CdsDiscussionThread-nav-link-content">
            <span class="CdsDiscussionThread-nav-icon">💬</span>
            <span>Discussion Connected</span>
        </div>
        <span class="CdsDiscussionThread-nav-count">{{ totalDiscussionConnected(auth()->user()->id ?? 0) }}</span>
    </a>
    <a href="{{ baseUrl('manage-discussion-threads/saved-discussion') }}" class="CdsDiscussionThread-nav-link {{ $list_type == 'saved-discussion' ? 'active' : '' }}">
        <div class="CdsDiscussionThread-nav-link-content">
            <span class="CdsDiscussionThread-nav-icon">⭐</span>
            <span>Saved Discussions</span>
        </div>
        <span class="CdsDiscussionThread-nav-count">{{ totalSavedDiscussion(auth()->user()->id ?? 0) }}</span>
    </a>
    <a href="{{ baseUrl('manage-discussion-threads/pending-requests') }}" class="CdsDiscussionThread-nav-link {{ $list_type == 'pending-requests' ? 'active' : '' }}">
        <div class="CdsDiscussionThread-nav-link-content">
            <span class="CdsDiscussionThread-nav-icon">💬</span>
            <span>Pending Requests</span>
        </div>
        <span class="CdsDiscussionThread-nav-count">{{ totalPendingRequest(auth()->user()->id ?? 0) }}</span>
    </a>
</div>

<!-- Categories -->
<div class="CdsDiscussionThread-sidebar-card">
    <h3 class="CdsDiscussionThread-sidebar-title">Categories</h3>
    <div class="CdsDiscussionThread-category-glass active">
        <a href="{{ baseUrl('manage-discussion-threads') }}">
            <span>All Threads</span>
            <span class="CdsDiscussionThread-category-count">{{ totalAllDiscussion() }}</span>
        </a>
    </div>
    @foreach(discussionCategories() as $category)
    <div class="CdsDiscussionThread-category-glass">
        <a href="{{ baseUrl('manage-discussion-threads/category/'.$category->unique_id) }}">
            <span>{{ $category->name }}</span>
            <span class="CdsDiscussionThread-category-count">{{ $category->discussionThreads->count() }}</span>
        </a>
    </div>
    @endforeach
</div>
