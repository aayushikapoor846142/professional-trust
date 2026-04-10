@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('reviews') !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/19-CDS-send-invitation.css') }}">
@endsection
@section('content')
<!-- Content -->
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
            <div class="alert alert-warning mb-3">
                <strong>⚠ Review Management Limited</strong><br>
                {{ $reviewFeatureStatus['message'] ?? 'You need to upgrade your subscription to submit reviews.' }}
                @if(isset($reviewFeatureStatus['current_count']) && isset($reviewFeatureStatus['limit']))
                    <br>Current Usage: {{ $reviewFeatureStatus['current_count'] }} / {{ $reviewFeatureStatus['limit'] == 'unlimited' ? 'Unlimited' : $reviewFeatureStatus['limit'] }}
                @endif
            </div>
        </div>
    @else
        @if(isset($reviewFeatureStatus['has_subscription']) && $reviewFeatureStatus['has_subscription'])
            <div class="container">
                <div class="alert alert-info mb-3">
                    <strong>📊 Review Management Status</strong><br>
                    <strong>Plan:</strong> {{ $reviewFeatureStatus['plan_title'] }}<br>
                    <strong>Current Reviews:</strong> {{ $reviewFeatureStatus['current_count'] }} / {{ $reviewFeatureStatus['limit'] == 'unlimited' ? 'Unlimited' : $reviewFeatureStatus['limit'] }}
                    @if($reviewFeatureStatus['limit'] != 'unlimited')
                        <br><strong>Remaining Slots:</strong> {{ $reviewFeatureStatus['remaining'] }}
                        <br><strong>Usage:</strong> {{ $reviewFeatureStatus['usage_percentage'] }}%
                        @if($reviewFeatureStatus['usage_percentage'] >= 80)
                            <span class="badge bg-warning">High Usage</span>
                        @endif
                    @endif
                    <br><strong>Status:</strong> {{ $reviewFeatureStatus['message'] }}
                </div>
            </div>
        @elseif(isset($reviewFeatureStatus['current_count']) && isset($reviewFeatureStatus['limit']))
            <div class="container">
                <div class="alert alert-info mb-3">
                    <strong>📊 Review Management Status</strong><br>
                    Current Reviews: {{ $reviewFeatureStatus['current_count'] }} / {{ $reviewFeatureStatus['limit'] == 'unlimited' ? 'Unlimited' : $reviewFeatureStatus['limit'] }}
                    @if($reviewFeatureStatus['limit'] != 'unlimited' && isset($reviewFeatureStatus['remaining']))
                        <br>Remaining Slots: {{ $reviewFeatureStatus['remaining'] }}
                    @endif
                </div>
            </div>
        @endif
    @endif
@endif
			  <!-- Stats Grid -->
    <div  id="stats-container">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="CdsSendInvitation-stat-card">
                <div class="CdsSendInvitation-stat-icon CdsSendInvitation-stat-icon-primary">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="CdsSendInvitation-stat-content">
                    <div class="CdsSendInvitation-stat-value" id="total-invitations">0</div>
                    <div class="CdsSendInvitation-stat-label">Total Reviews</div>
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
                    <div class="CdsSendInvitation-stat-label">Pending</div>
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
                    <div class="CdsSendInvitation-stat-label">Reviews Received</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="CdsSendInvitation-stat-card">
                <div class="CdsSendInvitation-stat-icon CdsSendInvitation-stat-icon-success">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
                <div class="CdsSendInvitation-stat-content">
                    <div class="CdsSendInvitation-stat-value" id="spamCount">0</div>
                    <div class="CdsSendInvitation-stat-label">Reviews Mark as Spam</div>
                </div>
            </div>
        </div>
    </div>

  </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
   <div class="cds-ty-dashboard-box CdsSendInvitation-list-card">
                <div class="cds-ty-dashboard-box-header CdsSendInvitation-list-header">
                    <div class="CdsSendInvitation-list-title-section">
                        <h2 class="CdsSendInvitation-list-title">Spam Marked Review </h2>
                        <p class="CdsSendInvitation-list-subtitle">Manage and track all your given reviews</p>
                    </div>
                    <div class="cds-action-elements" style="display: none;">
                        <span class="font-size-sm mr-3">
                            <span id="datatableCounter">0</span>
                            Selected
                        </span>
                        <a class="CdsTYButton-btn-primary CdsTYButton-border-thick" data-href="{{ baseUrl('review-received/delete-multiple') }}" onclick="deleteMultiple(this)" href="javascript:;">
                            Delete
                        </a>
                    </div>
                    <div class="CdsSendInvitation-list-actions mt-3">
                        <div class="CdsSendInvitation-search-box">
                            <i class="fa-solid fa-search CdsSendInvitation-search-icon"></i>
                            <input type="text" placeholder="Search by email..." class="CdsSendInvitation-search-input" id="datatableSearch">
                        </div>
                    </div>
                </div>
                <div class="cds-ty-dashboard-box-body">
                    <!-- div table -->
                    <div class="cdsTYDashboard-table">
                        <div class="cdsTYDashboard-table-wrapper">
                            <div class="cdsTYDashboard-table-header">
                                <div class="cdsTYDashboard-table-cell cdsCheckbox">
                                    <div class="custom-control custom-checkbox">
                                        {!! FormHelper::formCheckbox([
                                            'id' => 'datatableCheckAll',
                                               'checkbox_class' => 'datatableCheckAll'
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="professional_id" data-order="asc" onclick="sortTable(this)">Professional Details <span class="sort-arrow"></span></div>
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

                   
                </div>
                <div class="cds-ty-dashboard-box-footer">
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
                                <input onblur="changePage('goto')" min="1" type="number" id="pageno" class="form-control text-center" style="width: 60px;" />
                            </div>
                            <button class="CdsSendInvitation-pagination-btn next">
                                Next
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
      
			</div>
	
	</div>
  </div>
</div>
 


@endsection
@section('javascript')
<script type="text/javascript">
const cookiePrefix = 'reviews_'; 
let sortColumn = getCookie(cookiePrefix + 'sortColumn') || 'created_at';
let sortDirection = getCookie(cookiePrefix + 'sortDirection') || 'desc';

   function showEditCasePopup() {
            document.getElementById('postCaseModal').classList.add('CDSPostCaseNotifications-list-view-active');
                        document.body.style.overflow = 'hidden';
            
        }

        function closeEditModal(modalId) {
            document.getElementById(modalId || 'postCaseModal').classList.remove('CDSPostCaseNotifications-list-view-active');
            document.body.style.overflow = '';
        }
        
function showEditForm(id) {
    const form = $('#editForm' + id);
    if (form.is(':visible')) {
        form.hide();
    } else {
        form.show();
    }
}
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


    loadData();
    $(".fetch-professional").on("click", function() {
        $.ajax({
            url: $(this).data('href'),
            dataType: 'json',
            success: function(result) {
                if (result.status == true) {
                    successMessage(result.message);
                    loadData();
                } else {
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
    $(".datatableCheckAll").change(function() {
        if ($(this).is(":checked")) {
            $(".row-checkbox").prop("checked", true);
        } else {
            $(".row-checkbox").prop("checked", false);
        }
        updateSelectionCounter();
    });
});
function updateSelectionCounter() {
    const selectedIds = new Set();
    $(".row-checkbox:checked").each(function () {
        selectedIds.add($(this).data("id"));
    });
    const count = selectedIds.size;
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

function loadData(page = 1) {
    var search = $("#datatableSearch").val();
    var status = $("#statusFilter").val();
    $.ajax({
        type: "POST",
        url: BASEURL + '/reviews/spam-reviews/ajax-list?page=' + page,
        data: {
            _token: csrf_token,
            search: search,
            status: status,
                     sort_direction:sortDirection,
		sort_column:sortColumn
        },
        dataType: 'json',
        beforeSend: function() {
            $("#common-skeleton-loader").show();
        },
        success: function(data) {
            $(".card-table").html(data.contents);
            $("#common-skeleton-loader").hide();
            if(data.contents) {
                $("#total-invitations").text(data.total || 0);
                $("#pending-invitations").text(data.pending || 0);
                $("#reviews-given").text(data.reviewsGiven || 0);
                            $("#spamCount").text(data.spamCount || 0);
            }
            
            // Update review feature status alert if available
            if(data.reviewFeatureStatus) {
                updateReviewFeatureAlert(data.reviewFeatureStatus);
            }
            
            if (data.total_records > 0) {
                var pageinfo = 'Page <strong>' + data.current_page + '</strong> of <strong>' + data.last_page + '</strong> (' + data.total_records + ' records)';
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
            bindCheckboxEvents();
        },
        error: function() {
            $(".card-table").html('<div class="text-center text-danger py-5">Error loading data. Please try again.</div>');
        }
    });
}
function bindCheckboxEvents() {
    $(".row-checkbox").off('change').on('change', function() {
        updateSelectionCounter();
    });
}

function updateReviewFeatureAlert(reviewFeatureStatus) {
    let alertHtml = '';
    
    if (!reviewFeatureStatus.allowed) {
        alertHtml = `
            <div class="alert alert-warning mb-3">
                <strong>⚠ Review Management Limited</strong><br>
                ${reviewFeatureStatus.message || 'You need to upgrade your subscription to submit reviews.'}
                ${reviewFeatureStatus.current_count !== undefined && reviewFeatureStatus.limit !== undefined ? 
                    '<br>Current Usage: ' + reviewFeatureStatus.current_count + ' / ' + (reviewFeatureStatus.limit == -1 ? 'Unlimited' : reviewFeatureStatus.limit) : ''}
            </div>
        `;
    } else if (reviewFeatureStatus.has_subscription) {
        let usageBadge = '';
        if (reviewFeatureStatus.limit != -1 && reviewFeatureStatus.usage_percentage >= 80) {
            usageBadge = '<span class="badge bg-warning">High Usage</span>';
        }
        
        alertHtml = `
            <div class="alert alert-info mb-3">
                <strong>📊 Review Management Status</strong><br>
                <strong>Plan:</strong> ${reviewFeatureStatus.plan_title}<br>
                <strong>Current Reviews:</strong> ${reviewFeatureStatus.current_count} / ${reviewFeatureStatus.limit == -1 ? 'Unlimited' : reviewFeatureStatus.limit}
                ${reviewFeatureStatus.limit != -1 ? 
                    '<br><strong>Remaining Slots:</strong> ' + reviewFeatureStatus.remaining +
                    '<br><strong>Usage:</strong> ' + reviewFeatureStatus.usage_percentage + '%' + usageBadge : ''}
                <br><strong>Status:</strong> ${reviewFeatureStatus.message}
            </div>
        `;
    } else if (reviewFeatureStatus.current_count !== undefined && reviewFeatureStatus.limit !== undefined) {
        alertHtml = `
            <div class="alert alert-info mb-3">
                <strong>📊 Review Management Status</strong><br>
                Current Reviews: ${reviewFeatureStatus.current_count} / ${reviewFeatureStatus.limit == -1 ? 'Unlimited' : reviewFeatureStatus.limit}
                ${reviewFeatureStatus.limit != -1 && reviewFeatureStatus.remaining !== undefined ? 
                    '<br>Remaining Slots: ' + reviewFeatureStatus.remaining : ''}
            </div>
        `;
    }
    
    // Update or create the alert container
    let alertContainer = $('.container').first();
    let existingAlert = alertContainer.find('.alert');
    if (existingAlert.length > 0) {
        existingAlert.replaceWith(alertHtml);
    } else if (alertHtml) {
        alertContainer.prepend(alertHtml);
    }
}

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
function toggleDropdown(event) {
    event.stopPropagation();
    const button = event.currentTarget;
    const dropdown = button.nextElementSibling;
    const isOpen = dropdown.classList.contains('show');
    document.querySelectorAll('.CdsSendInvitation-dropdown.show').forEach(d => {
        d.classList.remove('show');
    });
    if (!isOpen) {
        dropdown.classList.add('show');
    }
}
document.addEventListener('click', function(event) {
    if (!event.target.closest('.CdsSendInvitation-action-menu')) {
        document.querySelectorAll('.CdsSendInvitation-dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
    }
});
function handleAction(action, email, id) {
    document.querySelectorAll('.CdsSendInvitation-dropdown.show').forEach(dropdown => {
        dropdown.classList.remove('show');
    });
    switch(action) {
        case 'view-stats':
            window.location.href = BASEURL + '/invitations-sent/stats/' + id;
            break;
        case 'view-reviews':
            window.location.href = BASEURL + '/invitations-sent/reviews/' + id;
            break;
    }
}

</script>
@endsection


