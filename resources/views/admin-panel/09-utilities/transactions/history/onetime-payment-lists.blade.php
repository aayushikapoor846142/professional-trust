<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
<div class="CDSDashboardContainer-main-content">
<div class="CDSDashboardContainer-main-content-inner">   
<div class="CDSDashboardContainer-main-content-inner-header">
			@include("admin-panel.09-utilities.transactions.history.component.header-onetime-payment") 

				</div>
<div class="CDSDashboardContainer-main-content-inner-body">
  <div class="cdsTYDashboard-contribution-history-table-container">
            <div class="cds-ty-dashboard-box-body">
                <div class="cdsTYDashboard-table-wrapper">
                    <!-- div table -->
                    <div class="cdsTYDashboard-table">
                        <div class="cdsTYDashboard-table-wrapper">
                            <div class="cdsTYDashboard-table-header">
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="user_id" data-order="asc" onclick="sortTableOneTime(this)">Name <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="amount" data-order="asc" onclick="sortTableOneTime(this)">Amount Paid <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="tax" data-order="asc" onclick="sortTableOneTime(this)">Tax Amount <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="total_amount" data-order="asc" onclick="sortTableOneTime(this)">Total Amount <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTableOneTime(this)">Created On <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="status" data-order="asc" onclick="sortTableOneTime(this)">Status <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell cdsTYDashboard-table-cell-action">Action</div>
                            </div>
                            <div class="cdsTYDashboard-table-body" id="tableList"><div id="common-skeleton-loader" style="display:none;">
								@include('components.loaders.transaction-history-loader')              
							</div>
                            </div>  
							<div class="cdsTYDashboard-table-footer">
                    <div class="cdsTYDashboard-table-footer-count">
                        <span>Page:</span>
                        <span id="pageinfo"></span>
                    </div>
                    <div class="cdsTYDashboard-table-footer-nav">
                        <nav id="datatablePagination" aria-label="Activity pagination">
                            <div class="dataTables_paginate paging_simple_numbers" id="datatable_paginate">
                                <ul id="datatable_pagination" class="pagination datatable-custom-pagination justify-content-center justify-content-md-start">
                                    <li class="paginate_item page-item previous disabled">
                                        <a class="paginate_button page-link CdsTYButton-btn-primary" aria-controls="datatable" data-dt-idx="0" tabindex="0" id="datatable_previous"><span aria-hidden="true">Prev</span></a>
                                    </li>
                                    <li class="paginate_item page-item ms-2 me-2">
                                        <input onblur="changePage('goto')" min="1" type="number" id="pageno" class="form-control text-center" />
                                    </li>
                                    <li class="paginate_item page-item next disabled">
                                        <a class="paginate_button page-link CdsTYButton-btn-primary" aria-controls="datatable" data-dt-idx="3" tabindex="0"><span aria-hidden="true">Next</span></a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
                        </div>
                    </div>
                    <!-- # div table -->

                   

                    
                </div>
            </div>
             </div>
  

</div>
</div>
</div>
</div>











@push('scripts')
<script type="text/javascript">
var onetime_pass_to_dates = "";
	var onetime_pass_from_dates = "";
	var onetime_search = "";
	var onetime_sort_by_column = "all";
    var onetime_sort_order = "desc";
    var onetime_status = "all";
    
const cookiePrefix = 'onetime_payment_'; 
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
   
	let onetime_from_date = "";
	let onetime_filter_from_date = "";
	let onetime_filter_to_date = "";
	let onetime_to_date = "";  // to track the actual selected toDate

function destroyCalendar(inputId) {
  const input = document.getElementById(inputId);
  if (!input) return;
  const nextSibling = input.nextElementSibling;
  if (nextSibling && nextSibling.id && nextSibling.id.startsWith("datepicker-wrapper-")) {
    nextSibling.remove();
  }
}

// Initialize fromDate calendar
CustomCalendarWidget.initialize("oneTimeFromDate", {
  onDateSelect: function (selectedDate) {
    onetime_from_date = selectedDate;

    // If to_date exists and is before from_date, clear it
    if (onetime_to_date && onetime_to_date < onetime_from_date) {
      onetime_to_date = "";
      onetime_filter_to_date = "";
      // Also clear the toDate input value
      const toInput = document.getElementById("oneTimeToDate");
      if (toInput) toInput.value = "";
    }

    // Destroy existing toDate calendar
    destroyCalendar("oneTimeToDate");

    // Initialize toDate calendar with minDate = from_date
    CustomCalendarWidget.initialize("oneTimeToDate", {
      minDate: onetime_from_date,
      onDateSelect: function (selectedToDate) {
       onetime_to_date = selectedToDate;
        onetime_filter_to_date = selectedToDate;
      }
    });
  }
});

// Initialize toDate calendar on page load with current from_date (which is "")
CustomCalendarWidget.initialize("oneTimeToDate", {
  minDate: onetime_from_date || null,
  onDateSelect: function (selectedToDate) {
    onetime_to_date = selectedToDate;
    onetime_filter_to_date = selectedToDate;
  }
});


  })
  loadData();

	function setOneTimeStatusFilter(statuses)
    {
        $(".cds-status-filter").removeClass("cds-active");
        $(`.cds-status-filter[data-status="${statuses}"]`).addClass("cds-active");
        onetime_status = statuses;
        loadData();
    }

	function oneTimeDateFilter(){
		onetime_pass_to_dates = $("#oneTimeToDate").val();
		onetime_pass_from_dates = $("#oneTimeFromDate").val();
		loadData();
	}

	function clearOneTimeDateFilter()
	{
		onetime_pass_to_dates = "";
		onetime_pass_from_dates = "";
		$("#oneTimeToDate").val("");
		$("#oneTimeFromDate").val("");
		loadData();
	}

	function searchOneTimePayment(inputElement) {
		onetime_search = inputElement.value;
		loadData();
	}

	function sortTableOneTime(element) {
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

    var search = $("#searchInput").val();
     let status = $('.CdsTYDashboard-status-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();

    let price_range = $('.CdsTYDashboard-price-range:checked')
        .map(function () {
            return $(this).val();
        }).get();
  let hour_range = $('.CdsTYDashboard-hours-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();

        let min_range = $("#minPrice").val();
        let max_range = $("#maxPrice").val();
           let startDate = $("#startDate").val();
    let endDate = $("#endDate").val();

    $.ajax({
      type: "POST",
      url: BASEURL + '/transactions/history/ajax-list?page=' + page,
      data: {
        _token: csrf_token,
        search: search,
		status:onetime_status,
		onetime_pass_to_dates:onetime_pass_to_dates,
		onetime_pass_from_dates:onetime_pass_from_dates,
		onetime_sort_by_column:onetime_sort_by_column,
        onetime_sort_order:onetime_sort_order,
             sort_direction:sortDirection,
		sort_column:sortColumn,
      status:status,
    price_range:price_range,
    min_range:min_range,
    max_range:max_range,
    start_date:startDate,
    end_date:endDate,
    hour_range:hour_range
      },
      dataType: 'json',
      beforeSend: function() {
        var cols = $("#tableList thead tr > th").length;
        $("#common-skeleton-loader").show();
      },
      success: function(data) {
        $(".norecord").remove();  
        $("#tableList").html(data.contents);
		$("#common-skeleton-loader").hide();
        if (data.total_records > 0) {
          var pageinfo = data.current_page + " of " + data.last_page + " <small class='text-danger'>(" + data.total_records + " records)</small>";
          $("#pageinfo").html(pageinfo);
          $("#pageno").val(data.current_page);
          if (data.current_page < data.last_page) {
            $(".next").removeClass("disabled");
          } else {
            $(".next").addClass("disabled", "disabled");
          }
          if (data.current_page > 1) {
            $(".previous").removeClass("disabled");
          } else {
            $(".previous").addClass("disabled", "disabled");
          }
          $("#pageno").attr("max", data.last_page);
        } else {
          $(".cdsTYDashboard-table").find(".norecord").remove();
          var html = '<div class="cdsTYDashboard-empty-list norecord">No records available</div>';
          $(".cdsTYDashboard-table-body").append(html);
        }
      },
    });
  }

  function changePage(action) {
    var page = parseInt($("#pageno").val());
    if (action == 'prev') {
      page--;
    }
    if (action == 'next') {
      page++;
    }
    if (!isNaN(page)) {
      loadData(page);
    } else {
      errorMessage("Invalid Page Number");
    }

  }

  function confirmDelete(id) {
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!',
      confirmButtonClass: 'CdsTYButton-btn-primary',
      cancelButtonClass: 'CdsTYButton-btn-primary CdsTYButton-border-thick ml-1',
      buttonsStyling: false,
    }).then(function(result) {
      if (result.value) {
        $.ajax({
          type: "POST",
          url: BASEURL + '/support-payments/delete-user',
          data: {
            _token: csrf_token,
            user_id: id,
          },
          dataType: 'json',
          success: function(result) {
            if (result.status == true) {
              Swal.fire({
                type: "success",
                title: 'Deleted!',
                text: 'User has been deleted.',
                confirmButtonClass: 'btn btn-success',
              }).then(function() {

                window.location.href = result.redirect;
              });
            } else {
              Swal.fire({
                title: "Error!",
                text: "Error while deleting",
                type: "error",
                confirmButtonClass: 'CdsTYButton-btn-primary',
                buttonsStyling: false,
              });
            }
          },
        });
      }
    })
  }

</script>
@endpush
