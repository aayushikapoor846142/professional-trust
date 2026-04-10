@extends('admin-panel.layouts.app')
@if(checkPrivilege([
                        'route_prefix' => 'panel.cases',
                        'module' => 'professional-cases',
                        'action' => 'view'
                    ]))
                    @php
                    $canViewCases=true;
                    @endphp
@else
                    @php
                    $canViewCases=false;
                    @endphp
@endif
@php 
$page_arr = [
    'page_title' => 'Cases Overview ',
    'page_description' => 'View and manage your cases overview.',
    'page_type' => 'cases-overview',
    'canViewCases' => $canViewCases,
    'casesFeatureStatus' => $casesFeatureStatus ?? null,
];
@endphp
@section('page-submenu')
{!! pageSubMenu('cases',$page_arr) !!}
@endsection
@section('styles')
<!-- You can move the internal styles from the new HTML into a separate CSS file like assets/css/cases-overview.css and link here -->
<link rel="stylesheet" href="{{ url('assets/css/13-CDS-case-overview.css') }}">
@endsection

@section('content')
                
	<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
                @if(isset($casesFeatureStatus))
                    @if(!$canViewCases)
                        <div class="alert alert-danger mb-3">
                            <strong>⚠ Cases Management</strong><br>
                            {{ $casesFeatureStatus['message']  }}
                           
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                                <strong>⚠ Cases Management</strong><br>
                           
                            {{ $casesFeatureStatus['message'] }}
                        </div>
                    @endif
                @endif
                <!-- Header -->
        <div class="CdsCaseOverview-header">
            <h1 class="CdsCaseOverview-header-title mb-0">{{ $pageTitle }}</h1>
        </div>

                @if($canViewCases)
        <!-- Stats Grid -->
        <div class="CdsCaseOverview-stats-grid">
            <div class="CdsCaseOverview-stat-card">
                <div class="CdsCaseOverview-stat-icon">📁</div>
                <div class="CdsCaseOverview-stat-value">{{ countCase('all') }}</div>
                <div class="CdsCaseOverview-stat-label">Total Cases</div>
            </div>
            <div class="CdsCaseOverview-stat-card">
                <div class="CdsCaseOverview-stat-icon">🟢</div>
                <div class="CdsCaseOverview-stat-value">{{ countCase('open') }}</div>
                <div class="CdsCaseOverview-stat-label">Active Cases</div>
            </div>
            <div class="CdsCaseOverview-stat-card">
                <div class="CdsCaseOverview-stat-icon">📝</div>
                <div class="CdsCaseOverview-stat-value">{{ countCase('proposal_sent') }}</div>
                <div class="CdsCaseOverview-stat-label">Proposals Sent</div>
            </div>
            <div class="CdsCaseOverview-stat-card">
                <div class="CdsCaseOverview-stat-icon">🔔</div>
                <div class="CdsCaseOverview-stat-value">{{ countCase('unread_case') }}</div>
                <div class="CdsCaseOverview-stat-label">Unread Cases</div>
            </div>
            <div class="CdsCaseOverview-stat-card">
                <div class="CdsCaseOverview-stat-icon">👁️</div>
                <div class="CdsCaseOverview-stat-value">{{ countCase('viewed_case') }}</div>
                <div class="CdsCaseOverview-stat-label">Viewed Cases</div>
            </div>
            <div class="CdsCaseOverview-stat-card">
                <div class="CdsCaseOverview-stat-icon">⭐</div>
                <div class="CdsCaseOverview-stat-value">{{ countCase('favourite') }}</div>
                <div class="CdsCaseOverview-stat-label">Favourite Cases</div>
            </div>
        </div>

        <!-- Recent Post Cases -->
        <div class="CdsCaseOverview-table-section">
            <div class="CdsCaseOverview-section-header">
                <div class="CdsCaseOverview-section-title">
                    <div class="CdsCaseOverview-section-icon">📋</div>
                    <span>Recent Post Cases</span>
                </div>
                <a href="{{ baseUrl('cases') }}" class="CdsCaseOverview-btn CdsCaseOverview-btn-primary">View All</a>
            </div>
            <!-- div table -->
            <div class="cdsTYDashboard-table">
                <div class="cdsTYDashboard-table-wrapper">
                    <div class="cdsTYDashboard-table-header">
                        <div class="cdsTYDashboard-table-cell">Title</div>
                        <div class="cdsTYDashboard-table-cell">Client</div>
                        <div class="cdsTYDashboard-table-cell">Proposals</div>
                        <div class="cdsTYDashboard-table-cell">Status</div>
                        <div class="cdsTYDashboard-table-cell">Last Updated</div>
                    </div>
                    <div class="cdsTYDashboard-table-body" id="tableList">
                        @forelse($recentPostCases as $case)
                        <div class="cdsTYDashboard-table-row">                            
                            <div class="cdsTYDashboard-table-cell" data-label="Title">{{ $case->title }}</div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Client">{{ $case->userAdded->first_name ?? '-' }} {{ $case->userAdded->last_name ?? '' }}</div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Proposals">{{ $case->submitProposal->count() }}</div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Status">
                                <span class="CdsCaseOverview-status-badge {{ $case->status == 'posted' ? 'CdsCaseOverview-status-active' : 'CdsCaseOverview-status-pending' }}">
                                    {{ ucfirst($case->status) }}
                                </span>
                            </div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Last Updated">{{ getTimeAgo($case->updated_at) }}</div>                            
                        </div>
                        @empty
                        <div class="cdsTYDashboard-table-row"> 
                            <div class="cdsTYDashboard-table-cell" data-label="">No recent post cases found.</div>      
                        </div>
                        @endforelse
                    </div>  
                </div>
            </div>
            <!-- # div table -->  

           
        </div>

         

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <!-- Recent Case With Professionals -->
        <div class="CdsCaseOverview-table-section">
            <div class="CdsCaseOverview-section-header">
                <div class="CdsCaseOverview-section-title">
                    <div class="CdsCaseOverview-section-icon">👥</div>
                    <span>Recent Case With Professionals</span>
                </div>
                <a href="{{ baseUrl('case-with-professionals') }}" class="CdsCaseOverview-btn CdsCaseOverview-btn-primary">View All</a>
            </div>

            <!-- div table -->
            <div class="cdsTYDashboard-table">
                <div class="cdsTYDashboard-table-wrapper">
                    <div class="cdsTYDashboard-table-header">
                        <div class="cdsTYDashboard-table-cell">Case Title</div>
                        <div class="cdsTYDashboard-table-cell">Client</div>
                        <div class="cdsTYDashboard-table-cell">Professional</div>
                        <div class="cdsTYDashboard-table-cell">Status</div>
                        <div class="cdsTYDashboard-table-cell">Last Updated</div>
                    </div>
                    <div class="cdsTYDashboard-table-body" id="tableList">
                        @forelse($recentCaseWithProfessionals as $case)
                        <div class="cdsTYDashboard-table-row">                            
                            <div class="cdsTYDashboard-table-cell" data-label="Case Title">{{ $case->case_title }}</div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Client">{{ $case->client->first_name ?? '-' }} {{ $case->client->last_name ?? '' }}</div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Professional">{{ $case->professional->first_name ?? '-' }} {{ $case->professional->last_name ?? '' }}</div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Status">
                                <span class="CdsCaseOverview-status-badge {{ $case->status == 'in-progress' ? 'CdsCaseOverview-status-active' : 'CdsCaseOverview-status-pending' }}">
                                    {{ ucfirst($case->status) }}
                                </span>
                            </div>                            
                            <div class="cdsTYDashboard-table-cell" data-label="Last Updated">{{ getTimeAgo($case->updated_at) }}</div>                            
                        </div>
                        @empty
                        <div class="cdsTYDashboard-table-row"> 
                            <div class="cdsTYDashboard-table-cell" data-label="">No recent case with professionals found.</div>      
                        </div>
                        @endforelse
                    </div>  
                </div>
            </div>
           
        </div>
                @else
                    <div class="text-center text-muted">
                        <p>You don't have permission to view cases overview.</p>
                    </div>
                @endif
 
			</div>
	
	</div>
  </div>
</div>			

@endsection
