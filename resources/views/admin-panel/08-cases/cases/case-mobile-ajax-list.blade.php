@if (!empty($records) && $records->isNotEmpty())
    @foreach ($records as $case)
        <div class="CDSPostCaseNotifications-list-view-mobile-case-card ${c.isNew ? 'CDSPostCaseNotifications-list-view-new-case' : ''}" data-mobile-case-id="${c.id}">
            <div class="CDSPostCaseNotifications-list-view-mobile-case-header">
                <div>
                    <div class="CDSPostCaseNotifications-list-view-mobile-case-title">
                        {{ $case->title ?? '' }}
                    </div>
                    <div class="CDSPostCaseNotifications-list-view-case-tags">
                        <span class="CDSPostCaseNotifications-list-view-tag">{{$case->services->name}}</span>
                        <span class="CDSPostCaseNotifications-list-view-tag">{{$case->subServices->name}}</span>
                    </div>
                </div>
                <div class="CDSPostCaseNotifications-list-view-status-badge CDSPostCaseNotifications-list-view-status-{{$case->status}}">
                    <div class="CDSPostCaseNotifications-list-view-pulse-dot"></div>
                    {{ $case->status ?? '' }}
                </div>
            </div>
            <div class="CDSPostCaseNotifications-list-view-mobile-case-description">{!! html_entity_decode($case->description) !!}</div>
            <div class="CDSPostCaseNotifications-list-view-mobile-case-meta">
                <div class="CDSPostCaseNotifications-list-view-mobile-meta-item">
                    <span>{!! getProfileImage($case->userAdded->unique_id) !!}</span>
                    <span>{{$case->userAdded->first_name ?? ''}} {{$case->userAdded->last_name ?? ''}}</span>
                </div>
                <div class="CDSPostCaseNotifications-list-view-mobile-meta-item">
                    <span>📝</span>
                    <span>{{count($case->submitProposal)}} proposals</span>
                </div>
                <div class="CDSPostCaseNotifications-list-view-mobile-meta-item">
                    <span>⏰</span>
                    <span>{{getTimeAgo($case->created_at ?? '' )}}</span>
                </div>
            </div>
            <div class="CDSPostCaseNotifications-list-view-mobile-case-footer">
                @if(checkPrivilege([
                    'route_prefix' => 'panel.cases',
                    'module' => 'professional-cases',
                    'action' => 'view'
                ]))
                <a class="CDSPostCaseNotifications-list-view-btn-action CDSPostCaseNotifications-list-view-btn-view" href="{{ baseUrl('cases/view/'.$case->unique_id) }}">View Details</a>
                @endif
                @if(empty($case->professionalFavouriteCase))
                    <a class="CDSPostCaseNotifications-list-view-btn-action CDSPostCaseNotifications-list-view-btn-apply"  data-href="{{ baseUrl('cases/mark-as-favourite/'.$case->unique_id) }}" onclick="confirmFavourite(this)" data-type="add">Favourite</a>
                @else
                    <a class="CDSPostCaseNotifications-list-view-btn-action CDSPostCaseNotifications-list-view-btn-danger"  data-href="{{ baseUrl('cases/mark-as-favourite/'.$case->unique_id) }}" onclick="confirmFavourite(this)" data-type="remove">Remove Favourite</a>
                @endif
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

