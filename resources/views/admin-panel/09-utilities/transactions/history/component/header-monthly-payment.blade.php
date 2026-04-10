<!-- header-monthly-payment.blade.php -->
<div class="monthly-payments-container">
   

    <!-- Title Section -->
    <div class="monthly-header">
        <h2 class="monthly-title">Monthly Payments</h2>
        <p class="monthly-count">Showing <span id="recordCount">0</span> payments</p>
      <!-- Search Bar -->
        <div class="monthly-search-wrapper">
            <input type="text" 
                   class="monthly-search-input" 
                   placeholder="Search By Name, Amount, Or Status..." 
                   oninput="searchMonthlyPayment(this)">
            <button class="monthly-search-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </button>
        </div>
	</div>

    <!-- Search and Filter Section -->
    <div class="monthly-controls">
      

        <!-- Filter Dropdowns -->
        <div class="monthly-filters">
            <!-- Status Filter -->
            <div class="monthly-dropdown">
                <button class="monthly-dropdown-btn" onclick="toggleMonthlyFilter('status', event)">
                    Status
                    <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 12 12">
                        <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    </svg>
                </button>
                <div class="monthly-dropdown-panel" id="statusPanel">
                    <div class="dropdown-panel-content">
                        <div class="dropdown-item">
                            <input type="checkbox" id="status-all" value="all" checked>
                            <label for="status-all">All Statuses</label>
                        </div>
                        @if(!empty($subscriptionStatuses))
                            @foreach($subscriptionStatuses as $value)
                            <div class="dropdown-item">
                                <input type="checkbox" id="status-{{$value}}" value="{{$value}}">
                                <label for="status-{{$value}}">{{ucfirst($value)}}</label>
                            </div>
                            @endforeach
                        @else
                            <div class="dropdown-item">
                                <input type="checkbox" id="status-active" value="active">
                                <label for="status-active">Active</label>
                            </div>
                            <div class="dropdown-item">
                                <input type="checkbox" id="status-inactive" value="inactive">
                                <label for="status-inactive">Inactive</label>
                            </div>
                            <div class="dropdown-item">
                                <input type="checkbox" id="status-pending" value="pending">
                                <label for="status-pending">Pending</label>
                            </div>
                        @endif
                        <div class="dropdown-actions">
                            <button class="btn-clear" onclick="clearFilter('status')">Clear</button>
                            <button class="btn-apply" onclick="applyFilter('status')">Apply</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount Filter -->
            <div class="monthly-dropdown">
                <button class="monthly-dropdown-btn" onclick="toggleMonthlyFilter('amount', event)">
                    Amount
                    <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 12 12">
                        <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    </svg>
                </button>
                <div class="monthly-dropdown-panel" id="amountPanel">
                    <div class="dropdown-panel-content">
                        <div class="dropdown-section">
                            <label class="section-label">Amount Range</label>
                            <div class="range-inputs">
                                <input type="number" id="minAmount" placeholder="Min" value="0">
                                <span>-</span>
                                <input type="number" id="maxAmount" placeholder="Max" value="10000">
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-item">
                            <input type="checkbox" id="amount-under-100" value="0-100">
                            <label for="amount-under-100">Under $100</label>
                        </div>
                        <div class="dropdown-item">
                            <input type="checkbox" id="amount-100-500" value="100-500">
                            <label for="amount-100-500">$100 - $500</label>
                        </div>
                        <div class="dropdown-item">
                            <input type="checkbox" id="amount-500-1000" value="500-1000">
                            <label for="amount-500-1000">$500 - $1,000</label>
                        </div>
                        <div class="dropdown-item">
                            <input type="checkbox" id="amount-over-1000" value="1000+">
                            <label for="amount-over-1000">Over $1,000</label>
                        </div>
                        <div class="dropdown-actions">
                            <button class="btn-clear" onclick="clearFilter('amount')">Clear</button>
                            <button class="btn-apply" onclick="applyFilter('amount')">Apply</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Filter -->
            <div class="monthly-dropdown">
                <button class="monthly-dropdown-btn" onclick="toggleMonthlyFilter('details', event)">
                    Details
                    <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 12 12">
                        <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    </svg>
                </button>
                <div class="monthly-dropdown-panel" id="detailsPanel">
                    <div class="dropdown-panel-content">
                        <div class="dropdown-section">
                            <label class="section-label">Date Range</label>
                            <div class="date-inputs">
                                <input type="date" id="detailFromDate" class="date-input">
                                <input type="date" id="detailToDate" class="date-input">
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-item">
                            <input type="checkbox" id="detail-today" value="today">
                            <label for="detail-today">Today</label>
                        </div>
                        <div class="dropdown-item">
                            <input type="checkbox" id="detail-week" value="week">
                            <label for="detail-week">This Week</label>
                        </div>
                        <div class="dropdown-item">
                            <input type="checkbox" id="detail-month" value="month">
                            <label for="detail-month">This Month</label>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-section">
                            <label class="section-label">Payment Type</label>
                        </div>
                        <div class="dropdown-item">
                            <input type="checkbox" id="type-regular" value="regular">
                            <label for="type-regular">Regular Payment</label>
                        </div>
                        <div class="dropdown-item">
                            <input type="checkbox" id="type-refund" value="refund">
                            <label for="type-refund">Refunded</label>
                        </div>
                        <div class="dropdown-actions">
                            <button class="btn-clear" onclick="clearFilter('details')">Clear</button>
                            <button class="btn-apply" onclick="applyFilter('details')">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
// Global variables
let activeDropdown = null;
let activeFilters = {
    status: [],
    amount: [],
    details: []
};

// Toggle dropdown
function toggleMonthlyFilter(type, event) {
    event.stopPropagation();
    
    const panel = document.getElementById(type + 'Panel');
    const btn = event.currentTarget;
    
    // Close other dropdowns
    if (activeDropdown && activeDropdown !== type) {
        closeDropdown(activeDropdown);
    }
    
    // Toggle current dropdown
    if (activeDropdown === type) {
        closeDropdown(type);
    } else {
        panel.classList.add('show');
        btn.classList.add('active');
        activeDropdown = type;
    }
}

// Close dropdown
function closeDropdown(type) {
    const panel = document.getElementById(type + 'Panel');
    const btn = panel.parentElement.querySelector('.monthly-dropdown-btn');
    
    panel.classList.remove('show');
    btn.classList.remove('active');
    
    if (activeDropdown === type) {
        activeDropdown = null;
    }
}

// Clear filter
function clearFilter(type) {
    // Clear checkboxes
    const panel = document.getElementById(type + 'Panel');
    const checkboxes = panel.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = false);
    
    // Clear range inputs if amount filter
    if (type === 'amount') {
        document.getElementById('minAmount').value = '0';
        document.getElementById('maxAmount').value = '10000';
    }
    
    // Clear date inputs if details filter
    if (type === 'details') {
        document.getElementById('detailFromDate').value = '';
        document.getElementById('detailToDate').value = '';
    }
    
    // Update active filters
    activeFilters[type] = [];
    
    // Apply filters
    applyAllFilters();
}

// Apply filter
function applyFilter(type) {
    const panel = document.getElementById(type + 'Panel');
    activeFilters[type] = [];
    
    // Collect checked values
    const checkboxes = panel.querySelectorAll('input[type="checkbox"]:checked');
    checkboxes.forEach(cb => {
        activeFilters[type].push(cb.value);
    });
    
    // Collect range values if amount filter
    if (type === 'amount') {
        const min = document.getElementById('minAmount').value;
        const max = document.getElementById('maxAmount').value;
        if (min || max) {
            activeFilters[type].push(`range:${min}-${max}`);
        }
    }
    
    // Collect date values if details filter
    if (type === 'details') {
        const fromDate = document.getElementById('detailFromDate').value;
        const toDate = document.getElementById('detailToDate').value;
        if (fromDate || toDate) {
            activeFilters[type].push(`date:${fromDate}-${toDate}`);
        }
    }
    
    // Close dropdown
    closeDropdown(type);
    
    // Apply all filters
    applyAllFilters();
}

// Apply all filters
function applyAllFilters() {
    const filterData = {
        from_date: document.getElementById('fromDate')?.value || '',
        to_date: document.getElementById('toDate')?.value || '',
        search: document.querySelector('.monthly-search-input')?.value || '',
        status: activeFilters.status,
        amount: activeFilters.amount,
        details: activeFilters.details
    };
    
    // Make AJAX call
    if (typeof jQuery !== 'undefined') {
        $.ajax({
            url: '', // Update with your route
            type: 'POST',
            data: filterData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#monthly-payment-list').html(response); // Update with your container ID
                updateRecordCount(response.count || 0);
            },
            error: function(xhr) {
                console.error('Filter error:', xhr);
            }
        });
    }
}

// Update record count
function updateRecordCount(count) {
    document.getElementById('recordCount').textContent = count;
}

// Search function
function searchMonthlyPayment(input) {
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(() => {
        applyAllFilters();
    }, 500);
}

// Date filter functions (existing)
function monthlyDateFilter() {
    applyAllFilters();
}

function clearMonthlyDateFilter() {
    document.getElementById('fromDate').value = '';
    document.getElementById('toDate').value = '';
    applyAllFilters();
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (activeDropdown && !e.target.closest('.monthly-dropdown')) {
        closeDropdown(activeDropdown);
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Any initialization code here
});
</script>