@php
$type = request('type') ?? ($type == '' ? 'my' : $type);
$sub_type = request('sub_type') ?? 'scheduled';
@endphp
<div class="cdsTYDashboard-feed-main-container">
    <div class="feed-left-side" style="display:{{$feedUId == ''?'block':'none'}}">
        @if(isset($status) && $status == 'my-feeds')
        <div class="feed-content">
            <div id="feed-list-tab" class="active chat-tab-content">
                @include("admin-panel.04-profile.feeds.conversation.partials.create-feed")
            </div>
        </div>
        @endif
        <div class="cdsTYDashboard-feed-inside-navigation mt-0 mt-lg-4">
            <input type="hidden" value="{{ $type }}" id="list-feed-data">
            <input type="hidden" value="{{$sub_type}}" id="my-list-feed-data">
            <div class="chat-header">
                <div class="chat-sidebar-tabs mb-3">
                    <a href="javascript:;" onclick="listFeedsData('my', this)" class="{{ ($type == 'my') ? 'active' : '' }}">My</a>
                    <a href="javascript:;" onclick="listFeedsData('all', this)" class="{{ ($type == 'all') ? 'active' : '' }}">All</a>
                    <a href="javascript:;" onclick="listFeedsData('commented', this)" class="{{ $type == 'commented' ? 'active' : '' }}">Commented</a>
                    <a href="javascript:;" onclick="listFeedsData('pinned', this)" class="{{ $type == 'pinned' ? 'active' : '' }}">Pinned</a>
                    <a href="javascript:;" onclick="listFeedsData('favourite', this)" class="{{ $type == 'favourite' ? 'active' : '' }}">Favourite</a>
                </div>
                <div class="cds-feedSercharea group-search mt-2">
                    <a href="javascript:;" class="search-icon">
                        <i class="fa-magnifying-glass fa-regular fa-sharp"></i>
                    </a>
                    <input type="text" id="feedsSearch" onkeyup="getFeedsSearch(this.value)" placeholder="Search Feeds" />
                </div>
            </div>
        </div>
        <div class="cdsTYDashboard-feed-recent-container">
            <div id="feeds-sidebar-list"></div>
            <div class="text-center">
                <button id="feedLink" class="CdsTYButton-btn-primary loaderBtn">Load More</button>
            </div>
        </div>
    </div>
</div>
<div class="feed-container" style="display:{{$feedUId != ''?'block':'none'}}">
    @if($feedUId != '')
    <div class="bg-white">
        @include('components.skelenton-loader.feed-detail-loader')
    </div>
    @endif
</div>
@php
$loader_html = minify_html(view("components.skelenton-loader.feed-comment-loader")->render());
@endphp
@include('admin-panel.04-profile.feeds.conversation.partials.scripts')
@push('scripts')
<script>
var loader_html = '{!! $loader_html !!}';
var users = '';
@if(($chat_members ?? '') != '')
users = {
    !!$chat_members!!
};
@endif
var feed_status = "{{ $status??'my-feeds' }}";
</script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    @if(!empty($feedUId))
        await loadFeedContentAjax("{{$feedUId}}", "{{$feed_id}}");
        await initializeFeedContent("{{$feed_id}}");
        await initializeFeedSocket("{{$feed_id}}");
    @endif
});
</script>
<script>
function toggleFeedShareIcons() {
    const allShareLists = document.querySelectorAll('.share-list');
    const currentShareList = document.getElementById('sharedListTwo');
    const isVisible = currentShareList.classList.contains('visible');
    allShareLists.forEach((list) => {
        list.classList.add('hidden');
        list.classList.remove('visible');
    });
    if (!isVisible) {
        currentShareList.classList.remove('hidden');
        currentShareList.classList.add('visible');
    }
}
</script>
@endpush