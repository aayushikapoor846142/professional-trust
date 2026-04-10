@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('appointment-system') !!}
@endsection
@section('styles')
<link href="{{ url('assets/css/10-CDS-appointment-system.css') }}" rel="stylesheet" />
@endsection
@section('content')

<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 @if(!$canAddAppointmentBooking)
                <div class="alert alert-danger mb-3">
                    <strong>⚠ Appointment Booking Management</strong><br>
                    {{ $appointmentBookingFeatureStatus['message']  }}
                </div>
            @else
                <div class="alert alert-warning mb-3">
                    <strong>⚠ Appointment Booking Management</strong><br>
                    {{ $appointmentBookingFeatureStatus['message'] }}
                </div>
            @endif
@include('admin-panel.03-appointments.appointment-system.appointment-booking.header-search') <div class="CdsDashboardAppointment-system-controls">
            <div class="CdsDashboardAppointment-system-search">
                <span class="CdsDashboardAppointment-system-search-icon">🔍</span>
                <input type="text" class="CdsDashboardAppointment-system-search-input" placeholder="Search by appointment ID, or client" id="search-input" name="search">
                <div class="CdsDashboardAppointment-system-autocomplete-dropdown" id="autocomplete-dropdown">
                    <!-- Recent Searches Section -->
                    <div class="CdsDashboardAppointment-system-autocomplete-recent" id="recent-searches">
                        <div class="CdsDashboardAppointment-system-autocomplete-section-header">
                            <span>Recent Searches</span>
                            <button type="button" class="CdsDashboardAppointment-system-autocomplete-clear-recent">
                                Clear
                            </button>
                        </div>
                        <div class="CdsDashboardAppointment-system-autocomplete-recent-items"></div>
                    </div>

                    <!-- Search Results Section -->
                    <div class="CdsDashboardAppointment-system-autocomplete-search-section" style="display: none;">
                        <div class="CdsDashboardAppointment-system-autocomplete-loading">
                            <i class="fa fa-spinner fa-spin"></i> Searching...
                        </div>
                        <div class="CdsDashboardAppointment-system-autocomplete-results"></div>
                        <div class="CdsDashboardAppointment-system-autocomplete-no-results">
                            <div class="no-results-icon">🔍</div>
                            <div class="no-results-text">No appointments found</div>
                            <div class="no-results-hint">Try searching with different keywords</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="CdsDashboardAppointment-system-btn-group">
                
                @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'add'
                ]))
                @if($canAddAppointmentBooking)
                <a href="{{ baseUrl('appointments/appointment-booking/save-booking') }}" 
                   class="CdsDashboardAppointment-system-btn CdsDashboardAppointment-system-btn-primary" 
                   data-mobile-text="New">
                    ➕ <span>New Appointment</span>
                </a>
                @endif
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'view-calender'
                ]))
                <a href="{{ baseUrl('appointments/appointment-booking/calendar') }}" class="CdsDashboardAppointment-system-btn CdsDashboardAppointment-system-btn-primary">
                    <i class="fa-eye fa-solid me-1"></i>
                    View Appointment Calendar
                </a>
                @endif
            </div>
        </div>
  
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="CdsDashboardAppointment-system-table-container cdsfeedoverflow">
        
        <!-- Status Tabs -->
        <div class="cds-t25n-content-professional-profile-container-main-navigation mt-5">
            @php
            $statuses = [
                'all' => 'All',
                'draft' => 'Draft',
                'approved' => 'Upcoming',
                'awaiting' => 'Awaiting',
                'cancelled' => 'Cancelled',
                'archieved' => 'Archieved',
                'completed' => 'Completed',
                'non-conducted' => 'Non Conducted',
            ];
            $currentStatus = request()->query('status', 'all');
            @endphp

            <ul class="status-tabs cdsappointmentTab">
                @foreach ($statuses as $key => $label)
                    @php
                        $isActive = ($currentStatus === null && $key === 'all') || $currentStatus === $key;
                        $count = $appointmentsCount[$key] ?? 0;
                    @endphp
                    <li class="{{ $isActive ? 'cds-active' : '' }}">
                        <a href="{{ baseUrl('appointments/appointment-booking' . ($key !== 'all' ? '?status=' . $key : '')) }}">
                            {{ $label }} ({{ $count }})
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- div table -->
        <div class="cdsTYDashboard-table">
            <div class="cdsTYDashboard-table-wrapper">
                <div class="cdsTYDashboard-table-header">
                    <div class="cdsTYDashboard-table-cell">Client</div>
                    <div class="cdsTYDashboard-table-cell">Service</div>
                    <div class="cdsTYDashboard-table-cell">Date & time</div>
                    <div class="cdsTYDashboard-table-cell">Duration</div>
                    <div class="cdsTYDashboard-table-cell">Status</div>
                    <div class="cdsTYDashboard-table-cell">Payment</div>
                    <div class="cdsTYDashboard-table-cell">Actions</div>

                </div>
                <div class="cdsTYDashboard-table-body">
                    <div class="card-table" id="appointmentGridBody"></div>
                    <div id="common-skeleton-loader" style="display:none;">
                        @include('components.loaders.appointment-booking-loader')              
                    </div>
                </div> 
            </div>
        </div>

        <!-- Desktop Grid View -->
        {{--<div class="CdsDashboardAppointment-system-desktop-wrapper">
            <div class="CdsDashboardAppointment-system-grid">
                <!-- Header Row -->
                <div class="CdsDashboardAppointment-system-grid-header">
                    <div class="CdsDashboardAppointment-system-grid-header-item">
                        <input type="checkbox" class="CdsDashboardAppointment-system-checkbox" id="selectAll">
                    </div>
                    <div class="CdsDashboardAppointment-system-grid-header-item">CLIENT</div>
                    <div class="CdsDashboardAppointment-system-grid-header-item">SERVICE</div>
                    <div class="CdsDashboardAppointment-system-grid-header-item">DATE & TIME</div>
                    <div class="CdsDashboardAppointment-system-grid-header-item">DURATION</div>
                    <div class="CdsDashboardAppointment-system-grid-header-item">STATUS</div>
                    <div class="CdsDashboardAppointment-system-grid-header-item">PAYMENT</div>
                    <div class="CdsDashboardAppointment-system-grid-header-item">ACTIONS</div>
                </div>

                <!-- Body Rows -->
                <div class="CdsDashboardAppointment-system-grid-body" id="appointmentGridBody">
                    <!-- AJAX content loads here -->
                </div>
                <div id="common-skeleton-loader" style="display:none;">
                    @include('components.loaders.appointment-booking-loader')              
                </div>
            </div>
        </div>--}}

        <!-- Mobile Card View -->
        <div class="CdsDashboardAppointment-system-mobile-cards" id="mobileCards">
            <!-- Mobile cards will be dynamically generated -->
        </div>
        
        <div class="CdsDashboardAppointment-system-pagination">
            <div class="CdsDashboardAppointment-system-pagination-info">
                <span id="pageinfo"></span>
            </div>
            <div class="CdsDashboardAppointment-system-pagination-controls">
                <button class="CdsDashboardAppointment-system-pagination-btn previous" disabled>Previous</button>
                <input onblur="changePage('goto')" min="1" type="number" id="pageno" 
                       class="CdsDashboardAppointment-system-pagination-btn text-center" 
                       style="width: 60px; padding: 8px 4px;" />
                <button class="CdsDashboardAppointment-system-pagination-btn next">Next</button>
            </div>
        </div>
    </div>

			</div>
	
	</div>
  </div>
</div>


@endsection

@section('javascript')
<link rel="stylesheet" href="{{ url('assets/css/appointment-booking.css') }}" />
<script type="text/javascript">
let searchTimeout;
let currentFocus = -1;
let searchResults = [];
const $searchInput = $('#search-input');
const $searchClear = $('#search-clear');
const $dropdown = $('#autocomplete-dropdown');
const $loading = $('.CdsDashboardAppointment-system-autocomplete-loading');
const $results = $('.CdsDashboardAppointment-system-autocomplete-results');
const $noResults = $('.CdsDashboardAppointment-system-autocomplete-no-results');
const $recentSection = $('#recent-searches');
const $searchSection = $('.CdsDashboardAppointment-system-autocomplete-search-section');
$(document).ready(function() {
    // Pagination handlers
    $searchInput.on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        updateClearButton();
        
        if (query.length === 0) {
            showRecentSearches();
            return;
        }
        
        if (query.length < 2) {
            hideDropdown();
            return;
        }
        
        // Show search section and hide recent
        $recentSection.hide();
        $searchSection.show();
        
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });
    $searchClear.on('click', function() {
        $searchInput.val('').trigger('input').focus();
        loadData(1);
    });
    $(".next").click(function() {
        if (!$(this).hasClass('disabled')) {
            changePage('next');
        }
    });
    
    $(".previous").click(function() {
        if (!$(this).hasClass('disabled')) {
            changePage('prev');
        }
    });
    
    // Search functionality
    // $("#search-input").on('input', debounce(function() {
    //     loadData();
    // }, 300));
    
    // Select all functionality
    $("#selectAll").change(function() {
        $(".row-checkbox").prop("checked", $(this).is(":checked"));
        updateSelectAllCheckbox();
    });
});

// Load initial data
loadData();

function loadData(page = 1) {
    var search = $("#searchInput").val();
    var status = "";
     // Check if Feed Post By filter is active
    var feedPostByDropdown = document.querySelector('.cdsTYDashboardDropdownsDropdown');
    if (feedPostByDropdown) {
        const selectedItem = feedPostByDropdown.querySelector('.cdsTYDashboardDropdownsDropdownItem.cdsTYDashboardDropdownsActive');
        if (selectedItem) {
            const selectedValue = selectedItem.getAttribute('data-value');
            if (selectedValue !== 'all') {
                status = selectedValue;
            }
        }
    }

    $.ajax({
        type: "POST",
        url: BASEURL + '/appointments/appointment-booking/ajax-list?page=' + page,
        data: {
            _token: csrf_token,
            search: search,
            status: "{{ $currentStatus }}",
            service_id:$('#parent_service_id').val(),
            sub_service_id:$('#sub_service_id').val(),
            filter_status:status,
            start_date:$("#startDate").val(),
            end_date:$("#endDate").val()
        },
        dataType: 'json',
        beforeSend: function() {
            // $("#appointmentGridBody").html(
            //     '<div class="CdsDashboardAppointment-system-grid-row">' +
            //     '<div class="CdsDashboardAppointment-system-grid-item" style="grid-column: 1 / -1; text-align: center;">' +
            //     '<i class="fa fa-spin fa-spinner fa-3x"></i>' +
            //     '</div></div>'
            // );
            $("#common-skeleton-loader").show();
        },
        success: function(data) {
            $("#appointmentGridBody").html(data.contents);
             $("#common-skeleton-loader").hide();
            if (data.total_records > 0) {
                var pageinfo = `Showing ${data.current_page} of ${data.last_page} from ${data.total_records} records`;
                $("#pageinfo").html(pageinfo);
                $("#pageno").val(data.current_page);
                $("#pageno").attr("max", data.last_page);
                
                // Update pagination buttons
                $(".next").toggleClass("disabled", data.current_page >= data.last_page);
                $(".previous").toggleClass("disabled", data.current_page <= 1);
                
                // Generate mobile cards
                generateMobileCards();
                attachActionButtonListeners();
            } else {
                $("#appointmentGridBody").html(
                    '<div class="CdsDashboardAppointment-system-grid-row">' +
                    '<div class="CdsDashboardAppointment-system-grid-item" style="grid-column: 1 / -1; text-align: center; color: #dc2626;">' +
                    'No appointments found' +
                    '</div></div>'
                );
                $("#pageinfo").html("No appointments found");
            }
        },
        error: function() {
            errorMessage("Failed to load appointments");
        }
    });
}

function changePage(action) {
    var page = parseInt($("#pageno").val());
    if (action == 'prev') page--;
    if (action == 'next') page++;
    
    if (!isNaN(page) && page > 0) {
        loadData(page);
    } else {
        errorMessage("Invalid Page Number");
    }
}

function updateSelectAllCheckbox() {
    const allCheckboxes = document.querySelectorAll('.CdsDashboardAppointment-system-grid-body .row-checkbox');
    const checkedCheckboxes = document.querySelectorAll('.CdsDashboardAppointment-system-grid-body .row-checkbox:checked');
    document.getElementById('selectAll').checked = allCheckboxes.length === checkedCheckboxes.length && allCheckboxes.length > 0;
}

function generateMobileCards() {
    const mobileCardsContainer = document.getElementById('mobileCards');
    const gridRows = document.querySelectorAll('.CdsDashboardAppointment-system-grid-row');
    
    mobileCardsContainer.innerHTML = '';
    
    gridRows.forEach((row) => {
        const card = row.querySelector('.mobile-card-template');
        if (card) {
            mobileCardsContainer.appendChild(card.cloneNode(true));
        }
    });
}

function attachActionButtonListeners() {
    // Checkbox listeners
    $('.row-checkbox').off('change').on('change', function() {
        updateSelectAllCheckbox();
    });
    
    // Action button listeners are handled by inline onclick
}

function exportData() {
    const selectedRows = [];
    document.querySelectorAll('.CdsDashboardAppointment-system-grid-row').forEach(row => {
        const checkbox = row.querySelector('.row-checkbox');
        if (checkbox && checkbox.checked) {
            selectedRows.push(row);
        }
    });
    
    if (selectedRows.length === 0) {
        alert('Please select at least one appointment to export.');
        return;
    }
    
    // Implement export logic here
    alert(`Exporting ${selectedRows.length} appointment(s)...`);
}

function confirmAction(element) {
    const href = element.getAttribute('data-href');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            window.location.href = href;
        }
    });
}

function showPopup(url) {
    // Implement popup logic
    window.open(url, 'popup', 'width=600,height=400');
}

// Debounce helper
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showRecentSearches() {
    if (recentSearches.length === 0) {
        hideDropdown();
        return;
    }
    
    let html = '';
    recentSearches.forEach(search => {
        html += `
            <div class="CdsDashboardAppointment-system-autocomplete-recent-item" 
                  data-search-id="${search.id}"
                  data-search-text="${search.text}">
                <span class="icon">🕐</span>
                <span>${search.text}</span>
            </div>
        `;
    });
    
    $('.CdsDashboardAppointment-system-autocomplete-recent-items').html(html);
    $recentSection.show();
    $searchSection.hide();
    $dropdown.addClass('show');
    
    // Attach click handlers
    $('.CdsDashboardAppointment-system-autocomplete-recent-item').on('click', function() {
        const searchText = $(this).data('search-text');
        $searchInput.val(searchText).trigger('input');
    });
}
 // Perform search
function performSearch(query) {
    showLoading();
    $.ajax({
        type: 'POST',
        url: BASEURL + '/appointments/appointment-booking/search-appointments',
        data: {
            search: query,
            status:"{{ $currentStatus }}",
            _token: csrf_token
        },
        success: function(response) {
            if (response.status && response.records.length > 0) {
                displayResults(response.records, query);
            } else {
                showNoResults();
            }
        },
        error: function() {
            showNoResults();
        }
    });
}

// Display search results
function displayResults(data, query) {
    searchResults = data;
    currentFocus = -1;
    
    let html = '';
    data.forEach((item, index) => {
        const highlightedId = highlightText(`${item.unique_id}`, query);
        const highlightedClient = highlightText(item.client.first_name + " " + item.client.last_name, query);
        const highlightedProfessional = highlightText(item.professional.first_name + " " + item.professional.last_name, query);
        
        // Generate initials for avatar
        const initials = (item.professional.first_name.charAt(0) + item.professional.last_name.charAt(0)).toUpperCase();
        
        html += `
            <div class="CdsDashboardAppointment-system-autocomplete-item" 
                  data-index="${index}"
                  data-appointment-id="${item.unique_id}">
                <div class="CdsDashboardAppointment-system-autocomplete-content">
                    <div class="CdsDashboardAppointment-system-autocomplete-avatar" 
                          style="background: #5865F2">
                        ${initials}
                    </div>
                    <div class="CdsDashboardAppointment-system-autocomplete-info">
                        <div class="CdsDashboardAppointment-system-autocomplete-primary">
                            <span>${highlightedClient}</span>
                            <span class="CdsDashboardAppointment-system-autocomplete-id">#${highlightedId}</span>
                        </div>
                        <div class="CdsDashboardAppointment-system-autocomplete-secondary">
                            <span>Dr. ${highlightedProfessional}</span>
                            <span>📅 ${item.appointment_date}</span>
                            <span class="CdsDashboardAppointment-system-autocomplete-badge CdsDashboardAppointment-system-autocomplete-badge-${item.status}">
                                ${item.status}
                            </span>
                        </div>
                    </div>
                </div>
                <a href="${BASEURL}/appointments/appointment-booking/view/${item.unique_id}" 
                   class="CdsDashboardAppointment-system-autocomplete-view-link"
                   title="View Details">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
        `;
    });
    
    $results.html(html).addClass('show');
    $loading.removeClass('show');
    $noResults.removeClass('show');
    $dropdown.addClass('show');
    
    // Attach click handlers
    // Handle clicking on the main content area
    $('.CdsDashboardAppointment-system-autocomplete-content').on('click', function(e) {
        e.stopPropagation();
        const $item = $(this).closest('.CdsDashboardAppointment-system-autocomplete-item');
        selectItem($item.data('index'));
    });
    
    // Handle clicking on the view link
    $('.CdsDashboardAppointment-system-autocomplete-view-link').on('click', function(e) {
        e.stopPropagation();
        // The href will handle navigation
    });
    
    // Handle hover effect for the entire item
    $('.CdsDashboardAppointment-system-autocomplete-item').on('mouseenter', function() {
        $(this).addClass('hover');
    }).on('mouseleave', function() {
        $(this).removeClass('hover');
    });
}

// Highlight matching text
function highlightText(text, query) {
    if (!text) return '';
    console.log("Highlight Text");
    console.log(text,query);

    const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
    return text.replace(regex, '<span class="CdsDashboardAppointment-system-autocomplete-highlight">$1</span>');
}

// Escape regex special characters
function escapeRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Show loading state
function showLoading() {
    $dropdown.addClass('show');
    $loading.addClass('show');
    $results.removeClass('show');
    $noResults.removeClass('show');
}

// Show no results
function showNoResults() {
    $loading.removeClass('show');
    $results.removeClass('show');
    $noResults.addClass('show');
}

// Hide dropdown
function hideDropdown() {
    $dropdown.removeClass('show');
    currentFocus = -1;
}

// Select an item
function selectItem(index) {
    if (index >= 0 && index < searchResults.length) {
        const item = searchResults[index];
        const searchText = `${item.client.first_name} ${item.client.last_name} - #${item.unique_id}`;
        
        // Save to recent searches
        // saveRecentSearch({
        //     id: item.unique_id,
        //     text: searchText
        // });
        
        $searchInput.val(item.unique_id);
        hideDropdown();
        
        // Load the specific appointment
        loadData(1);
    }
}
function updateClearButton() {
    if ($searchInput.val().length > 0) {
        $searchClear.show();
    } else {
        $searchClear.hide();
    }
}



// Single event handler for main service change
    $('#parent_service_id').on('change', function () {
        var service_id = $(this).val();
        if (service_id) {
            serviceList(service_id, 'sub_service_id');
        } else {
            // If no service selected, clear sub-service and refresh
            $('#sub_service_id').html('<option value="">All SubService</option>');
        }
    });

    function serviceList(service_id, id) {
      
    $.ajax({
        url: "{{ baseUrl('cases/fetch-sub-service') }}",
        data: {
            service_id: service_id
        },
        dataType: "json",
        beforeSend: function() {
            console.log('AJAX request started');
            $("#" + id).html('');
        },
        success: function(response) {
            console.log('AJAX response received:', response);
            if (response.status == true) {
                $("#" + id).html(response.options);
                // Clear sub-service selection and refresh cases list when main service changes
                $("#" + id).val('').trigger('change');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            console.error('Response text:', xhr.responseText);
        }
    });
}
</script>
@endsection