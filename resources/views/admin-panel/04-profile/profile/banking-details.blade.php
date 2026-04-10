@extends('admin-panel.layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/banking-details.css') }}">

<div class="CdsBankDetails-container">
    <div class="CdsBankDetails-header">
        <h1>Manage Your Banking Details</h1>
        <p>Add and manage your banking accounts for payments and withdrawals. Only one account can be active at a time.</p>
    </div>

    <div class="CdsBankDetails-controls">
        <button class="CdsBankDetails-add-btn" onclick="openBankingModal()">
            <span class="CdsBankDetails-icon CdsBankDetails-icon-plus"></span>
            Add New Banking Details
        </button>
        <div class="CdsBankDetails-search-wrapper">
            <span class="CdsBankDetails-search-icon CdsBankDetails-icon CdsBankDetails-icon-search"></span>
            <input type="text" class="CdsBankDetails-search" id="bankingSearch"
                placeholder="Search by bank name, account holder, or account number..."
                onkeyup="searchBankingDetails()">
        </div>
        <div class="CdsBankDetails-counter">
            Showing <span id="cdsBankDetailCount">{{ $bankingDetails->count() }}</span> banking details
        </div>
    </div>

    {{-- Banking Details Grid --}}
    @if($bankingDetails->count() > 0)
    <div class="CdsBankDetails-grid" id="bankingDetailsList">
        @foreach($bankingDetails as $banking)
            @php
                $withdrawalCount = \App\Models\WithdrawalRequest::where('banking_detail_id', $banking->id)->count();
                $canDelete = $withdrawalCount === 0;
            @endphp
            <div class="CdsBankDetails-card {{ $banking->is_active ? 'CdsBankDetails-default' : '' }}">
                <div class="CdsBankDetails-card-header">
                    <div class="CdsBankDetails-bank-logo">
                        {{ strtoupper(substr($banking->bank_name, 0, 2)) }}
                    </div>
                    <div class="CdsBankDetails-bank-info">
                        <div class="CdsBankDetails-bank-name">{{ $banking->bank_name }}</div>
                    </div>
                    @if($banking->is_active)
                        <div class="CdsBankDetails-status">Active</div>
                    @endif
                </div>
                <div class="CdsBankDetails-details">
                    <div class="CdsBankDetails-detail-row">
                        <span class="CdsBankDetails-label">Account Holder:</span>
                        <span class="CdsBankDetails-value">{{ $banking->account_holder_name }}</span>
                    </div>
                    <div class="CdsBankDetails-detail-row">
                        <span class="CdsBankDetails-label">Account Number:</span>
                        <span class="CdsBankDetails-value">****{{ substr($banking->account_number, -4) }}</span>
                    </div>
                    <div class="CdsBankDetails-detail-row">
                        <span class="CdsBankDetails-label">Account Type:</span>
                        <span class="CdsBankDetails-value">{{ ucfirst($banking->account_type) }}</span>
                    </div>
                    @if($banking->swift_code)
                    <div class="CdsBankDetails-detail-row">
                        <span class="CdsBankDetails-label">SWIFT/IFSC Code:</span>
                        <span class="CdsBankDetails-value">{{ $banking->swift_code }}</span>
                    </div>
                    @endif
                    @if($banking->bank_address || $banking->city || $banking->state || $banking->country)
                    <div class="CdsBankDetails-detail-row">
                        <span class="CdsBankDetails-label">Branch:</span>
                        <span class="CdsBankDetails-value" style="font-family: inherit; font-size: 0.8rem;">
                            {{ $banking->bank_address }}{{ $banking->city ? ', '.$banking->city : '' }}{{ $banking->state ? ', '.$banking->state : '' }}{{ $banking->country ? ', '.$banking->country : '' }}
                        </span>
                    </div>
                    @endif
                </div>
                <div class="CdsBankDetails-actions">
                    <button class="CdsBankDetails-action-btn CdsBankDetails-btn-default"
                        onclick="setActiveBanking('{{ $banking->unique_id }}')"
                        {{ $banking->is_active ? 'disabled' : '' }}>
                        <span class="CdsBankDetails-icon CdsBankDetails-icon-check"></span>
                        {{ $banking->is_active ? 'Default' : 'Set Default' }}
                    </button>
                    <button class="CdsBankDetails-action-btn CdsBankDetails-btn-edit"
                        onclick="editBanking('{{ $banking->unique_id }}')">
                        <span class="CdsBankDetails-icon CdsBankDetails-icon-edit"></span>
                        Edit
                    </button>
                    @if($canDelete)
                    <button class="CdsBankDetails-action-btn CdsBankDetails-btn-delete"
                        onclick="deleteBanking('{{ $banking->unique_id }}')">
                        <span class="CdsBankDetails-icon CdsBankDetails-icon-delete"></span>
                        Delete
                    </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="CdsBankDetails-empty">
        <div class="CdsBankDetails-empty-icon CdsBankDetails-icon CdsBankDetails-icon-bank"></div>
        <div class="CdsBankDetails-empty-text">No banking details found</div>
        <button class="CdsBankDetails-add-btn" onclick="openBankingModal()">
            <span class="CdsBankDetails-icon CdsBankDetails-icon-plus"></span>
            Add Your First Bank Account
        </button>
    </div>
    @endif
