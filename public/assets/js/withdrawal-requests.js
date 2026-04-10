/**
 * Withdrawal Requests AJAX Handler
 * Handles all AJAX operations for withdrawal requests management
 */

class WithdrawalRequestsManager {
    constructor() {
        this.currentPage = 1;
        this.autoRefreshInterval = null;
        this.searchTimeout = null;
        this.baseUrl = window.BASEURL || '';
        this.csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
        
        this.init();
    }

    init() {
        this.initializeEventListeners();
        
        // Start auto-refresh if enabled
        if ($("#autoRefresh").is(":checked")) {
            this.startAutoRefresh();
        }
        
        // Load initial data
        this.loadData();
    }

    initializeEventListeners() {
        // Pagination event listeners
        $(".next").click(() => {
            if (!$(".next").hasClass('disabled')) {
                this.changePage('next');
            }
        });
        
        $(".previous").click(() => {
            if (!$(".previous").hasClass('disabled')) {
                this.changePage('prev');
            }
        });
        
        // Enhanced search with debouncing
        $("#datatableSearch").on('input', (e) => {
            clearTimeout(this.searchTimeout);
            const value = $(e.target).val();
            
            if (value === '') {
                this.searchTimeout = setTimeout(() => {
                    this.loadData();
                }, 300);
            } else if (value.length >= 2) {
                this.searchTimeout = setTimeout(() => {
                    this.loadData();
                }, 500);
            }
        });
        
        // Search form submission
        $("#search-form").submit((e) => {
            e.preventDefault();
            this.loadData();
        });
        
        // Auto-refresh toggle
        $("#autoRefresh").change((e) => {
            if ($(e.target).is(":checked")) {
                this.startAutoRefresh();
            } else {
                this.stopAutoRefresh();
            }
        });

        // Cleanup on page unload
        $(window).on('beforeunload', () => {
            this.stopAutoRefresh();
        });
    }

    startAutoRefresh() {
        this.stopAutoRefresh(); // Clear any existing interval
        this.autoRefreshInterval = setInterval(() => {
            this.loadData(this.currentPage, true); // Silent refresh
        }, 30000); // Refresh every 30 seconds
    }

    stopAutoRefresh() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
        }
    }

    refreshData() {
        this.loadData(this.currentPage);
    }

    clearSearch() {
        $("#datatableSearch").val('');
        this.loadData();
    }

    loadData(page = 1, silent = false) {
        this.currentPage = page;
        const search = $("#datatableSearch").val();
        const status = $("#statusFilter").val();
        
        if (!silent) {
            this.showLoading();
            this.hideError();
        }
        
        $.ajax({
            type: "POST",
            url: this.baseUrl + '/withdrawal-requests-ajax?page=' + page,
            data: {
                _token: this.csrfToken,
                search: search,
                status: status
            },
            dataType: 'json',
            timeout: 10000, // 10 second timeout
            success: (data) => {
                this.hideLoading();
                this.hideError();
                
                if (data.status) {
                    $(".norecord").remove(); 
                    $("#tableList").html(data.contents);
                    this.updateLastUpdated();
                    this.updatePagination(data);
                    
                    if (data.total_records > 0) {
                        this.showTable();
                    } else {
                        this.showNoRecords();
                    }
                } else {
                    this.showError(data.message || 'Error loading data');
                }
            },
            error: (xhr, status, error) => {
                this.hideLoading();
                console.error('AJAX Error:', status, error);
                
                let errorMessage = 'Error loading data. Please try again.';
                if (xhr.status === 0) {
                    errorMessage = 'Network error. Please check your connection.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again later.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Page not found. Please refresh the page.';
                }
                
                this.showError(errorMessage);
            }
        });
    }

    showLoading() {
        $("#loadingIndicator").show();
        $("#tableContainer").hide();
        $("#errorMessage").hide();
    }

    hideLoading() {
        $("#loadingIndicator").hide();
        $("#tableContainer").show();
    }

    showError(message) {
        $("#errorText").text(message);
        $("#errorMessage").show();
        $("#tableContainer").hide();
    }

    hideError() {
        $("#errorMessage").hide();
    }

    showTable() {
        $("#tableContainer").show();
    }

    showNoRecords() {
        $(".cdsTYDashboard-table").find(".norecord").remove();
        const html = `
            <div class="text-center text-muted py-5 norecord">
                <i class="fas fa-money-bill-transfer fa-4x mb-4 text-muted"></i>
                <h4>No Withdrawal Requests Found</h4>
                <p class="mb-4">No records match your current search criteria.</p>
                <button type="button" class="btn btn-outline-primary" onclick="withdrawalRequestsManager.clearSearch()">Clear Search</button>
            </div>
        `;
        $("#tableList").html(html);
    }

    updateLastUpdated() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        $("#lastUpdated").text(timeString);
    }

    updatePagination(data) {
        if (data.total_records > 0) {
            const pageinfo = data.current_page + " of " + data.last_page + " <small class='text-danger'>(" + data.total_records + " records)</small>";
            $("#pageinfo").html(pageinfo);
            $("#pageno").val(data.current_page);
            
            // Update pagination buttons
            if (data.current_page < data.last_page) {
                $(".next").removeClass("disabled");
            } else {
                $(".next").addClass("disabled");
            }
            
            if (data.current_page > 1) {
                $(".previous").removeClass("disabled");
            } else {
                $(".previous").addClass("disabled");
            }
            
            $("#pageno").attr("max", data.last_page);
        }
    }

    changePage(action) {
        let page = parseInt($("#pageno").val());
        
        if (action === 'prev') {
            page--;
        }
        if (action === 'next') {
            page++;
        }
        
        if (!isNaN(page) && page > 0) {
            this.loadData(page);
        } else {
            this.showError("Invalid Page Number");
        }
    }

    sortTable(columnIndex) {
        const rows = Array.from(document.querySelectorAll('#tableList .cdsTYDashboard-table-row'));
        const headerCells = document.querySelectorAll('.cdsTYDashboard-table-header .cdsTYDashboard-table-cell');
        const isAscending = headerCells[columnIndex].classList.contains('sorted-asc');
        
        // Reset all header sorting classes
        headerCells.forEach(cell => {
            cell.classList.remove('sorted-asc', 'sorted-desc');
        });
        
        // Toggle sort direction
        if (isAscending) {
            headerCells[columnIndex].classList.add('sorted-desc');
        } else {
            headerCells[columnIndex].classList.add('sorted-asc');
        }
        
        rows.sort((rowA, rowB) => {
            const cellA = rowA.querySelectorAll('.cdsTYDashboard-table-cell')[columnIndex].innerText.trim();
            const cellB = rowB.querySelectorAll('.cdsTYDashboard-table-cell')[columnIndex].innerText.trim();

            if (isAscending) {
                return cellA < cellB ? -1 : cellA > cellB ? 1 : 0;
            } else {
                return cellA > cellB ? -1 : cellA < cellB ? 1 : 0;
            }
        });
        
        // Reorder the rows in the table
        rows.forEach(row => document.getElementById('tableList').appendChild(row));
    }
}

// Global functions for backward compatibility
function refreshData() {
    if (window.withdrawalRequestsManager) {
        window.withdrawalRequestsManager.refreshData();
    }
}

function clearSearch() {
    if (window.withdrawalRequestsManager) {
        window.withdrawalRequestsManager.clearSearch();
    }
}

function loadData(page = 1, silent = false) {
    if (window.withdrawalRequestsManager) {
        window.withdrawalRequestsManager.loadData(page, silent);
    }
}

function changePage(action) {
    if (window.withdrawalRequestsManager) {
        window.withdrawalRequestsManager.changePage(action);
    }
}

function sortTable(columnIndex) {
    if (window.withdrawalRequestsManager) {
        window.withdrawalRequestsManager.sortTable(columnIndex);
    }
}

// Initialize when document is ready
$(document).ready(function() {
    // Initialize withdrawal requests manager for index page
    if ($('#tableList').length > 0) {
        window.withdrawalRequestsManager = new WithdrawalRequestsManager();
    }
    
    // Initialize form handling for create page
    if ($('#withdrawalRequestForm').length > 0) {
        initializeWithdrawalRequestForm();
    }
});

// Form handling for withdrawal request creation
function initializeWithdrawalRequestForm() {
    // Update banking details preview when selection changes
    $('#banking_detail_id').change(function() {
        const selectedId = $(this).val();
        if (selectedId) {
            // You could load the banking details via AJAX here
            // For now, we'll just show a simple message
            $('#bankingDetailsPreview').html('<p class="text-muted">Loading banking details...</p>');
        } else {
            $('#bankingDetailsPreview').html('<p class="text-muted">No banking details selected.</p>');
        }
    });

    // Form submission
    $('#withdrawalRequestForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: window.BASEURL + '/withdrawal-requests',
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
                    // Redirect to withdrawal requests list
                    setTimeout(function() {
                        window.location.href = window.BASEURL + '/withdrawal-requests';
                    }, 1500);
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

    // File size validation
    $('#file_upload').change(function() {
        const file = this.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB
        
        if (file && file.size > maxSize) {
            alert('File size must be less than 2MB.');
            this.value = '';
        }
    });

    // Amount validation
    $('#amount').on('input', function() {
        const amount = parseFloat($(this).val());
        if (amount < 1) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
} 