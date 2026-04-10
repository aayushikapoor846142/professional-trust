@extends('admin-panel.layouts.app')
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
<h1>Create Withdrawal Request</h1>
  <div class="cds-ty-dashboard-box-header-title">
                        <h4>Submit New Withdrawal Request</h4>
                        <p>Fill in the details below to submit your withdrawal request. Make sure all information is accurate.</p>
                    </div>
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <form id="withdrawalForm" action="{{ baseUrl('/withdrawal-requests') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Amount and Earnings Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           step="0.01" min="1" max="{{ $pendingEarnings }}" required placeholder="Enter amount">
                                </div>
                                <div class="form-text">
                                    Minimum amount: $1.00 | Maximum amount: ${{ number_format($pendingEarnings, 2) }}
                                </div>
                                <div id="amountError" class="text-danger mt-1" style="display: none;"></div>
                            </div>
                            <div class="col-md-6">
                                <!-- Earnings Summary Cards -->
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                            Total Earnings
                                                        </div>
                                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                            ${{ number_format($totalEarnings, 2) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="card border-left-success shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                            Available for Withdrawal
                                                        </div>
                                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                            ${{ number_format($pendingEarnings, 2) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border-left-info shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                            Completed Withdrawals
                                                        </div>
                                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                            ${{ number_format($completedWithdrawals, 2) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banking Details Selection -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="banking_detail_id" class="form-label">Banking Details *</label>
                                <select class="form-control" id="banking_detail_id" name="banking_detail_id" required>
                                    <option value="">Select Banking Details</option>
                                    @foreach($bankingDetails as $banking)
                                        <option value="{{ $banking->unique_id }}" 
                                                {{ $banking->is_active ? 'selected' : '' }}>
                                            {{ $banking->bank_name }} - 
                                            {{ $banking->account_holder_name }} - 
                                            ****{{ substr($banking->account_number, -4) }}
                                            {{ $banking->is_active ? ' (Active)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    <a href="{{ baseUrl('/profile/banking-details') }}" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Manage banking details
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3" placeholder="Optional description for this withdrawal request"></textarea>
                                <div class="form-text">Provide any additional information about this withdrawal request.</div>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="file_upload" class="form-label">Supporting Document (Optional)</label>
                                <input type="file" class="form-control" id="file_upload" name="file_upload" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <div class="form-text">
                                    Accepted formats: PDF, JPG, JPEG, PNG, DOC, DOCX (Max: 2MB)
                                </div>
                            </div>
                        </div>

                        <!-- Selected Banking Details Preview -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-university me-2"></i>
                                            Selected Banking Details Preview
                                        </h6>
                                    </div>
                                    <div class="card-body" id="bankingDetailsPreview">
                                        @if($activeBankingDetail)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Bank Name:</strong> {{ $activeBankingDetail->bank_name }}</p>
                                                    <p><strong>Account Holder:</strong> {{ $activeBankingDetail->account_holder_name }}</p>
                                                    <p><strong>Account Number:</strong> ****{{ substr($activeBankingDetail->account_number, -4) }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Account Type:</strong> {{ ucfirst($activeBankingDetail->account_type) }}</p>

                                                    @if($activeBankingDetail->routing_number)
                                                        <p><strong>Routing Number:</strong> {{ $activeBankingDetail->routing_number }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted">No banking details selected.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ baseUrl('/withdrawal-requests') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Requests
                                    </a>
                                    <button type="submit" class="CdsTYButton-btn-primary">
                                        <i class="fas fa-paper-plane"></i> Submit Request
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
          
			</div>
	
	</div>
  </div>
</div>




@endsection

@section('css')
<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.text-xs {
    font-size: 0.7rem;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
</style>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    const totalEarnings = {{ $totalEarnings }};
    const pendingEarnings = {{ $pendingEarnings }};
    const maxAmount = pendingEarnings;
    
    // Validate amount on input
    $('#amount').on('input', function() {
        const amount = parseFloat($(this).val()) || 0;
        const errorDiv = $('#amountError');
        
        if (amount > maxAmount) {
            errorDiv.text('Amount cannot exceed your available earnings of $' + maxAmount.toFixed(2));
            errorDiv.show();
            $(this).addClass('is-invalid');
        } else if (amount < 1) {
            errorDiv.text('Amount must be at least $1.00');
            errorDiv.show();
            $(this).addClass('is-invalid');
        } else {
            errorDiv.hide();
            $(this).removeClass('is-invalid');
        }
    });
    
    // Form submission with AJAX
    $('#withdrawalForm').on('submit', function(e) {
        e.preventDefault();
        
        const amount = parseFloat($('#amount').val()) || 0;
        
        if (amount > maxAmount) {
            errorMessage('Withdrawal amount cannot exceed your available earnings of $' + maxAmount.toFixed(2));
            return false;
        }
        
        if (amount < 1) {
            errorMessage('Amount must be at least $1.00');
            return false;
        }
        
        // Check if banking detail is selected
        const bankingDetailId = $('#banking_detail_id').val();
        if (!bankingDetailId) {
            errorMessage('Please select a banking detail');
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
        submitBtn.prop('disabled', true);
        
        // Create FormData for file upload
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status) {
                    successMessage(response.message);
                    setTimeout(function() {
                        window.location.href = '{{ baseUrl("/withdrawal-requests") }}';
                    }, 1500);
                } else {
                    errorMessage(response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while submitting the request.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    if (typeof xhr.responseJSON.message === 'object') {
                        // Handle validation errors
                        const errors = xhr.responseJSON.message;
                        const errorText = Object.values(errors).flat().join('<br>');
                        errorMessage = errorText;
                    } else {
                        errorMessage = xhr.responseJSON.message;
                    }
                }
                errorMessage(errorMessage);
            },
            complete: function() {
                // Reset button state
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection 