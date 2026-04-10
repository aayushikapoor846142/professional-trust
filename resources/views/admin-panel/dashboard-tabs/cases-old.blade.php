@extends('admin-panel.layouts.app')

@section('content')
@include('admin-panel.dashboard-tabs.common.dashboard-nav', ['activeTab' => 'cases'])
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header-dashboard">
 <!-- Summary Cards -->
    <div class="cdsTYDashboard-main-summary-cards">
        <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('cases')">
            <div class="cdsTYDashboard-main-card-header">
                <div class="cdsTYDashboard-main-card-info">
                    <div class="cdsTYDashboard-main-card-label">Total Cases</div>
                    <div class="cdsTYDashboard-main-card-value" data-count="{{ countCase('all') ?? 0 }}">{{ countCase('all') }}</div>
                </div>
                <div class="cdsTYDashboard-main-card-icon" style="background: rgba(94, 114, 228, 0.1); color: #5e72e4;">
                    📁
                </div>
            </div>
            <div class="cdsTYDashboard-main-card-footer">
                <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                    <span>↑</span>
                    <span>12%</span>
                </div>
                <span style="color: #6b7280;">vs last month</span>
            </div>
        </div>

        <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('appointments')">
            <div class="cdsTYDashboard-main-card-header">
                <div class="cdsTYDashboard-main-card-info">
                    <div class="cdsTYDashboard-main-card-label">Active Cases</div>
                    <div class="cdsTYDashboard-main-card-value" data-count="{{ count($open ?? []) }}">{{ countCase('open') }}</div>
                </div>
                <div class="cdsTYDashboard-main-card-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    📅
                </div>
            </div>
            <div class="cdsTYDashboard-main-card-footer">
                @if(count($open ?? []) > 0)
                    <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                        <span>↑</span>
                        <span>{{ count($open ?? []) }}</span>
                    </div>
                    <span style="color: #6b7280;">upcoming</span>
                @else
                    <span style="color: #6b7280;">No upcoming appointments</span>
                @endif
            </div>
        </div>

        <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('messages')">
            <div class="cdsTYDashboard-main-card-header">
                <div class="cdsTYDashboard-main-card-info">
                    <div class="cdsTYDashboard-main-card-label">Proposal Sent</div>
                    <div class="cdsTYDashboard-main-card-value" data-count="{{countCase('proposal_sent') }} ?? 0 }}">0</div>
                </div>
                <div class="cdsTYDashboard-main-card-icon" style="background: rgba(251, 146, 60, 0.1); color: #fb923c;">
                    💌
                </div>
            </div>
            <div class="cdsTYDashboard-main-card-footer">
                @if((countCase('proposal_sent') ?? 0) > 0)
                    <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                        <span>↑</span>
                        <span>{{ countCase('proposal_sent') ?? 0 }}</span>
                    </div>
                    <span style="color: #6b7280;">unread</span>
                @else
                    <span style="color: #6b7280;">All caught up!</span>
                @endif
            </div>
        </div>

        <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
            <div class="cdsTYDashboard-main-card-header">
                <div class="cdsTYDashboard-main-card-info">
                    <div class="cdsTYDashboard-main-card-label">Unread Cases</div>
                    <div class="cdsTYDashboard-main-card-value" data-count="{{ countCase('unread_case') ?? 0 }}">{{ countCase('unread_case') }}</div>
                </div>
                <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    💳
                </div>
            </div>
            <div class="cdsTYDashboard-main-card-footer">
                @if((countCase('unread_case') ?? 0) > 0)
                    <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                        <span>↑</span>
                        <span>{{ countCase('unread_case') ?? 0 }}</span>
                    </div>
                    <span style="color: #6b7280;">pending</span>
                @else
                    <span style="color: #6b7280;">All invoices cleared</span>
                @endif
            </div>
        </div> <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
            <div class="cdsTYDashboard-main-card-header">
                <div class="cdsTYDashboard-main-card-info">
                    <div class="cdsTYDashboard-main-card-label">Viewed Cases</div>
                    <div class="cdsTYDashboard-main-card-value" data-count="{{ countCase('viewed_case') ?? 0 }}">{{ countCase('viewed_case') }}</div>
                </div>
                <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    💳
                </div>
            </div>
            <div class="cdsTYDashboard-main-card-footer">
                @if((countCase('viewed_case') ?? 0) > 0)
                    <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                        <span>↑</span>
                        <span>{{ countCase('viewed_case') ?? 0 }}</span>
                    </div>
                    <span style="color: #6b7280;">pending</span>
                @else
                    <span style="color: #6b7280;">All invoices cleared</span>
                @endif
            </div>
        </div><div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
            <div class="cdsTYDashboard-main-card-header">
                <div class="cdsTYDashboard-main-card-info">
                    <div class="cdsTYDashboard-main-card-label">Favourite</div>
                    <div class="cdsTYDashboard-main-card-value" data-count="{{ $favourite ?? 0 }}">0</div>
                </div>
                <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    💳
                </div>
            </div>
            <div class="cdsTYDashboard-main-card-footer">
                @if(($favourite ?? 0) > 0)
                    <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                        <span>↑</span>
                        <span>{{ $favourite ?? 0 }}</span>
                    </div>
                    <span style="color: #6b7280;">pending</span>
                @else
                    <span style="color: #6b7280;">All invoices cleared</span>
                @endif
            </div>
        </div>
    </div>

       
  

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 
   <!-- Tables Section -->
    <div class="cdsTYDashboard-main-tables-grid">
        <div class="cdsTYDashboard-main-table-card">
            <div class="cdsTYDashboard-main-table-header">
                <h3 class="cdsTYDashboard-main-table-title">Recent Live Postings</h3>
                <a href="{{ baseUrl('cases') }}" class="cdsTYDashboard-main-view-all">View all →</a>  
            </div>
            <div class="cdsTYDashboard-recent-postings-case-list">
               @forelse($recentPostCases as $case)
                   
				   
				   
				   <div class="cdsTYDashboard-recent-postings-case-segment">              <div class="cdsTYDashboard-recent-postings-case-segment-inner">
				     <div class="cdsTYDashboard-recent-postings-case-segment-inner-header"> 
                           {{ $case->title }}</div> 

                       <div class="cdsTYDashboard-recent-postings-case-segment-inner-body">      
                            <div class="cdsTYDashboard-recent-postings-case-segment-inner-body-cell" data-label="Client">
							<span>Posted By</span>
							{{ $case->userAdded->first_name ?? '-' }} {{ $case->userAdded->last_name ?? '' }}</div>                            
                            <div class="cdsTYDashboard-recent-postings-case-segment-inner-body-cell" data-label="Proposals">
							<span>Proposals</span>
							{{ $case->submitProposal->count() ?? 0 }}</div>                            
                            <div class="cdsTYDashboard-recent-postings-case-segment-inner-body-cell" data-label="Status">
                                <span class="cdsTYDashboard-status-badge cdsTYDashboard-status-{{ strtolower($case->status) }}">{{ ucfirst($case->status) }}</span>
                            </div>                            
                            <div class="cdsTYDashboard-recent-postings-case-segment-inner-body-cell" data-label="Last Updated"><span>Date</span>
							{{ \Carbon\Carbon::parse($case->updated_at)->diffForHumans() }}</div>                            
                        </div>
						</div>
					   </div>
				
					
                @empty
                    <div class="cdsTYDashboard-main-table-row">
                        <div class="cdsTYDashboard-main-row-content">
                            <div class="cdsTYDashboard-main-row-icon" style="background: #f3f4f6;">
                                📁
                            </div>
                            <div class="cdsTYDashboard-main-row-info">
                                <div class="cdsTYDashboard-main-row-title">No recent cases</div>
                                <div class="cdsTYDashboard-main-row-subtitle">Start by creating your first case</div>
                            </div>
                        </div>
                        <span class="cdsTYDashboard-main-row-status cdsTYDashboard-main-status-draft">Empty</span>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="cdsTYDashboard-main-table-card">
            <div class="cdsTYDashboard-main-table-header">
                <h3 class="cdsTYDashboard-main-table-title">Awarded cases</h3>
                <a href="{{ baseUrl('case-with-professionals') }}" class="cdsTYDashboard-main-view-all">View all →</a>
            </div>
            <div class="cdsTYDashboard-main-table-list">
               @forelse($recentCaseWithProfessionals as $caseWithPro)
                     <div class="cdsTYDashboard-table-body" id="tableList">
                        @forelse($recentCaseWithProfessionals as $caseWithPro)
                        <div class="cdsTYDashboard-table-row">                            
                            <div class="cdsTYDashboard-table-cell" data-label="Case Title">{{ $caseWithPro->case_title ?? 'N/A' }}</div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Client">{{ $caseWithPro->client->first_name ?? '-' }} {{ $caseWithPro->client->last_name ?? '' }}</div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Professional">{{ $caseWithPro->professional->first_name ?? '-' }} {{ $caseWithPro->professional->last_name ?? '' }}</div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Status">
                                <span class="cdsTYDashboard-status-badge cdsTYDashboard-status-{{ strtolower($caseWithPro->status ?? 'pending') }}">{{ ucfirst($caseWithPro->status ?? 'Pending') }}</span>
                            </div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Last Updated">{{ \Carbon\Carbon::parse($caseWithPro->updated_at)->diffForHumans() }}</div>                            
                        </div>
                        @empty
                        <div class="cdsTYDashboard-table-row">
                            <div class="cdsTYDashboard-table-cell" data-label="No cases found" style="text-align: center; grid-column: 1 / -1;">
                                No recent cases with professionals found.
                            </div>
                        </div>
                        @endforelse
                    </div>
            
                @empty
                    <div class="cdsTYDashboard-main-table-row">
                        <div class="cdsTYDashboard-main-row-content">
                            <div class="cdsTYDashboard-main-row-icon" style="background: #fef3c7;">
                                📄
                            </div>
                            <div class="cdsTYDashboard-main-row-info">
                                <div class="cdsTYDashboard-main-row-title">No recent invoices</div>
                                <div class="cdsTYDashboard-main-row-subtitle">Create your first invoice</div>
                            </div>
                        </div>
                        <span class="cdsTYDashboard-main-row-amount">₹0.00</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
	
 
 
 
 
 
 
 
 
 
 
 
 

			</div>
	
	</div>
  </div>
</div>

@endsection

@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/13-CDS-case-overview.css') }}">
@endsection

@section('javascript')
@include('admin-panel.dashboard-tabs.common.dashboard-scripts')
@endsection

