 @if(!empty($records) || $records->isNotEmpty())
                                @foreach($records as $case)
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