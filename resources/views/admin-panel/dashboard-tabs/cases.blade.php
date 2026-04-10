 <!-- cases dashboard -->
     <div class="cdsTYDashboard-integrated-container-component" data-container-id="1">
        <div class="cdsTYDashboard-integrated-container-header">
            <div class="cdsTYDashboard-integrated-header-left">
                <h1 class="cdsTYDashboard-integrated-container-title">Case Overview</h1>
            </div>
            <div class="cdsTYDashboard-integrated-header-controls">
                <button class="cdsTYDashboard-integrated-sidebar-toggle" aria-label="Toggle Sidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
                <button class="cdsTYDashboard-integrated-minimize-btn" aria-label="Minimize Container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 15l7-7 7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    

                    <section id="overview" class="cdsTYDashboard-integrated-section-header">
                        <!-- <h2>Case Overview</h2> -->
                        <p>Welcome to your minimalist dashboard. You can collapse the sidebar using the arrow button for more screen space, or minimize entire containers using the chevron button in the header.</p>
                        
                        <!-- Summary Cards -->
                        <div class="cdsTYDashboard-main-summary-cards">
                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('cases')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Total Cases</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countCase('all') }}">{{ countCase('all') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(94, 114, 228, 0.1); color: #5e72e4;">
                                        📁
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                        <span>↑</span>
                                        <span>12%</span>
                                    </div>
                                    <span style="color: #6b7280;">vs last month</span>
                                </div>--}}
                            </div>

                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('appointments')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Active Cases</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countCase('open') }}">{{ countCase('open') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                        📅
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(countCase('open') > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ countCase('open') ?? [] }}</span>
                                        </div>
                                        <span style="color: #6b7280;">upcoming</span>
                                    @else
                                        <span style="color: #6b7280;">No upcoming active cases</span>
                                    @endif
                                </div>--}}
                            </div>

                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('messages')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Proposals Sent</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countCase('proposal_sent') }}">{{ countCase('proposal_sent') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(251, 146, 60, 0.1); color: #fb923c;">
                                        💌
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($unreadMessages ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $unreadMessages ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">unread</span>
                                    @else
                                        <span style="color: #6b7280;">All caught up!</span>
                                    @endif
                                </div>--}}
                            </div>

                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Unread Cases</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countCase('unread_case') }}">{{ countCase('unread_case') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        💳
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($pendingInvoices ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $pendingInvoices ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">pending</span>
                                    @else
                                        <span style="color: #6b7280;">All invoices cleared</span>
                                    @endif
                                </div>--}}
                            </div>
                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Viewed Cases</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countCase('viewed_case') }}">{{ countCase('viewed_case') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        💳
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($pendingInvoices ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $pendingInvoices ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">pending</span>
                                    @else
                                        <span style="color: #6b7280;">All invoices cleared</span>
                                    @endif
                                </div>--}}
                            </div>
                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Favourite Cases</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ countCase('favourite') }}">{{ countCase('favourite') }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        💳
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($pendingInvoices ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $pendingInvoices ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">pending</span>
                                    @else
                                        <span style="color: #6b7280;">All invoices cleared</span>
                                    @endif
                                </div>--}}
                            </div>
                        </div>

                    </section>
                  
                    <section id="all"  class="cdsTYDashboard-integrated-section-header">
                        <h3 class="cdsTYDashboard-main-table-title">Recent Case</h3>
                        <div class="CDSPostCaseNotifications-compact-list-container">
                            <div class="CDSPostCaseNotifications-compact-list-header">
                                <div class="CDSPostCaseNotifications-compact-list-header-item">Case Details</div>
                                <div class="CDSPostCaseNotifications-compact-list-header-item">Status</div>
                                <div class="CDSPostCaseNotifications-compact-list-header-item">Client</div>
                                <div class="CDSPostCaseNotifications-compact-list-header-item">Proposals</div>
                                <div class="CDSPostCaseNotifications-compact-list-header-item">Actions</div>
                            </div>

                            <div class="CDSPostCaseNotifications-compact-list-case-list">
                                @if(!empty($cases))
                                <!-- Case 1 -->
                                    @foreach($cases as $case)
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
                            </div>
                        </div>

                    </section>
                    <!-- end recent cases -->
                    <!-- end case with professional -->
                    <div class="CdsCompactCaseList-container">
                         <h3 class="cdsTYDashboard-main-table-title">Recent Case With Professionals</h3>
                        <div class="CdsCompactCaseList-header">
                            <div class="CdsCompactCaseList-header-item CdsCompactCaseList-case-info">Case Information</div>
                            <div class="CdsCompactCaseList-header-item CdsCompactCaseList-team">Team & Client</div>
                            <div class="CdsCompactCaseList-header-item CdsCompactCaseList-progress">Progress</div>
                            <div class="CdsCompactCaseList-header-item CdsCompactCaseList-posted">Posted</div>
                            <div class="CdsCompactCaseList-header-item CdsCompactCaseList-actions">Actions</div>
                        </div>

                        <div class="CdsCompactCaseList-list">
                            @if(!empty($caseWithProfessionals) || $caseWithProfessionals->isNotEmpty())
                                @foreach($caseWithProfessionals as $case)
                                    @php
                                        // Determine priority based on case data
                                        $priority = 'medium'; // Default
                                        if($case->is_urgent) {
                                            $priority = 'high';
                                        } elseif($case->status == 'completed') {
                                            $priority = 'low';
                                        }
                                        
                                        // Calculate progress percentage
                                        $totalStages = 0; // Adjust based on your case stages
                                        $currentStage = 0; // You'll need to determine this from your case data
                                        
                                        
                                        // Get stage name based on status
                                        $progressPercent = 0;
                                        if($case->totalCaseStage->isNotEmpty()){
                                                $stageName = 'Initial Assessment';
                                            if(count($case->totalCaseStage) == count($case->completedCaseStage)){
                                                $stageName = 'Initial Assessment [Completed]';
                                            }
                                            
                                            $totalStages = count($case->totalCaseStage);
                                            if($case->completedCaseStage->isNotEmpty()){
                                                $currentStage = count($case->completedCaseStage);
                                            }
                                            
                                        }else{
                                            $stageName = 'No stages added';
                                        }
                                        if($totalStages != 0){
                                            $progressPercent = round(($currentStage / $totalStages) * 100);
                                        }
                                        
                                        
                                        // Generate avatar initials
                                        $caseManagerInitials = '';
                                        if($case->assignedTo) {
                                            $caseManagerInitials = strtoupper(substr($case->assignedTo->first_name, 0, 1) . substr($case->assignedTo->last_name, 0, 1));
                                        }
                                        
                                        $clientInitials = '';
                                        if($case->userAdded) {
                                            $clientInitials = strtoupper(substr($case->userAdded->first_name, 0, 1) . substr($case->userAdded->last_name, 0, 1));
                                        }
                                        
                                    @endphp
        
                                    <!-- Case 1 -->
                                    <div class="CdsCompactCaseList-item">
                                        <div class="CdsCompactCaseList-case-info">
                                            <div class="CdsCompactCaseList-case-number">
                                                <svg class="CdsCompactCaseList-case-number-icon" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd"/>
                                                </svg>
                                                CASE #{{$case->unique_id}}
                                                @if($priority == 'low')
                                                    <span class="CdsCompactCaseList-priority-badge CdsCompactCaseList-medium">Medium</span>
                                                @else
                                                    <span class="CdsCompactCaseList-priority-badge CdsCompactCaseList-high">High</span>
                                                @endif
                                            </div>
                                            <div class="CdsCompactCaseList-case-title">{{ $case->case_title ?? 'Untitled Case' }}</div>
                                            <div class="CdsCompactCaseList-case-pathway">
                                                <svg class="CdsCompactCaseList-pathway-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                {{ $case->services->name ?? 'Service' }} → {{ $case->subServices->name ?? 'Sub Service' }}
                                            </div>
                                        </div>
                                        <div class="CdsCompactCaseList-team">
                                            @if($case->assignedStaff)
                                                <div class="CdsCompactCaseList-team-group">
                                                    <div class="CdsCompactCaseList-team-label">Team</div>
                                                    <div class="CdsCompactCaseList-team-content">
                                                        <div class="CdsCompactCaseList-avatar-group">
                                                            <div class="CdsCompactCaseList-avatar CdsCompactCaseList-manager"> {!! getProfileImage($case-> professional->unique_id) !!}</div>
                                                            @foreach($case->assignedStaff->take(1) as $value)
                                                                @if($value->Staff != '' && $value->Staff != null)
                                                                    <div class="CdsCompactCaseList-avatar CdsCompactCaseList-manager"> {!! getProfileImage($value->Staff->unique_id) !!}</div>
                                                                  
                                                                @endif
                                                            @endforeach
                                                            @if(count($case->assignedStaff) > 1)
                                                            <div class="CdsCompactCaseList-avatar CdsCompactCaseList-more">+1</div>
                                                            @endif
                                                        </div>
                                                        <div class="CdsCompactCaseList-team-name">{{$case-> professional->first_name.''.$case-> professional->last_name}}</div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="CdsCompactCaseList-team-group">
                                                <div class="CdsCompactCaseList-team-label">Client</div>
                                                @if($case->userAdded)
                                                <div class="CdsCompactCaseList-client-info">
                                                    <div class="CdsCompactCaseList-avatar CdsCompactCaseList-client">{!! getProfileImage($case-> userAdded->unique_id) !!}</div>
                                                    <div class="CdsCompactCaseList-client-name"> {{ $case->userAdded->first_name }} {{ $case->userAdded->last_name }}</div>
                                                </div>
                                                @else
                                                    - 
                                                @endif
                                            </div>
                                        </div>
                                        <div class="CdsCompactCaseList-progress">
                                            @if($totalStages != 0)
                                                <div class="CdsCompactCaseList-stage-info">
                                                    <span class="CdsCompactCaseList-stage-text">Stage {{ $currentStage }} of {{ $totalStages }} - {{ $stageName }}</span>
                                                    <span class="CdsCompactCaseList-stage-percent">{{ $progressPercent }}%</span>
                                                </div>
                                                <div class="CdsCompactCaseList-progress-bar">
                                                    <div class="CdsCompactCaseList-progress-fill" style="width: {{ $progressPercent }}%"></div>
                                                </div>
                                                <div class="CdsCompactCaseList-status-text">All documents submitted, pending review</div>
                                            @else
                                                <div class="CdsCompactCaseList-stage-info">
                                                  No stages added
                                                </div>
                                            @endif
                                        </div>
                                        <div class="CdsCompactCaseList-posted"> {{ $case->created_at->format('M d, Y') }}</div>
                                        <div class="CdsCompactCaseList-actions">
                                            <a href="{{baseUrl('/case-with-professionals/view/'.$case->unique_id)}}" class="CdsCompactCaseList-btn">
                                                View Details
                                                <svg class="CdsCompactCaseList-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                No Cases
                            @endif
                        </div>
                    </div>

                    <script>
        function alignHeaderWithContent() {
            const container = document.querySelector('.CdsCompactCaseList-container');
            const firstItem = document.querySelector('.CdsCompactCaseList-item');
            const header = document.querySelector('.CdsCompactCaseList-header');
            const headerItems = document.querySelectorAll('.CdsCompactCaseList-header-item');
            
            if (!firstItem || !header || headerItems.length === 0) return;
            
            // Only align on desktop view
            if (window.innerWidth <= 1200) {
                headerItems.forEach(item => {
                    item.style.width = '';
                    item.style.flex = '';
                });
                return;
            }
            
            // Get all the main sections in the list item
            const sections = [
                firstItem.querySelector('.CdsCompactCaseList-case-info'),
                firstItem.querySelector('.CdsCompactCaseList-team'),
                firstItem.querySelector('.CdsCompactCaseList-progress'),
                firstItem.querySelector('.CdsCompactCaseList-posted'),
                firstItem.querySelector('.CdsCompactCaseList-actions')
            ];
            
            // Get computed widths of each section
            const sectionWidths = sections.map(section => {
                if (section) {
                    return section.getBoundingClientRect().width;
                }
                return 0;
            });
            
            // Apply exact widths to header items
            headerItems.forEach((header, index) => {
                if (sectionWidths[index] > 0) {
                    header.style.flex = 'none';
                    header.style.width = sectionWidths[index] + 'px';
                }
            });
        }
        
        // Debounce function for resize events
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Run alignment with proper timing
        document.addEventListener('DOMContentLoaded', () => {
            alignHeaderWithContent();
            
            // Re-run after fonts load
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(() => {
                    alignHeaderWithContent();
                });
            }
        });
        
        // Handle window resize with debouncing
        window.addEventListener('resize', debounce(() => {
            alignHeaderWithContent();
        }, 250));
        
        // Fallback alignment after everything loads
        window.addEventListener('load', () => {
            setTimeout(alignHeaderWithContent, 100);
        });
    </script>