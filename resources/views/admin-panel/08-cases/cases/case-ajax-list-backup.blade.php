
@if (!empty($records) && $records->isNotEmpty())
    @foreach ($records as $case)
        <li class="CDSPostCaseNotifications-list-view02-feed-item {{ ($case_id != '' && $case->id == $case_id) ? 'CDSPostCaseNotifications-list-view02-new' : '' }}" data-case-id="{{ $case->unique_id ?? '' }}">
            <span class="CDSPostCaseNotifications-list-view02-timestamp">{{getTimeAgo($case->created_at ?? '' )}}</span>
            
            <div class="CDSPostCaseNotifications-list-view02-item-header">
                <div>
                    <h2 class="CDSPostCaseNotifications-list-view02-item-title">{{ $case->title ?? '' }}</h2>
                    <div class="CDSPostCaseNotifications-list-view02-item-meta">
                        <span class="CDSPostCaseNotifications-list-view02-meta-info">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{$case->userAdded->first_name ?? ''}} {{$case->userAdded->last_name ?? ''}}
                        </span>
                        <span class="CDSPostCaseNotifications-list-view02-meta-info">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{$case->subServices->name ?? ''}}
                        </span>
                        <span class="CDSPostCaseNotifications-list-view02-status-badge CDSPostCaseNotifications-list-view02-status-{{$case->status}}">
                            {{ $case->status ?? '' }}
                        </span>
                        @if($case->is_urgent == 1)
                            <span class="CDSPostCaseNotifications-list-view02-status-badge CDSPostCaseNotifications-list-view02-status-urgent">Urgent</span>
                        @endif
                        @if($case->is_time_constrained == 1 && $case->end_date != null)
                            <span class="CDSPostCaseNotifications-list-view02-status-badge CDSPostCaseNotifications-list-view02-status-featured">Time Constrained: {{ dateFormat($case->end_date) }}</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <p class="CDSPostCaseNotifications-list-view02-item-description">
                {!! html_entity_decode($case->description) !!}
            </p>
            
            <div class="CDSPostCaseNotifications-list-view02-item-footer">
                <div class="CDSPostCaseNotifications-list-view02-tags">
                    <span class="CDSPostCaseNotifications-list-view02-tag">{{$case->services->name}}</span>
                    <span class="CDSPostCaseNotifications-list-view02-tag">{{$case->subServices->name}}</span>
                    
                </div>
                
                <div class="CDSPostCaseNotifications-list-view02-item-actions">
                    <span class="CDSPostCaseNotifications-list-view02-proposals-count">{{count($case->submitProposal)}} proposals</span>
                    
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.cases',
                        'module' => 'professional-cases',
                        'action' => 'view'
                    ]))
                        <a class="CDSPostCaseNotifications-list-view02-action-btn" href="{{ baseUrl('cases/view/'.$case->unique_id) }}">View</a>
                    @endif
                    
                    @if(empty($case->professionalFavouriteCase))
                        <a class="CDSPostCaseNotifications-list-view02-action-btn CDSPostCaseNotifications-list-view02-primary" data-href="{{ baseUrl('cases/mark-as-favourite/'.$case->unique_id) }}" onclick="confirmFavourite(this)" data-type="add">Favourite</a>
                    @else
                        <a class="CDSPostCaseNotifications-list-view02-action-btn" data-href="{{ baseUrl('cases/mark-as-favourite/'.$case->unique_id) }}" onclick="confirmFavourite(this)" data-type="remove">Remove Favourite</a>
                    @endif
                </div>
            </div>
        </li>
    @endforeach
@endif

@if(!empty($records))
    @if($current_page > 2 &&  $current_page < $last_page)
        <div class="posting-case-more-link text-center">
            <a href="javascript:;" onclick="listCaseData({{ $next_page }})" class="CdsTYButton-btn-primary">View More <i class="fa fa-chevron-down"></i></a>
        </div>
    @endif
@endif
