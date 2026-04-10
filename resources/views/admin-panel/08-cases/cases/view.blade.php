@extends('admin-panel.layouts.app')
@section('styles')
<link href="{{ url('assets/css/20-CDS-cases-detail.css') }}" rel="stylesheet" />
@endsection
@section('content')
 <div class="container my-4">
        <!-- Header Bar -->
        <div class="CDSPostCaseDetail-header-bar">
            {{--<div class="CDSPostCaseDetail-breadcrumb">
                <a href="#">Home</a>
                <span>›</span>
                <a href="#">Cases</a>
                <span>›</span>
                <span>{{$record->title}}</span>
            </div>--}}
            <div class="CDSPostCaseDetail-header-actions ms-auto">
                <a href="{{baseUrl('/cases')}}" class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-secondary" >
                    <span>←</span>
                    <span>Back to List</span>
                </a>
                @if(!$case_history)
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.cases',
                        'module' => 'professional-professional-cases',
                        'action' => 'submit-praposal'
                    ]))
                    @if($canAddProposal)
                   @if($quotations->isNotEmpty() && !empty($sub_services))
                    <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-primary" onclick="submitProposal()">
                        <span>📝</span>
                        <span>Submit Proposal </span>
                    </button>
                    @endif
                    @endif
                    @endif

                       @if(checkPrivilege([
                            'route_prefix' => 'panel.message-centre',
                            'module' => 'professional-message-centre',
                            'action' => 'list'
                        ]))
                    <a href="javascript:;" onclick="proposalMessage(this)" data-href="{{baseUrl('cases/create-group/'.$record->unique_id)}}" class="CDSProposals-btn CDSProposals-btn-message">Message</a>
                    @endif
                    @if(!empty($allProposal))
                        <a class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-danger" data-href="{{ baseUrl('/cases/withdraw-proposal/'.$record->unique_id) }}" onclick="withDrawProposal(this)" >
                            <span>📝</span>
                            <span>Withdraw Proposal</span>
                        </a>
                    @endif
                @endif
            </div>
        </div>
        @if($quotations->isEmpty())
            <div class="alert alert-danger">
                No quotation is available for the service. Please add quotation. <a href="{{ baseUrl('/quotations/add') }}">Click Here</a>
            </div>
        @endif
        @if(empty($sub_services))
            <div class="alert alert-danger">
                Please add sub service types for this services. 
                <a href="{{ baseUrl('/manage-services') }}">Click Here</a>
            </div>
        @endif
        <!-- Proposal Limit Alert Box -->
        @if(!$canAddProposal)
            <div class="alert alert-danger mb-3">
                <strong>⚠ Proposal Management</strong><br>
                {{ $proposalFeatureStatus['message'] }}
            </div>
        @else
            <div class="alert alert-info alert-warning mb-3">
                <strong>⚠ Proposal Management</strong><br>
                {{ $proposalFeatureStatus['message'] }}
            </div>
        @endif

        <!-- Main Layout -->
        <div class="CDSPostCaseDetail-layout">
            <!-- Main Content -->
            <div class="CDSPostCaseDetail-main">
                <!-- Case Header -->
                <div class="CDSPostCaseDetail-case-header">
                    <div class="CDSPostCaseDetail-case-title-section">
                        <div>
                            <h1 class="CDSPostCaseDetail-case-title">
                                {{$record->title}}
                            </h1>
                            <div class="CDSPostCaseDetail-case-meta">
                                <div class="CDSPostCaseDetail-meta-item">
                                    <span>📁</span>
                                    <span>{{$record->subServices->name}}</span>
                                </div>
                                <div class="CDSPostCaseDetail-meta-item">
                                    <span>📍</span>
                                    <span>London, UK</span>
                                </div>
                                <div class="CDSPostCaseDetail-meta-item">
                                    <span>⏰</span>
                                    <span>{{getTimeAgo($record->created_at ?? '' )}}</span>
                                </div>
                                <div class="CDSPostCaseDetail-meta-item">
                                    <span>👁</span>
                                    <span>{{$record->professionalCaseViewedCount->count()}} views</span>
                                </div>
                            </div>
                            <div class="CDSPostCaseDetail-tags">
                                <span class="CDSPostCaseDetail-tag">Urgent</span>
                                <span class="CDSPostCaseDetail-tag">Tech</span>
                                <span class="CDSPostCaseDetail-tag">UK Immigration</span>
                                <span class="CDSPostCaseDetail-tag">Visa Application</span>
                            </div>
                        </div>
                        <div class="CDSPostCaseDetail-status-badge CDSPostCaseDetail-status-open">
                            <div class="CDSPostCaseDetail-pulse-dot"></div>
                            Open for Proposals
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="CDSPostCaseDetail-description-card">
                    <h2 class="CDSPostCaseDetail-section-title">
                        <span>📋</span>
                        Case Description
                    </h2>
                    <p class="CDSPostCaseDetail-description-text">
                       {!! html_entity_decode($record->description) !!}
                    </p>
                </div>

                <!-- Proposals Section -->
                <div class="CDSPostCaseDetail-proposals-section">
                    <div class="CDSPostCaseDetail-proposals-header">
                        <h2 class="CDSPostCaseDetail-section-title">
                            <span>💼</span>
                            Proposals Received
                        </h2>
                        <div class="CDSPostCaseDetail-proposals-count">
                            <span class="CDSPostCaseDetail-proposal-count-badge">{{$professionalProposal}}</span>
                            <span>professionals have submitted proposals</span>
                        </div>
                    </div>

                    <!-- Proposal Card 1 -->
                    {{--<div class="CDSProposals-card">
                        <!-- Professional Header -->
                        <div class="CDSProposals-professional-header">
                            <div class="CDSProposals-professional-info">
                                <div class="CDSProposals-professional-avatar no-image">
                                    LB
                                </div>
                                <div class="CDSProposals-professional-details">
                                    <h3>Laith Barry</h3>
                                    <div class="CDSProposals-professional-company">Hurley Calhoun Associates</div>
                                    <div class="CDSProposals-professional-location">
                                        <span>📍</span>
                                        <span>Ahmedabad, India</span>
                                    </div>
                                    <div class="CDSProposals-professional-tags">
                                        <span class="CDSProposals-tag">New Professional</span>
                                        <span class="CDSProposals-tag">0 Years Experience</span>
                                    </div>
                                </div>
                            </div>
                            <div class="CDSProposals-submission-time">
                                <span style="font-size: 13px; color: #999;">Submitted 30 min ago</span>
                            </div>
                        </div>

                        <!-- Proposal Content -->
                        <div class="CDSProposals-content">
                            <div class="CDSProposals-description">
                                I'm excited to help with your immigration case. As a dedicated professional at Hurley Calhoun Associates, I bring fresh perspectives and commitment to ensuring your case receives the attention it deserves. Our firm specializes in student pathways and international education streams.
                            </div>

                            <!-- Quotation Section -->
                            <div class="CDSProposals-quotation">
                                <h4 class="CDSProposals-quotation-title">Quotation Details</h4>
                                <table class="CDSProposals-quotation-table">
                                    <thead>
                                        <tr>
                                            <th>PARTICULAR</th>
                                            <th style="text-align: right;">AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Initial Consultation & Case Review</td>
                                            <td class="amount">$23.00</td>
                                        </tr>
                                        <tr class="CDSProposals-quotation-total">
                                            <td><strong>Total:</strong></td>
                                            <td class="amount"><strong>$23.00</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Service Details -->
                            <div class="CDSProposals-service-details">
                                <div class="CDSProposals-detail-item">
                                    <span class="CDSProposals-detail-label">Service Offer</span>
                                    <span class="CDSProposals-detail-value">Student Pathways > International Education Stream > Document test</span>
                                </div>
                                <div class="CDSProposals-detail-item">
                                    <span class="CDSProposals-detail-label">Initial Consultancy Fees</span>
                                    <span class="CDSProposals-detail-value">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="CDSProposals-actions">
                            <button class="CDSProposals-btn CDSProposals-btn-view">
                                View Previous
                            </button>
                            <button class="CDSProposals-btn CDSProposals-btn-message">
                                Message
                            </button>
                            <button class="CDSProposals-btn CDSProposals-btn-award">
                                Award Contract
                            </button>
                        </div>
                    </div>--}}
                    <div id="proposal-history"></div>
                    <!-- Proposal Card 2 -->
                    {{--<div class="CDSPostCaseDetail-proposal-card">
                        <div class="CDSPostCaseDetail-proposal-header">
                            <div class="CDSPostCaseDetail-professional-info">
                                <div class="CDSPostCaseDetail-professional-avatar">MC</div>
                                <div class="CDSPostCaseDetail-professional-details">
                                    <h3>Michael Chen</h3>
                                    <div class="CDSPostCaseDetail-professional-meta">
                                        <span class="CDSPostCaseDetail-verified-badge">
                                            ✓ Verified Professional
                                        </span>
                                        <span class="CDSPostCaseDetail-rating">
                                            ⭐ 4.9
                                        </span>
                                        <span>10+ years exp</span>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 12px; color: #999;">Submitted 1 hour ago</span>
                            </div>
                        </div>
                        
                        <div class="CDSPostCaseDetail-proposal-content">
                            As an immigration lawyer with over 10 years of experience in UK work visas, I can provide expert guidance for your Skilled Worker visa application. I've handled numerous cases for tech professionals and understand the nuances of your industry's requirements.
                            <br><br>
                            I'll ensure your application is complete, accurate, and presents your case in the best possible light. My service includes document review, application assistance, and representation if needed.
                        </div>
                        
                        <div class="CDSPostCaseDetail-proposal-footer">
                            <div class="CDSPostCaseDetail-proposal-details">
                                <div class="CDSPostCaseDetail-proposal-detail">
                                    <span class="CDSPostCaseDetail-detail-label">Proposed Fee</span>
                                    <span class="CDSPostCaseDetail-detail-value">$2,400</span>
                                </div>
                                <div class="CDSPostCaseDetail-proposal-detail">
                                    <span class="CDSPostCaseDetail-detail-label">Timeline</span>
                                    <span class="CDSPostCaseDetail-detail-value">5 days</span>
                                </div>
                                <div class="CDSPostCaseDetail-proposal-detail">
                                    <span class="CDSPostCaseDetail-detail-label">Success Rate</span>
                                    <span class="CDSPostCaseDetail-detail-value">99%</span>
                                </div>
                            </div>
                            <div class="CDSPostCaseDetail-proposal-actions">
                                <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-secondary" onclick="viewProfile()">View Profile</button>
                                <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-award" onclick="awardContract()">Award Contract</button>
                            </div>
                        </div>
                    </div>

                    <!-- Load More Button -->
                    <div style="text-align: center; margin-top: 20px;">
                        <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-secondary" onclick="loadMoreProposals()">
                            Load More Proposals (3)
                        </button>
                    </div>--}}
                </div>

            </div>

            <!-- Sidebar -->
            <div class="CDSPostCaseDetail-sidebar">

                <!-- Client Card -->
                <div class="CDSPostCaseDetail-client-card">
                    <h3 class="CDSPostCaseDetail-section-title" style="font-size: 16px;">
                        <span>👤</span>
                        About the Client
                    </h3>
                    <div class="CDSPostCaseDetail-client-header">
                        {!! getProfileImage($record->userAdded->unique_id) !!}
                        <!-- <div class="CDSPostCaseDetail-client-avatar">JS</div> -->
                        <div class="CDSPostCaseDetail-client-info">
                            <h4>{{$record->userAdded->first_name ?? ''}} {{$record->userAdded->last_name ?? ''}}</h4>
                            <div class="CDSPostCaseDetail-client-status">
                                <div class="CDSPostCaseDetail-online-indicator"></div>
                                <span>Online now</span>
                            </div>
                        </div>
                    </div>
                    <div class="CDSPostCaseDetail-client-stats">
                        
                    </div>
                </div>

                <!-- Timeline Card -->
                <div class="CDSPostCaseDetail-timeline-card">
                    <h3 class="CDSPostCaseDetail-section-title" style="font-size: 16px;">
                        <span>⏱</span>
                        Timeline
                    </h3>
                    <div class="CDSPostCaseDetail-timeline-item">
                        <div class="CDSPostCaseDetail-timeline-dot">📝</div>
                        <div class="CDSPostCaseDetail-timeline-content">
                            <div class="CDSPostCaseDetail-timeline-title">Case Posted</div>
                            <div class="CDSPostCaseDetail-timeline-date">{{getTimeAgo($record->created_at)}}</div>
                        </div>
                    </div>
                    <div class="CDSPostCaseDetail-timeline-item">
                        <div class="CDSPostCaseDetail-timeline-dot">💬</div>
                        <div class="CDSPostCaseDetail-timeline-content">
                            <div class="CDSPostCaseDetail-timeline-title">First Proposal Received</div>
                           
                            <div class="CDSPostCaseDetail-timeline-date">
                                @if(!empty($firstProposal))
                                    {{getTimeAgo($firstProposal->created_at ?? '')}}
                                @else
                                    <span class="text-danger">N/A</span>
                                 @endif
                            </div>
                           
                        </div>
                    </div>
                    <div class="CDSPostCaseDetail-timeline-item">
                        <div class="CDSPostCaseDetail-timeline-dot">🔔</div>
                        <div class="CDSPostCaseDetail-timeline-content">
                            <div class="CDSPostCaseDetail-timeline-title">Last Proposals Received</div>
                            <div class="CDSPostCaseDetail-timeline-date">
                                @if(!empty($lastProposal))
                                    {{getTimeAgo($lastProposal->created_at ?? '')}}
                                @else
                                    <span class="text-danger">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="CDSPostCaseDetail-timeline-item">
                        <div class="CDSPostCaseDetail-timeline-dot" style="background: #e0e0e0; color: #999;">⏳</div>
                        <div class="CDSPostCaseDetail-timeline-content">
                            <div class="CDSPostCaseDetail-timeline-title" style="color: #999;">Expected Decision</div>
                            <div class="CDSPostCaseDetail-timeline-date"><span class="text-danger">N/A</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
         {{--
        <div class="CDSPostCaseDetail-action-bar">
            <div class="CDSPostCaseDetail-action-buttons">
                <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-secondary" onclick="saveCase()">
                    <span>🔖</span>
                    <span>Save Case</span>
                </button>
                <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-secondary" onclick="reportCase()">
                    <span>🚩</span>
                    <span>Report</span>
                </button>
                <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-primary" onclick="showPopup('<?php echo baseUrl('cases/add-proposal/'.$record->unique_id) ?>')">
                    <span>📝</span>
                    <span>Submit Proposal</span>
                </button>
                
            </div>
            <div class="CDSPostCaseDetail-share-buttons">
                <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-secondary CDSPostCaseDetail-btn-icon" onclick="shareCase('facebook')">
                    f
                </button>
                <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-secondary CDSPostCaseDetail-btn-icon" onclick="shareCase('twitter')">
                    X
                </button>
                <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-secondary CDSPostCaseDetail-btn-icon" onclick="shareCase('linkedin')">
                    in
                </button>
                <button class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-secondary CDSPostCaseDetail-btn-icon" onclick="copyLink()">
                    🔗
                </button>
            </div>
        </div>--}}
    </div>

    <!-- Notification -->
    <div class="CDSPostCaseDetail-notification-badge" id="notification"></div>

    <!-- Submit Proposal Modal -->
    @include("admin-panel.08-cases.cases.add-proposal-modal")
    
    <!-- edit proposal modal -->
    <div id="editProposalModal" class="CDSPostCaseDetail-modal"></div>

@endsection


@section('javascript')
<script>
    // Proposal Modal functions
    function submitProposal() {
        document.getElementById('proposalModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeProposalModal() {
        document.getElementById('proposalModal').classList.remove('active');
        document.body.style.overflow = '';
        document.getElementById('proposalForm').reset();
    }

    function proposalMessage(e)
    {
        var url = $(e).attr("data-href");
        showLoader();
        $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            beforeSend: function () {},
            success: function (response) {
                if (response.status == true) {
                    hideLoader();
                   openGroupChatforMobileDesktop(response.group_unique_id,response.group_id);
                } else {
                    errorMessage(response.message);
                }
            }
        });
    }
    function withDrawProposal(e)
    {
        var url = $(e).attr("data-href");
        
        Swal.fire({
            title: "Are you sure to withdraw proposal?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            confirmButtonClass: "CdsTYButton-btn-primary",
            cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                redirect(url);
            }
        });
    }
</script>

@endsection