</div>

{{-- Modal --}}
<div class="CdsBankDetails-modal" id="bankingModal">
    <div class="CdsBankDetails-modal-content">
        <div class="CdsBankDetails-modal-header">
            <h2 class="CdsBankDetails-modal-title" id="bankingModalLabel">Add Banking Details</h2>
        </div>
        <div class="CdsBankDetails-modal-body">
            <form id="bankingForm">
                @csrf
                <input type="hidden" id="banking_id" name="banking_id">
                
                <div class="CdsBankDetails-form-row">
                    <div class="CdsBankDetails-form-group">
                        <label class="CdsBankDetails-form-label">Bank Name *</label>
                        <input type="text" class="CdsBankDetails-form-input" id="bank_name" name="bank_name" required>
                    </div>
                    
                    <div class="CdsBankDetails-form-group">
                        <label class="CdsBankDetails-form-label">Account Holder Name *</label>
                        <input type="text" class="CdsBankDetails-form-input" id="account_holder_name" name="account_holder_name" required>
                    </div>
                </div>
                
                <div class="CdsBankDetails-form-row">
                    <div class="CdsBankDetails-form-group">
                        <label class="CdsBankDetails-form-label">Account Number *</label>
                        <input type="text" class="CdsBankDetails-form-input" id="account_number" name="account_number" required>
                    </div>
                    
                    <div class="CdsBankDetails-form-group">
                        <label class="CdsBankDetails-form-label">Account Type *</label>
                        <select class="CdsBankDetails-form-select" id="account_type" name="account_type" required onclick="console.log('Select clicked')" onchange="console.log('Select changed to:', this.value)">
                            <option value="">Select Account Type</option>
                            <option value="savings">Savings</option>
                            <option value="checking">Checking</option>
                            <option value="business">Business</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="CdsBankDetails-form-row">
                    <div class="CdsBankDetails-form-group">
                        <label class="CdsBankDetails-form-label">Routing Number</label>
                        <input type="text" class="CdsBankDetails-form-input" id="routing_number" name="routing_number">
                    </div>
                    
                    <div class="CdsBankDetails-form-group">
                        <label class="CdsBankDetails-form-label">SWIFT Code/IFSC Code</label>
                        <input type="text" class="CdsBankDetails-form-input" id="swift_code" name="swift_code">
                    </div>
                </div>
                
                <div class="CdsBankDetails-form-group">
                    <label class="CdsBankDetails-form-label">IBAN</label>
                    <input type="text" class="CdsBankDetails-form-input" id="iban" name="iban">
                </div>
                
                <div class="CdsBankDetails-form-group">
                    <label class="CdsBankDetails-form-label">Branch Name</label>
                    <textarea class="CdsBankDetails-form-input" id="bank_address" name="bank_address" rows="2"></textarea>
                </div>
                
                <div class="CdsBankDetails-form-row">
                    <div class="CdsBankDetails-form-group">
                        <label class="CdsBankDetails-form-label">City</label>
                        <input type="text" class="CdsBankDetails-form-input" id="city" name="city">
                    </div>
                    
                    <div class="CdsBankDetails-form-group">
                        <label class="CdsBankDetails-form-label">State</label>
                        <input type="text" class="CdsBankDetails-form-input" id="state" name="state">
                    </div>
                </div>
                
                <div class="CdsBankDetails-form-row">
                    <div class="CdsBankDetails-form-group">
                        <label class="CdsBankDetails-form-label">Country</label>
                        <input type="text" class="CdsBankDetails-form-input" id="country" name="country">
                    </div>
                    
                    <div class="CdsBankDetails-form-group">
                        <label class="CdsBankDetails-form-label">Zip Code</label>
                        <input type="text" class="CdsBankDetails-form-input" id="zip_code" name="zip_code">
                    </div>
                </div>
                
                <div class="CdsBankDetails-form-group">
                    <label class="CdsBankDetails-form-label">Description</label>
                    <textarea class="CdsBankDetails-form-input" id="description" name="description" rows="2" placeholder="Optional description for this banking account"></textarea>
                </div>
                
                <div class="CdsBankDetails-form-group">
                    <label class="CdsBankDetails-form-label">
                        <input type="checkbox" id="is_active" name="is_active" value="1">
                        Set as default banking account
                    </label>
                </div>
            </form>
        </div>
        <div class="CdsBankDetails-modal-footer">
            <button class="CdsBankDetails-btn CdsBankDetails-btn-secondary" onclick="closeBankingModal()">Cancel</button>
            <button class="CdsBankDetails-btn CdsBankDetails-btn-primary" type="submit" form="bankingForm">Save Banking Details</button>
        </div>
    </div>
