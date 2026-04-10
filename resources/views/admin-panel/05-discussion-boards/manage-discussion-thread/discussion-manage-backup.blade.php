@extends('admin-panel.layouts.app')
@section('content')
<div class="cdsTYDashboard-title-section-top01" >
<div class="cdsTYDashboard-title-section-top01-outer" >
<div class="cdsTYDashboard-title-section-top01-main" >

{{$pageTitle}}

</div>
<div class="cdsTYDashboard-title-section-top01-action" >
 

</div>

</div>


</div>
<section class="cdsTYDashboard-discussion-panel-section">
    <div class="cdsTYDashboard-discussion-panel-section-header">

    </div>
    <div class="cdsTYDashboard-discussion-panel-section-body">



        <div class="cdsTYDashboard-discussion-panel-section-body-inner">
            <div class="cdsTYDashboard-discussion-panel-section-body-main">
                 @if($discussionUId == '')
                <div class="cdsTYDashboard-discussion-panel-section-body-header">
                    <div class="cdsTYDashboard-discussion-panel-section-body-header-panel">
                       
                        <div class="cdsTYDashboard-discussion-panel-section-body-header-panel-search">

                            @if(!empty($category))
                            <h3>Category: {{ $category->name??'' }}</h3>
                            @endif
                            <div class="cdsTYDashboard-discussion-panel-search" id="discussion-search"
                                style="display: none;">
                                <div class="cdsTYDashboard-discussion-panel-search-header">
                                    <div class="cdsTYDashboard-discussion-panel-search-header-inner">
                                        <a href="javascript:;" class="search-icon">
                                            <i class="fa-magnifying-glass fa-regular fa-sharp"></i></a>
                                        <input type="text" id="discussionThreadSearch"
                                            onkeyup="getDiscussionThreadSearch(this.value)"
                                            placeholder="Search Discussion Thread" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(checkPrivilege([
                        'route_prefix' => 'panel.discussion-threads',
                        'module' => 'professional-discussion-threads',
                        'action' => 'add'
                        ]))
                        <div class="cdsTYDashboard-discussion-panel-section-body-header-panel-post">
                            <a href="{{ baseUrl('discussion-threads/add') }}">Post Topic</a>
                        </div>
                        @endif
                    </div>
                    <!-- add skeleton -->
                </div>
                <!-- tabs -->
                <div class="cdsTYDashboard-feed-inside-navigation">
                    <div class="chat-header">
                        <div class="chat-sidebar-tabs mb-3">
                            <input type="hidden" value="all" id="list-feed-data" />
                            <a href="javascript:;" onclick="listDiscussionsData('all', this)" class="active">All
                                [{{ countDiscussionType('all', isset($categoryId) ? $categoryId : null) }}]
                            </a>
                            <a href="javascript:;" onclick="listDiscussionsData('my', this)">My
                                [{{countDiscussionType('my',isset($categoryId) ? $categoryId : null)}}]</a>
                            <a href="javascript:;" onclick="listDiscussionsData('connected', this)" class="">Connected
                                To [{{countDiscussionType('connected',isset($categoryId) ? $categoryId : null)}}]</a>
                            <a href="javascript:;" onclick="listDiscussionsData('favourite', this)" class="">Favourite
                                [{{countDiscussionType('favourite',isset($categoryId) ? $categoryId : null)}}]</a>
                            <a href="javascript:;" onclick="listDiscussionsData('pending', this)" class="">Pending
                                Request [{{countDiscussionType('pending',isset($categoryId) ? $categoryId : null)}} /
                                {{countDiscussionType('send_pending',isset($categoryId) ? $categoryId : null)}}]</a>
                        </div>
                    </div>
                </div>
               
                <div class="cdsTYDashboard-feed-recent-container">

                    <div id="feeds-sidebar-list" style="display:{{$discussionUId == ''?'block':'none'}}"
                        class="feed-left-side"></div>

                    <button id="feedLink" class="CdsTYButton-btn-primary loaderBtn">Load More </button>
                </div>
                @else
                <div class="feed-container" style="display:{{$discussionUId != ''?'block':'none'}}">
                    @if($discussionUId != '')
                    {{--@include("admin-panel.05-discussion-boards.discussion-boards.conversation.partials.discussion-inner-container") --}}
                    @endif
                </div>
                @endif
                <!-- end tabs -->
                <div class="cdsTYDashboard-discussion-panel-section-body-main-inner">
                    <div class="cds-t25n-content-professional-profile-container-main-body-information">
                        <div class="cds-t25n-content-professional-profile-container-main-body-information-exp">
                            <div
                                class="cds-t25n-content-professional-profile-container-main-body-information-expertise-container">
                                <div class="cds-t25n-content-professional-profile-container-main-body-information-exp">
                                    <div
                                        class="cds-t25n-content-professional-profile-container-main-body-information-expertise-container">
                                        <div class="cdsTYDashboard-discussion-panel-main-container">
                                            <div class="feed-left-side"
                                                style="display:{{$discussionUId == ''?'block':'none'}}">
                                                <div class="feed-content">

                                                </div>
                                                <div id="loading-spinner" class="mt-50" style="display: none;">
                                                    @include('components.skelenton-loader.discussion-threads-skeleton')
                                                </div>
                                                <div class="cdsTYDashboard-discussion-panel-recent-container">
                                                    <!-- <div id="feeds-sidebar-list">
                                                    </div> -->
                                                    <button id="feedLink" class="CdsTYButton-btn-primary loaderBtn"
                                                        style="display: none;">Load More </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cdsTYDashboard-discussion-panel-section-body-sidebar">
                <input type="hidden" id="categoryId" value="@if(isset($categoryId)) {{ $categoryId }} @else null @endif">
                {{-- @if(isset($showSidebar) && $showSidebar) --}}
                @if($template == 'discussion-manage')
                <div id="discussion-categories">
                    <div class="cdsTYDashboard-discussion-categories-container">
                        <div class="cdsTYDashboard-discussion-categories-container-header">Categories</div>
                        <div class="cdsTYDashboard-discussion-categories-container-body">

                            <ul>
                                <li><a href="{{ baseUrl(url: 'discussion-threads/manage') }}"
                                        class="{{ (empty($category))?'active-category':'' }}">All
                                        [{{getDiscussionCountByCategory()}}]</a> </li>
                                @foreach($categories as $cat)
                                <li><a href="{{ baseUrl('discussion-threads/discussion-list/' . $cat->unique_id) }}"
                                        class="{{ (!empty($category) && $cat->id == $category->id)?'active-cat':'' }}">{{ $cat->name }}
                                        [{{getDiscussionCountByCategory($cat->unique_id)}}]</a>
                                </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>
                </div>
                {{-- @endif --}}
                @endif

            </div>
        </div>
    </div>
    <div class="cdsTYDashboard-discussion-panel-section-footer"></div>
