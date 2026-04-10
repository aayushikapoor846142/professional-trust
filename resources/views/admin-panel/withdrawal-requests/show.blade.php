@extends('admin-panel.layouts.app')
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
<h1>Withdrawal Request Details</h1>
 <div class="cds-ty-dashboard-box-header-title">
                        <h4>Request #{{ $withdrawalRequest->unique_id }}</h4>
                        <p>Detailed information about your withdrawal request.</p>
                    </div>
                    <div class="cds-action-elements">
                        <a href="{{ baseUrl('/withdrawal-requests') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Requests
                        </a>
                    </div>
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
<div class="cds-ty-dashboard-box-body">
                    <div class="row">
                        <!-- Request Information -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Request Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Request ID:</strong> #{{ $withdrawalRequest->unique_id }}</p>
                                            <p><strong>Amount:</strong> {{ $withdrawalRequest->formatted_amount }}</p>
                                            <p><strong>Status:</strong> {!! $withdrawalRequest->status !!}</p>
                                            <p><strong>Request Date:</strong> {{ $withdrawalRequest->request_date->format('M d, Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                    
                                            @if($withdrawalRequest->processed_date)
                                                <p><strong>Processed Date:</strong> {{ $withdrawalRequest->processed_date->format('M d, Y H:i') }}</p>
                                            @endif
                                            @if($withdrawalRequest->processedBy)
                                                <p><strong>Processed By:</strong> {{ $withdrawalRequest->processedBy->first_name }} {{ $withdrawalRequest->processedBy->last_name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($withdrawalRequest->description)
                                        <hr>
                                        <p><strong>Description:</strong></p>
                                        <p class="text-muted">{{ $withdrawalRequest->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Banking Details -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-university me-2"></i>
                                        Banking Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Bank Name:</strong> {{ $withdrawalRequest->bankingDetail->bank_name }}</p>
                                            <p><strong>Account Holder:</strong> {{ $withdrawalRequest->bankingDetail->account_holder_name }}</p>
                                            <p><strong>Account Number:</strong> ****{{ substr($withdrawalRequest->bankingDetail->account_number, -4) }}</p>
                                            <p><strong>Account Type:</strong> {{ ucfirst($withdrawalRequest->bankingDetail->account_type) }}</p>
                                        </div>
                                        <div class="col-md-6">
                    
                                            @if($withdrawalRequest->bankingDetail->routing_number)
                                                <p><strong>Routing Number:</strong> {{ $withdrawalRequest->bankingDetail->routing_number }}</p>
                                            @endif
                                            @if($withdrawalRequest->bankingDetail->swift_code)
                                                <p><strong>SWIFT Code/IFSC Code:</strong> {{ $withdrawalRequest->bankingDetail->swift_code }}</p>
                                            @endif
                                            @if($withdrawalRequest->bankingDetail->iban)
                                                <p><strong>IBAN:</strong> {{ $withdrawalRequest->bankingDetail->iban }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($withdrawalRequest->bankingDetail->bank_address || $withdrawalRequest->bankingDetail->city || $withdrawalRequest->bankingDetail->state || $withdrawalRequest->bankingDetail->country)
                                        <hr>
                                        <p><strong>Branch Name:</strong></p>
                                        <p class="text-muted">
                                            {{ $withdrawalRequest->bankingDetail->bank_address }}
                                            {{ $withdrawalRequest->bankingDetail->city ? ', ' . $withdrawalRequest->bankingDetail->city : '' }}
                                            {{ $withdrawalRequest->bankingDetail->state ? ', ' . $withdrawalRequest->bankingDetail->state : '' }}
                                            {{ $withdrawalRequest->bankingDetail->country ? ', ' . $withdrawalRequest->bankingDetail->country : '' }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Admin Notes -->
                            @if($withdrawalRequest->admin_notes)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-comment me-2"></i>
                                            Admin Notes
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">{{ $withdrawalRequest->admin_notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Sidebar -->
                        <div class="col-md-4">
                            <!-- Status Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Status Timeline
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">Request Submitted</h6>
                                                <p class="text-muted mb-0">{{ $withdrawalRequest->request_date->format('M d, Y H:i') }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($withdrawalRequest->status !== 'pending')
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-success"></div>
                                                <div class="timeline-content">
                                                    <h6 class="mb-1">Request {{ ucfirst($withdrawalRequest->status) }}</h6>
                                                    <p class="text-muted mb-0">{{ $withdrawalRequest->processed_date->format('M d, Y H:i') }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- File Upload -->
                            @if($withdrawalRequest->file_upload)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-file me-2"></i>
                                            Supporting Document
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid">
                                            <a href="{{ baseUrl('/withdrawal-requests/' . $withdrawalRequest->unique_id . '/download') }}" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-download me-2"></i>
                                                Download File
                                            </a>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            File: {{ $withdrawalRequest->file_upload }}
                                        </small>
                                    </div>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-cogs me-2"></i>
                                        Actions
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                         @if($withdrawalRequest->admin_file_upload)
                                         <a  href="{{ otherFileDirUrl($withdrawalRequest->admin_file_upload, 't') }}" download" 
                                         class="btn btn-outline-info">
                                                <i class="fas fa-file-download me-2"></i>
                                                Download Admin File
                                            </a>
                                        @endif
                                        <a href="{{ baseUrl('/withdrawal-requests') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-list me-2"></i>
                                            View All Requests
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
	
	</div>
  </div>
</div>
 



<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    padding-left: 10px;
}
</style>


@endsection 