</div>
@endsection


@section('javascript')
    <script>
        // Banking Details JavaScript for Profile Page
        let currentBankingId = null;

        function openBankingModal(bankingId = null) {
            console.log('Opening modal with bankingId:', bankingId);
            currentBankingId = bankingId;
            
            if (bankingId) {
                // Edit mode - load existing data
                console.log('Edit mode - loading data for ID:', bankingId);
                resetBankingForm();
                loadBankingData(bankingId);
                $('#bankingModalLabel').text('Edit Banking Details');
            } else {
                // Add mode
                console.log('Add mode');
                resetBankingForm();
                currentBankingId = null; // Ensure it's null for add mode
                $('#bankingModalLabel').text('Add Banking Details');
            }
            
            $('#bankingModal').addClass('CdsBankDetails-active');
            $('body').addClass('modal-open');
            console.log('Modal should be visible now');
            
            // Debug: Check if modal is visible
            setTimeout(function() {
                const modal = document.getElementById('bankingModal');
                console.log('Modal element:', modal);
                console.log('Modal classes:', modal.className);
                console.log('Modal display style:', window.getComputedStyle(modal).display);
                console.log('Form fields count:', document.querySelectorAll('#bankingForm input, #bankingForm select, #bankingForm textarea').length);
            }, 100);
        }

        function resetBankingForm() {
            $('#bankingForm')[0].reset();
            $('#banking_id').val('');
            console.log('Form reset, currentBankingId:', currentBankingId);
        }

        function closeBankingModal() {
            $('#bankingModal').removeClass('CdsBankDetails-active');
            $('body').removeClass('modal-open');
            currentBankingId = null;
        }

        // Close modal on outside click
        $(document).ready(function() {
            initSelect();
            $('#bankingModal').on('click', function(e) {
                if (e.target === this) {
                    closeBankingModal();
                }
            });
        });



        function loadBankingData(bankingId) {
            // For security, we'll need to get full data via AJAX
            $.ajax({
                url: BASEURL + '/banking-details/get/' + bankingId,
                type: 'GET',
                success: function(response) {
                    if (response.status) {
                        const data = response.data;
                        $('#bank_name').val(data.bank_name);
                        $('#account_holder_name').val(data.account_holder_name);
                        $('#account_number').val(data.account_number);
                        $('#routing_number').val(data.routing_number);
                        $('#swift_code').val(data.swift_code);
                        $('#iban').val(data.iban);
                        $('#bank_address').val(data.bank_address);
                        $('#city').val(data.city);
                        $('#state').val(data.state);
                        $('#country').val(data.country);
                        $('#zip_code').val(data.zip_code);
                        $('#account_type').val(data.account_type);

                        $('#description').val(data.description);
                        $('#is_active').prop('checked', data.is_active);
                    }
                }
            });
        }

        $('#bankingForm').submit(function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            let url;
            
            console.log('Form submission - currentBankingId:', currentBankingId);
            
            if (currentBankingId) {
                // Edit mode
                url = BASEURL + '/banking-details/update/' + currentBankingId;
                console.log('Using update URL:', url);
            } else {
                // Add mode
                url = BASEURL + '/banking-details/save';
                console.log('Using save URL:', url);
            }
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status) {
                        successMessage(response.message);
                        $('#bankingModal').removeClass('CdsBankDetails-active');
                        $('body').removeClass('modal-open');
                        location.reload(); // Refresh to show updated data
                    } else {
                        validation(response.message);
                    }
                },
                error: function() {
                    hideLoader();
                    internalError();
                }
            });
        });

        function setActiveBanking(bankingId) {
            if (confirm('Are you sure you want to set this as your default banking account?')) {
                $.ajax({
                    url: BASEURL + '/banking-details/set-active/' + bankingId,
                    type: 'POST',
                    data: {
                        _token: csrf_token
                    },
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        hideLoader();
                        if (response.status) {
                            successMessage(response.message);
                            location.reload();
                        } else {
                            errorMessage(response.message);
                        }
                    },
                    error: function() {
                        hideLoader();
                        internalError();
                    }
                });
            }
        }

        function deleteBanking(bankingId) {
            if (confirm('Are you sure you want to delete this banking account? This action cannot be undone.')) {
                $.ajax({
                    url: BASEURL + '/banking-details/delete/' + bankingId,
                    type: 'DELETE',
                    data: {
                        _token: csrf_token
                    },
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        hideLoader();
                        if (response.status) {
                            successMessage(response.message);
                            location.reload();
                        } else {
                            errorMessage(response.message);
                        }
                    },
                    error: function(xhr) {
                        hideLoader();
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage(xhr.responseJSON.message);
                        } else {
                            internalError();
                        }
                    }
                });
            }
        }

        function editBanking(bankingId) {
            openBankingModal(bankingId);
        }

        // Search functionality
        function searchBankingDetails() {
            const searchTerm = $('#bankingSearch').val().toLowerCase().trim();
            const bankingItems = $('.CdsBankDetails-card');
            let visibleCount = 0;

            // Show/hide clear button based on search term
            if (searchTerm !== '') {
                $('#clearSearchBtn').show();
            } else {
                $('#clearSearchBtn').hide();
            }

            bankingItems.each(function() {
                const $item = $(this);
                const bankName = $item.find('.CdsBankDetails-bank-name').text().toLowerCase();
                const accountHolder = $item.find('.CdsBankDetails-label:contains("Account Holder:")').siblings('.CdsBankDetails-value').text().toLowerCase();
                const accountNumber = $item.find('.CdsBankDetails-label:contains("Account Number:")').siblings('.CdsBankDetails-value').text().toLowerCase();
                
                const matches = bankName.includes(searchTerm) || 
                            accountHolder.includes(searchTerm) || 
                            accountNumber.includes(searchTerm);

                if (matches || searchTerm === '') {
                    $item.show();
                    visibleCount++;
                } else {
                    $item.hide();
                }
            });

            // Update results count
            if (searchTerm === '') {
                $('#cdsBankDetailCount').text(bankingItems.length);
            } else {
                $('#cdsBankDetailCount').text(visibleCount);
            }
        }

        // Clear search functionality
        function clearSearch() {
            $('#bankingSearch').val('');
            searchBankingDetails();
            $('#bankingSearch').focus();
        }

        // Multi-select functionality (simplified for current implementation)
        function updateBulkActions() {
            // This function is kept for compatibility but simplified
            // since the current implementation doesn't use checkboxes
            console.log('Bulk actions updated');
        }

        function toggleSelectAll() {
            // This function is kept for compatibility but simplified
            console.log('Toggle select all');
        }

        function clearSelection() {
            // This function is kept for compatibility but simplified
            console.log('Clear selection');
        }

        function deleteSelectedBanking() {
            // This function is kept for compatibility but simplified
            console.log('Delete selected banking');
        }

        // Search on input (real-time search)
        $(document).ready(function() {
            $('#bankingSearch').on('input', function() {
                searchBankingDetails();
            });

            // Search on Enter key
            $('#bankingSearch').on('keypress', function(e) {
                if (e.which === 13) {
                    searchBankingDetails();
                }
            });
        });
    </script>
    @endsection 