@if (!empty($records) && $records->isNotEmpty())
    @foreach ($records as $case)
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
        
        <div class="CdsTYDashboardCaselist-expanded-list-case-card">
            <div class="CdsTYDashboardCaselist-expanded-list-case-header">
                <div class="CdsTYDashboardCaselist-expanded-list-case-top">
                    <div class="CdsTYDashboardCaselist-expanded-list-case-main">
                        <div class="CdsTYDashboardCaselist-expanded-list-case-badge">
                            <span class="CdsTYDashboardCaselist-expanded-list-badge-dot"></span>
                            CASE #{{ $case->unique_id }}
                        </div>
                        <h3 class="CdsTYDashboardCaselist-expanded-list-case-title">{{ $case->case_title ?? 'Untitled Case' }}</h3>
                        <div class="CdsTYDashboardCaselist-expanded-list-case-service">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                            {{ $case->services->name ?? 'Service' }} → {{ $case->subServices->name ?? 'Sub Service' }}
                        </div>
                    </div>
                    <div class="CdsTYDashboardCaselist-expanded-list-priority-indicator CdsTYDashboardCaselist-expanded-list-{{ $priority }}">
                        <div class="CdsTYDashboardCaselist-expanded-list-priority-level">
                            <div class="CdsTYDashboardCaselist-expanded-list-priority-bar"></div>
                            <div class="CdsTYDashboardCaselist-expanded-list-priority-bar"></div>
                            <div class="CdsTYDashboardCaselist-expanded-list-priority-bar"></div>
                        </div>
                        <span class="CdsTYDashboardCaselist-expanded-list-priority-text">{{ ucfirst($priority) }}</span>
                    </div>
                </div>
            </div>

            <div class="CdsTYDashboardCaselist-expanded-list-team-section">
                <div class="CdsTYDashboardCaselist-expanded-list-team-container">
                    <div class="CdsTYDashboardCaselist-expanded-list-team-members">
                        @if($case->userAdded)
                        <div class="CdsTYDashboardCaselist-expanded-list-team-member">
                            <div class="cdsavatarMainDiv cdsSize40 cdsSize50 cdsSize60">
                                {!! getProfileImage($case->userAdded->unique_id) !!}
                            </div>
                            <div class="CdsTYDashboardCaselist-expanded-list-member-info">
                                <span class="CdsTYDashboardCaselist-expanded-list-member-role">Client</span>
                                <span class="CdsTYDashboardCaselist-expanded-list-member-name">
                                    {{ $case->userAdded->first_name }} {{ $case->userAdded->last_name }}
                                </span>
                            </div>
                        </div>
                        @endif
                        @if($case->assignedStaff)
                            @foreach($case->assignedStaff as $value)
                                @if($value->Staff != '' && $value->Staff != null)
                                    <div class="CdsTYDashboardCaselist-expanded-list-team-member">
                                        <div class="CdsTYDashboardCaselist-expanded-list-member-avatar CdsTYDashboardCaselist-expanded-list-avatar-purple">
                                            {!! getProfileImage($value->Staff->unique_id) !!}
                                        </div>
                                        <div class="CdsTYDashboardCaselist-expanded-list-member-info">
                                            <span class="CdsTYDashboardCaselist-expanded-list-member-role">{{ $value->Staff->role }}</span>
                                            <span class="CdsTYDashboardCaselist-expanded-list-member-name">
                                                {{ $value->Staff->first_name }} {{ $value->Staff->last_name }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        
                       
                    </div>
                    <div class="CdsTYDashboardCaselist-expanded-list-team-info">
                        <div class="CdsTYDashboardCaselist-expanded-list-info-item">
                            <span class="CdsTYDashboardCaselist-expanded-list-info-label">Posted</span>
                            <span class="CdsTYDashboardCaselist-expanded-list-info-value">
                                {{ $case->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="CdsTYDashboardCaselist-expanded-list-progress-section">
                <div class="CdsTYDashboardCaselist-expanded-list-progress-content">
                    <div class="CdsTYDashboardCaselist-expanded-list-progress-header">
                        @if($totalStages != 0)
                        <div class="CdsTYDashboardCaselist-expanded-list-stage-info">
                            Stage <span class="CdsTYDashboardCaselist-expanded-list-stage-current">{{ $currentStage }} of {{ $totalStages }}</span> - {{ $stageName }}
                        </div>
                        <div class="CdsTYDashboardCaselist-expanded-list-percentage">{{ $progressPercent }}%</div>
                        @else
                        <div class="CdsTYDashboardCaselist-expanded-list-stage-info">
                           {{ $stageName }}
                        </div>
                        @endif
                    </div>
                    <div class="CdsTYDashboardCaselist-expanded-list-progress-bar">
                        <div class="CdsTYDashboardCaselist-expanded-list-progress-fill" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <div class="CdsTYDashboardCaselist-expanded-list-stage-name">
                        Status: {{ ucfirst(str_replace('_', ' ', $case->status ?? 'pending')) }}
                    </div>
                </div>
                <div class="CdsTYDashboardCaselist-expanded-list-case-actions">
                    @if(auth()->user()->role == "professional")
                    <button class="CdsTYDashboardCaselist-expanded-list-action-btn CdsTYDashboardCaselist-expanded-list-btn-icon" 
                            onclick="showPopup('{{ baseUrl('case-with-professionals/assign-to-staff/'.$case->unique_id) }}')"
                            title="Assign to Staff">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" y1="8" x2="19" y2="14"></line>
                            <line x1="22" y1="11" x2="16" y2="11"></line>
                        </svg>
                    </button>
                    @endif
                    
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.case-with-professionals',
                        'module' => 'professional-case-with-professionals',
                        'action' => 'view'
                    ]))
                    <a href="{{ baseUrl('case-with-professionals/view/'.$case->unique_id) }}" 
                       class="CdsTYDashboardCaselist-expanded-list-action-btn CdsTYDashboardCaselist-expanded-list-btn-primary">
                        View Details
                    </a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
    
    @if(!empty($records) && $current_page > 2 && $current_page < $last_page)
    <div class="my-cases-view-more-link text-center mt-4">
        <a href="javascript:;" onclick="loadData({{ $next_page }})" class="CdsTYButton-btn-primary">
            View More <i class="fa fa-chevron-down"></i>
        </a>
    </div>
    @endif
@else
    <div class="text-center text-danger mt-5">
        <i class="fa fa-folder-open fa-3x mb-3 text-muted"></i>
        <p>No cases found.</p>
    </div>
@endif