</section>
@php
$loader_html = minify_html(view("components.skelenton-loader.discussion-comment-loader")->render());
@endphp
@endsection
@push('scripts')
<script src="{{url('assets/js/custom-editor.js')}}"></script>
<script src="{{ url('assets/js/discussion-thread.js?v='.mt_rand()) }}"></script>

<script type="text/javascript">
var timestamp = "{{time()}}";
var users = '';
var loader_html = '{!! $loader_html !!}';
@if(($chat_members ?? '') != '')
users = {
    !!$chat_members!!
};
@endif
document.addEventListener('DOMContentLoaded', async function() {
   @if($discussionUId != '')
       await loadDiscussionContentAjax("{{$discussionUId}}", "{{$discussion_id}}");
       await initializeDiscussionContent("{{$discussion_id}}");
       await initializeDiscussionSocket("{{$discussion_id}}",'load');
   @endif
  
});
</script><script>document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.querySelector('.cdsTYDashboard-custom01-sidebar-toggle');
  const sidebar = document.querySelector('.cdsTYDashboard-discussion-panel-section-body-sidebar');
  const closeBtn = document.querySelector('.cdsTYDashboard-custom01-sidebar-close');
  const overlay = document.querySelector('.cdsTYDashboard-custom01-sidebar-overlay');

  function openSidebar() {
    sidebar.classList.add('open');
    document.body.classList.add('cdsTYDashboard-custom01-sidebar-open');
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    document.body.classList.remove('cdsTYDashboard-custom01-sidebar-open');
  }
  if(toggleBtn !== null)
    toggleBtn.addEventListener('click', openSidebar);
  if(closeBtn !== null)
    closeBtn.addEventListener('click', closeSidebar);
  if(overlay !== null)
    overlay.addEventListener('click', closeSidebar);

  // Optional: close if clicking outside sidebar and not on toggle
  document.addEventListener('click', function (e) {
    if (
      document.body.classList.contains('cdsTYDashboard-custom01-sidebar-open') &&
      !sidebar.contains(e.target) &&
      !toggleBtn.contains(e.target) &&
      !overlay.contains(e.target)
    ) {
      closeSidebar();
    }
  });
});

</script>
@endpush
