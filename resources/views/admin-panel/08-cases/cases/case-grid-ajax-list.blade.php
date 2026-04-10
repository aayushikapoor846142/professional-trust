
@if (!empty($records) && $records->isNotEmpty())
    @foreach ($records as $case)
        <div class="CDSPostCaseNotifications-grid-view02-feed-card">
            <div class="CDSPostCaseNotifications-grid-view02-card-header">
                <div class="CDSPostCaseNotifications-grid-view02-card-user">
                    <div class="CDSPostCaseNotifications-grid-view02-user-avatar" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">MK</div>
                    <div class="CDSPostCaseNotifications-grid-view02-user-info">
                        <div class="CDSPostCaseNotifications-grid-view02-user-name">{{$case->userAdded->first_name ?? ''}} {{$case->userAdded->last_name ?? ''}}</div>
                        <div class="CDSPostCaseNotifications-grid-view02-post-time">
                            <span>•</span>{{getTimeAgo($case->created_at ?? '' )}}
                        </div>
                    </div>
                </div>
                <h3 class="CDSPostCaseNotifications-grid-view02-card-title">{{ $case->title ?? '' }}</h3>
            </div>
            <div class="CDSPostCaseNotifications-grid-view02-card-body">
                <p class="CDSPostCaseNotifications-grid-view02-card-description">
                      {!! html_entity_decode($case->description) !!}
                </p>
                <div class="CDSPostCaseNotifications-grid-view02-card-tags">
                    <span class="CDSPostCaseNotifications-grid-view02-tag">{{$case->services->name}}</span>
                    <span class="CDSPostCaseNotifications-grid-view02-tag">{{$case->subServices->name}}</span>
                </div>
            </div>
            <div class="CDSPostCaseNotifications-grid-view02-card-footer">
                <div class="CDSPostCaseNotifications-grid-view02-card-stats">
                    <span class="CDSPostCaseNotifications-grid-view02-stat">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        45 views
                    </span>
                    <span class="CDSPostCaseNotifications-grid-view02-stat">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{count($case->submitProposal)}} proposals
                    </span>
                </div>
                <div class="CDSPostCaseNotifications-grid-view02-card-actions">
                    @if($case->is_urgent == 1)
                        <span class="CDSPostCaseNotifications-list-view02-status-badge CDSPostCaseNotifications-list-view02-status-urgent">Urgent</span>
                    @endif
                    @if($case->is_time_constrained == 1 && $case->end_date != null)
                        <span class="CDSPostCaseNotifications-list-view02-status-badge CDSPostCaseNotifications-list-view02-status-featured">Time Constrained: {{ dateFormat($case->end_date) }}</span>
                    @endif
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.cases',
                        'module' => 'professional-cases',
                        'action' => 'view'
                    ]))
                    <a class="CDSPostCaseNotifications-grid-view02-btn CDSPostCaseNotifications-grid-view02-btn-secondary" href="{{ baseUrl('cases/view/'.$case->unique_id) }}">View</a>
                    @endif

                    @if(empty($case->professionalFavouriteCase))
                    <a class="CDSPostCaseNotifications-grid-view02-btn CDSPostCaseNotifications-grid-view02-btn-primary" data-href="{{ baseUrl('cases/mark-as-favourite/'.$case->unique_id) }}" onclick="confirmFavourite(this)" data-type="add">Favourite</a>
                    @else
                    <a class="CDSPostCaseNotifications-grid-view02-btn CDSPostCaseNotifications-grid-view02-btn-primary"data-href="{{ baseUrl('cases/mark-as-favourite/'.$case->unique_id) }}" onclick="confirmFavourite(this)" data-type="remove">Remove Favourite</a>
                    @endif
                </div>
            </div>
        </div>
      
    @endforeach
@endif

@if(!empty($records))
    @if($current_page > 2 &&  $current_page < $last_page)
        <div class="posting-case-more-link text-center">
            <a href="javascript:;" onclick="gridCaseData({{ $next_page }})" class="CdsTYButton-btn-primary">View More <i class="fa fa-chevron-down"></i></a>
        </div>
    @endif
@endif
