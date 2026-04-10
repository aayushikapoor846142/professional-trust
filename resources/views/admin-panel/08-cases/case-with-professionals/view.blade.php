{{-- Modern Overview Page for Case With Professionals --}}
@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')

@section('case-container')
@php 
    $case_record = caseInfo($case_id);
@endphp

 <div class="CdsCaseOverview-container">
        <!-- Tab Navigation -->
      

        <!-- Stats Grid -->
        <div class="CdsCaseOverview-stats-grid">
            <div class="CdsCaseOverview-stat-card">
                <div class="CdsCaseOverview-stat-value">{{ $case_record->totalCaseStage->count()}}</div>
                <div class="CdsCaseOverview-stat-label">Total Stages</div>
            </div>
            <div class="CdsCaseOverview-stat-card">
                <div class="CdsCaseOverview-stat-value">{{ $total_requests }}</div>
                <div class="CdsCaseOverview-stat-label">Total Requests</div>
            </div>
            <div class="CdsCaseOverview-stat-card">
                <div class="CdsCaseOverview-stat-value">{{ $case_record->totalCaseStage->count() ?? 0 }}</div>
                <div class="CdsCaseOverview-stat-label">Stages</div>
            </div>
            <div class="CdsCaseOverview-stat-card">
                <div class="CdsCaseOverview-stat-value">{{ $case_record->caseFiles->count() ?? 0 }}</div>
                <div class="CdsCaseOverview-stat-label">Documents</div>
            </div>
            <div class="CdsCaseOverview-stat-card CdsCaseOverview-success">
                @if($case_record->retainAgreements)
                    <div class="CdsCaseOverview-success-icon">✓</div>
                    <div class="CdsCaseOverview-stat-label">Retain Agreement<br>Added</div>
                @else
                    <div class="CdsCaseOverview-success-icon">✓</div>
                    <div class="CdsCaseOverview-stat-label">Retain Agreement<br>Not Added</div>
                @endif
            </div>
        </div>

        <!-- Content Grid -->
        <div class="CdsCaseOverview-content-grid">
            <div class="CdsCaseOverview-content-section">
                <h3 class="CdsCaseOverview-section-header">Recent 5 Stages</h3>
                @forelse($case_record->totalCaseStage->sortByDesc('id')->take(5) as $stage)
                <div class="CdsCaseOverview-item-list">
                    <div class="CdsCaseOverview-list-item">
                        <span>{{ $stage->name ?? 'N/A' }}</span>
                        <span class="CdsCaseOverview-status-badge">{{ $stage->status ?? '-' }}</span>
                    </div>
                </div>
                @empty
                    <div class="CdsCaseOverview-item-list">
                        <div class="CdsCaseOverview-list-item">
                            No stages found.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="CdsCaseOverview-content-section">
                <h3 class="CdsCaseOverview-section-header">Recent 5 Requests</h3>
                @forelse($recent_requests as $request)
                    <div class="CdsCaseOverview-item-list">
                        <div class="CdsCaseOverview-list-item">
                            <span>{{ $request->title ?? 'N/A' }}</span>
                            <span class="CdsCaseOverview-status-badge CdsCaseOverview-pending">{{ $request->status ?? '-' }}</span>
                        </div>
                    </div>
                 @empty
                    <div class="CdsCaseOverview-item-list">
                        <div class="CdsCaseOverview-list-item">
                            No requests found.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Messages Section -->
        <div class="CdsCaseOverview-content-section" style="margin-bottom: 32px;">
            <h3 class="CdsCaseOverview-section-header">Recent 5 Messages (Case Group)</h3>
            @forelse($recent_messages as $msg)
                <div class="CdsCaseOverview-message-item">
                    <div class="CdsCaseOverview-message-header">
                        <span class="CdsCaseOverview-message-sender">{{ $msg->sentBy->first_name ?? 'Unknown' }} {{ $msg->sentBy->last_name ?? 'Unknown' }}</span>
                        <span class="CdsCaseOverview-message-time">{{ getTimeAgo($msg->created_at) }}</span>
                    </div>
                    <div class="CdsCaseOverview-message-content">{{ $msg->message }}</div>
                </div>
            @empty
                <span class="list-group-item">No messages found.</span>
            @endforelse
        </div>

        <!-- Case Details -->
        <div class="CdsCaseOverview-case-details">
            <div class="CdsCaseOverview-case-header">
                <div class="CdsCaseOverview-case-title-wrapper">
                    <h1 class="CdsCaseOverview-case-title mb-0">{{ $case_record->case_title ?? 'Untitled Case' }}</h1>
                    <span class="CdsCaseOverview-draft-badge">{{ $case_record->status ?? 'Status Unknown' }}</span>
                </div>
                {{--<button class="CdsCaseOverview-btn CdsCaseOverview-btn-secondary" onclick="cdsCaseOverviewSendMessage()">Message</button>--}}
            </div>

            <div class="CdsCaseOverview-details-grid">
                <div class="CdsCaseOverview-detail-item">
                    <div class="CdsCaseOverview-detail-label">Main Service</div>
                    <div class="CdsCaseOverview-detail-value">{{ $case_record->services->name ?? '-' }}</div>
                </div>
                <div class="CdsCaseOverview-detail-item">
                    <div class="CdsCaseOverview-detail-label">Sub Service</div>
                    <div class="CdsCaseOverview-detail-value">{{ $case_record->subServices->name ?? '-' }}</div>
                </div>
                <div class="CdsCaseOverview-detail-item">
                    <div class="CdsCaseOverview-detail-label">Requested On</div>
                    <div class="CdsCaseOverview-detail-value">{{ getTimeAgo($case_record->created_at) }}</div>
                </div>
                <div class="CdsCaseOverview-detail-item">
                    <div class="CdsCaseOverview-detail-label">Client</div>
                    <div class="CdsCaseOverview-detail-value">{{$case_record->clients->first_name}} {{$case_record->clients->last_name}}</div>
                </div>
                <div class="CdsCaseOverview-detail-item" style="grid-column: span 2;">
                    <div class="CdsCaseOverview-detail-label">Case Brief</div>
                    <div class="CdsCaseOverview-detail-value">
                        {!! html_entity_decode($case_record->case_description ?? '') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection