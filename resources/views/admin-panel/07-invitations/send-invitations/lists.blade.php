@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('reviews') !!}
@endsection
@section("styles")
<link rel="stylesheet" href="{{ url('assets/css/19-CDS-send-invitation.css') }}">
@endsection
@section('content')
 <div class="ch-action">
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.send-invitations',
                        'module' => 'professional-send-invitations',
                        'action' => 'add'
                    ]))
                    @if($canAddReview)
                    <a href="{{ baseUrl('send-invitations/add') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-plus fa-solid me-1"></i>
                        Send New Invitation
                    </a>
                    @endif
                    @endif
                </div> 
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
	@if ($errors->has('emails.*'))
        @foreach ($errors->get('emails.*') as $messages)
            @foreach ($messages as $message)
                <div class="alert alert-danger">{{ $message }}</div>
            @endforeach
        @endforeach
    @endif
<!-- Review Limit Alert Box -->
@if(isset($reviewFeatureStatus))
    @if(!$canAddReview)
        <div class="container">
            <div class="alert alert-danger mb-3">
                <strong>⚠ Review Management</strong><br>
                {{ $reviewFeatureStatus['message'] }}
            </div>
        </div>
    @else
       
            <div class="container">
                <div class="alert alert-warning mb-3">
                    <strong>⚠ Review Management Status</strong><br>
                   {{ $reviewFeatureStatus['message'] }}
                </div>
            </div>
       
    @endif
@endif
 <div id="stats-container">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="CdsSendInvitation-stat-card">
                <div class="CdsSendInvitation-stat-icon CdsSendInvitation-stat-icon-primary">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="CdsSendInvitation-stat-content">
                    <div class="CdsSendInvitation-stat-value" id="total-invitations">0</div>
                    <div class="CdsSendInvitation-stat-label">Total Invitations</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="CdsSendInvitation-stat-card">
                <div class="CdsSendInvitation-stat-icon CdsSendInvitation-stat-icon-warning">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="CdsSendInvitation-stat-content">
                    <div class="CdsSendInvitation-stat-value" id="pending-invitations">0</div>
                    <div class="CdsSendInvitation-stat-label">Pending invitation</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="CdsSendInvitation-stat-card">
                <div class="CdsSendInvitation-stat-icon CdsSendInvitation-stat-icon-success">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
                <div class="CdsSendInvitation-stat-content">
                    <div class="CdsSendInvitation-stat-value" id="reviews-given">0</div>
                    <div class="CdsSendInvitation-stat-label">Given Reviews</div>
                </div>
            </div>
        </div>
    

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="CdsSendInvitation-stat-card">
                <div class="CdsSendInvitation-stat-icon CdsSendInvitation-stat-icon-success">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
                <div class="CdsSendInvitation-stat-content">
                    <div class="CdsSendInvitation-stat-value" id="reviews-accepted">0</div>
                    <div class="CdsSendInvitation-stat-label">Accepted invitation</div>
                </div>
            </div>
        </div>
    </div>
 <div class="cds-ty-dashboard-box-header CdsSendInvitation-list-header">
                    <div class="CdsSendInvitation-list-title-section">
                        <h2 class="CdsSendInvitation-list-title">Invitation History</h2>
                        <p class="CdsSendInvitation-list-subtitle">Manage and track all your sent invitations</p>
                    </div>
                           @if(checkPrivilege([
                        'route_prefix' => 'panel.send-invitations',
                        'module' => 'professional-send-invitations',
                        'action' => 'delete'
                    ]))
                    <div class="cds-action-elements" style="display: none;">
                        <span class="font-size-sm mr-3">
                            <span id="datatableCounter">0</span>
                            Selected
                        </span>
                        <a class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-multi-delete ml-2" data-href="{{ baseUrl('send-invitations/delete-multiple') }}"
                            onclick="deleteMultiple(this)" href="javascript:;">
                            Delete
                        </a>
                    </div>
                        @endif

                        @include("admin-panel.07-invitations.send-invitations.components.header-search") 
                    <!-- <div class="CdsSendInvitation-list-actions mt-3">
                        <div class="CdsSendInvitation-search-box">
                            <i class="fa-solid fa-search CdsSendInvitation-search-icon"></i>
                            <input type="text" placeholder="Search by email..." class="CdsSendInvitation-search-input" id="datatableSearch">
                        </div>
                    </div> -->
                </div>
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                    <!-- div table -->
                    <div class="cdsTYDashboard-table">
                        <div class="cdsTYDashboard-table-wrapper">
                            <div class="cdsTYDashboard-table-header">
                                <div class="cdsTYDashboard-table-cell cdsCheckbox">
                                    <div class="custom-control custom-checkbox">
                                        {!! FormHelper::formCheckbox([
                                            'name' => 'datatableCheckAll',
                                            'checkbox_class' => 'datatableCheckAll',
                                        ]) !!}
                                        {{--<label class="custom-control-label" for="datatableCheckAll"></label>
                                        <input type="checkbox" class="CdsSendInvitation-checkbox" id="datatableCheckAll">--}}
                                    </div>
                                </div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="email" data-order="asc" onclick="sortTable(this)">Client Details <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="status" data-order="asc" onclick="sortTable(this)">Status <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell">Sent Date</div>
                                <div class="cdsTYDashboard-table-cell">Response</div>
                                 <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Created At <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell">Actions</div>
                            </div>
                            <div class="cdsTYDashboard-table-body">
                                <div class="card-table"></div>
                                <div id="common-skeleton-loader" style="display:none;">
                                    @include('components.loaders.send-invitation-loader')              
                                </div>
                            </div> 
                        </div>
                    </div>
<div class="cds-ty-dashboard-box-footer">
                    <!-- Pagination -->
                    <div class="CdsSendInvitation-pagination-container">
                        <div class="CdsSendInvitation-pagination-info">
                            <span id="pageinfo"></span>
                        </div>
                        <div class="CdsSendInvitation-pagination">
                            <button class="CdsSendInvitation-pagination-btn previous" disabled>
                                <i class="fa-solid fa-chevron-left"></i>
                                Prev
                            </button>
                            <div class="d-flex align-items-center mx-3">
                                <input onblur="changePage('goto')" min="1" type="number" id="pageno"
                                    class="form-control text-center" style="width: 60px;" />
                            </div>
                            <button class="CdsSendInvitation-pagination-btn next">
                                Next
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <!-- End Pagination -->
                </div>
			</div>
	
	</div>
  </div>
</div>				

@endsection

@section('javascript')
<!-- Existing links -->
<!-- Custom Styles for Send Invitations -->
<script type="text/javascript">

const cookiePrefix = 'send_invitations_'; 
let sortColumn = getCookie(cookiePrefix + 'sortColumn') || 'created_at';
let sortDirection = getCookie(cookiePrefix + 'sortDirection') || 'desc';

    $(document).ready(function() {

              // Find the matching header cell
    const $el = $(".cdsTYDashboard-table-cell[data-column='" + sortColumn + "']");

    if ($el.length) {
        // Remove any existing arrow classes and text
        $('.sort-header').removeClass('sorted-asc sorted-desc')
                         .attr('data-order', 'asc')
                         .find('.sort-arrow').text('');
        // Apply current sort direction
  $el.attr('data-order', sortDirection)
           .addClass(sortDirection === 'asc' ? 'sorted-asc' : 'sorted-desc');
    }

        // Initialize
        loadData();
        
        // Event Handlers
        $(".fetch-professional").on("click", function() {
            $.ajax({
                url: $(this).data('href'),
                dataType:'json',
                success: function (result) {
                    if(result.status == true){
                        successMessage(result.message);
                        loadData();
                    }else{
                        errorMessage(result.message);
                    }
                },
            });
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
        
        $("#datatableSearch").keyup(function() {
            var value = $(this).val();
            if (value == '') {
                loadData();
            }
            if (value.length > 3) {
                loadData();
            }
        });

        $("#statusFilter").change(function() {
            loadData();
        });
$(document).on('change', '.datatableCheckAll', function () {

    $('.row-checkbox').prop('checked', this.checked);
    updateSelectionCounter();
});
$(document).on('change', '.row-checkbox', function () {
    let allChecked = $('.row-checkbox').length === $('.row-checkbox:checked').length;
    $('.datatableCheckAll').prop('checked', allChecked);
    updateSelectionCounter();
});
       
        
    });

    // Update selection counter
    function updateSelectionCounter() {
        const selectedIds = new Set();
          let count = $('.case-checkbox:checked').length;
     $("#datatableCounter").html(count);

        if (count > 0) {
            $(".cds-action-elements").show();
        } else {
            $(".cds-action-elements").hide();
        }
    }

       function sortTable(element) {
    var $el = $(element);
    var currentOrder = $el.attr('data-order');
    var columnName = $el.attr('data-column');
    
    var newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
    $el.attr('data-order', newOrder);
    
    // Reset others
     $('.sort-header').not($el)
        .attr('data-order', 'asc')
        .removeClass('sorted-desc sorted-asc');
    
    // Update current - fix the arrow text
  
    $el.removeClass('sorted-desc sorted-asc').addClass(newOrder === 'asc' ? 'sorted-asc' : 'sorted-desc');
    
    // Set global sort variables
    sortColumn = columnName;
    sortDirection = newOrder;
   setCookie(cookiePrefix + 'sortColumn', columnName, 24);
setCookie(cookiePrefix + 'sortDirection', newOrder, 24);
   

    loadData();
}

  function setCookie(name, value, hours = 24) {
    const expires = new Date(Date.now() + hours * 60 * 60 * 1000).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/`;
}

function getCookie(name) {
    return document.cookie
        .split('; ')
        .find(row => row.startsWith(name + '='))
        ?.split('=')[1];
}


    // Load data function
    function loadData(page = 1) {
        var search = $("#searchInput").val();
        let status = $('.CdsTYDashboard-status-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();
        $.ajax({
            type: "POST",
            url: BASEURL + '/reviews/invitations-sent/ajax-list?page=' + page,
            data: {
                _token: csrf_token,
                search: search,
                status: status,
                sort_direction:sortDirection,
		        sort_column:sortColumn,
                start_date:$("#startDate").val(),
                end_date:$("#endDate").val(),
                rating:$('.CdsTYDashboard-special-filters-star-rating.active').data('rating')
            },
            dataType: 'json',
            beforeSend: function() {
                $("#common-skeleton-loader").show();
            },
            success: function(data) {
                $(".card-table").html(data.contents);
                $("#common-skeleton-loader").hide();
                // Update stats if provided
                if(data.contents) {
                     
                    $("#total-invitations").text(data.total || 0);
                    $("#pending-invitations").text(data.pending || 0);
                    $("#reviews-given").text(data.reviewsGiven || 0);
               $("#reviews-accepted").text(data.reviewsAccepted || 0);

                   
                    
                }
           
                if (data.total_records > 0) {
   
                    var pageinfo = 'Page <strong>' + data.current_page + '</strong> of <strong>' + data.last_page + 
                        '</strong> (' + data.total_records + ' records)';
                    $("#pageinfo").html(pageinfo);
                    $("#pageno").val(data.current_page);
                    
                    if (data.current_page < data.last_page) {
                        $(".next").removeClass("disabled").prop("disabled", false);
                    } else {
                        $(".next").addClass("disabled").prop("disabled", true);
                    }
                    
                    if (data.current_page > 1) {
                        $(".previous").removeClass("disabled").prop("disabled", false);
                    } else {
                        $(".previous").addClass("disabled").prop("disabled", true);
                    }
                    
                    $("#pageno").attr("max", data.last_page);
                } else {
                    $("#pageinfo").html('<strong>0</strong> records found');
                    $(".next, .previous").addClass("disabled").prop("disabled", true);
                }
                
                // Re-bind checkbox events
                bindCheckboxEvents();
            },
            error: function() {
                $(".card-table").html('<div class="text-center text-danger py-5">Error loading data. Please try again.</div>');
            }
        });
    }

    // Bind checkbox events
    function bindCheckboxEvents() {
        $(".row-checkbox").off('change').on('change', function() {
            updateSelectionCounter();
        });
    }

    // Change page
    function changePage(action) {
        var page = parseInt($("#pageno").val());
        if (action == 'prev') {
            page--;
        }
        if (action == 'next') {
            page++;
        }
        if (!isNaN(page) && page > 0) {
            loadData(page);
        } else {
            errorMessage("Invalid Page Number");
        }
    }

    // Toggle dropdown menu
    function toggleDropdown(event) {
        event.stopPropagation();
        const button = event.currentTarget;
        const dropdown = button.nextElementSibling;
        const isOpen = dropdown.classList.contains('show');
        
        // Close all other dropdowns
        document.querySelectorAll('.CdsSendInvitation-dropdown.show').forEach(d => {
            d.classList.remove('show');
        });
        
        // Toggle current dropdown
        if (!isOpen) {
            dropdown.classList.add('show');
        }
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.CdsSendInvitation-action-menu')) {
            document.querySelectorAll('.CdsSendInvitation-dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });

    // Handle dropdown actions
    function handleAction(action, email, id) {
        // Close the dropdown
        document.querySelectorAll('.CdsSendInvitation-dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
        
        // Handle the action
        switch(action) {
            case 'view-stats':
                // Navigate to stats page
                window.location.href = BASEURL + '/invitations-sent/stats/' + id;
                break;
            case 'view-reviews':
                // Navigate to reviews page
                window.location.href = BASEURL + '/invitations-sent/reviews/' + id;
                break;
        }
    }

    

    // Mark as complete
    function markAsComplete(e){
        var url = $(e).attr("data-href");
        if($(".row-checkbox:checked").length <= 0){
            warningMessage("No records selected");
            return false;
        }
        
        Swal.fire({
            title: 'Are you sure to mark as complete?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            confirmButtonClass: 'CdsTYButton-btn-primary',
            cancelButtonClass: 'CdsTYButton-btn-primary CdsTYButton-border-thick ml-1',
            buttonsStyling: false,
        }).then(function(result) {
            if(result.value){
                var row_ids = [];
                $(".row-checkbox:checked").each(function(){
                    row_ids.push($(this).val());
                });
                var ids = row_ids.join(",");
                
                $.ajax({
                    type: "POST",
                    url: url,
                    data:{
                        _token:csrf_token,
                        ids:ids,
                    },
                    dataType:'json',
                    success: function (response) {
                        if(response.status == true){
                            successMessage(response.message || "Marked as complete successfully");
                            loadData();
                        }else{
                            errorMessage(response.message);
                        }
                    },
                    error:function(){
                        errorMessage("Something went wrong. Please try again.");
                    }
                });
            }
        })
    }

    // Export data
    function exportData() {
        var search = $("#datatableSearch").val();
        var status = $("#statusFilter").val();
        
        window.location.href = BASEURL + '/invitations-sent/export?search=' + search + '&status=' + status;
    }

    // Add email field function (if needed for add form)
    function addEmailField() {
        const container = document.getElementById('email-container');
        const input = document.createElement('input');
        input.type = 'email';
        input.name = 'emails[]';
        input.placeholder = 'Enter email';
        input.className = 'form-control mb-2';
        container.appendChild(input);
    }
</script>
@endsection