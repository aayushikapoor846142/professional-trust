    @if (!empty($records) && $records->isNotEmpty())
    <!-- Case 1 -->
        @foreach($records as $case)
            <div class="CDSPostCaseNotifications-compact-list-case-item">
                <div class="CDSPostCaseNotifications-compact-list-case-details">
                    <div class="CDSPostCaseNotifications-compact-list-case-title">{{$case->title}}</div>
                    <div class="CDSPostCaseNotifications-compact-list-case-description">{{ str_limit($case->description, 50, '...') }}</div>
                    <div class="CDSPostCaseNotifications-compact-list-tags">
                        <span class="CDSPostCaseNotifications-compact-list-tag CDSPostCaseNotifications-compact-list-new"> {{$case->services->name ?? ''}}</span>
                        <span class="CDSPostCaseNotifications-compact-list-tag CDSPostCaseNotifications-compact-list-live">{{$case->subServices->name ?? ''}}</span>
                    </div>
                </div>
                <div class="CDSPostCaseNotifications-compact-list-status">
                    <span class="CDSPostCaseNotifications-compact-list-status-badge CDSPostCaseNotifications-compact-list-{{$case->status}}">
                        <span class="CDSPostCaseNotifications-compact-list-status-dot"></span>
                        {{ $case->status ?? '' }}
                    </span>
                </div>
                <div class="CDSPostCaseNotifications-compact-list-client">
                    <!-- <div class="CDSPostCaseNotifications-compact-list-avatar CDSPostCaseNotifications-compact-list-purple"> -->
                        {!! getProfileImage($case->userAdded->unique_id) !!}
                    <!-- </div> -->
                    <div class="CDSPostCaseNotifications-compact-list-client-info">
                        <div class="CDSPostCaseNotifications-compact-list-client-name">  {{$case->userAdded->first_name ?? ''}} {{$case->userAdded->last_name ?? ''}}</div>
                        <div class="CDSPostCaseNotifications-compact-list-client-time">{{getTimeAgo($case->created_at ?? '' )}}</div>
                    </div>
                </div>
                <div class="CDSPostCaseNotifications-compact-list-proposals">
                    <div class="CDSPostCaseNotifications-compact-list-proposal-count">
                        @if(count($case->submitProposal) == 0)
                            No proposals yet 
                        @else   
                            {{count($case->submitProposal)}}  Proposals
                        @endif
                        
                    </div>
                </div>
                <div class="CDSPostCaseNotifications-compact-list-actions">
                    <a href="{{baseUrl('cases/view/'.$case->unique_id)}}" class="CDSPostCaseNotifications-compact-list-btn CDSPostCaseNotifications-compact-list-btn-view">View</a>
                    {{--<button class="CDSPostCaseNotifications-compact-list-btn CDSPostCaseNotifications-compact-list-btn-apply">Apply</button>--}}
                </div>
            </div>
        @endforeach
    @endif

@if(!empty($records))
    @if($current_page > 2 &&  $current_page < $last_page)
        <div class="posting-case-more-link text-center">
            <a href="javascript:;" onclick="listCaseData({{ $next_page }})" class="CdsTYButton-btn-primary">View More <i class="fa fa-chevron-down"></i></a>
        </div>
    @endif
@endif
