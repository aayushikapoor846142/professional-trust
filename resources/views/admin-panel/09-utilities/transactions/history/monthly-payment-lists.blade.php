 <div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
<div class="CDSDashboardContainer-main-content">
<div class="CDSDashboardContainer-main-content-inner">   
<div class="CDSDashboardContainer-main-content-inner-header">
			@include("admin-panel.09-utilities.transactions.history.component.header-monthly-payment") 	
				</div>
<div class="CDSDashboardContainer-main-content-inner-body">
 <div class="cdsTYDashboard-contribution-history-table-container">
            <div class="cds-ty-dashboard-box-body">
                <div class="cdsTYDashboard-table-wrapper">
                    <!-- div table -->
                    <div class="cdsTYDashboard-table">
                        <div class="cdsTYDashboard-table-wrapper">
                            <div class="cdsTYDashboard-table-header">
                               <div class="cdsTYDashboard-table-cell sorted-asc" data-column="user_id" data-order="asc" onclick="sortTableMonthly(this)">Name <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="amount" data-order="asc" onclick="sortTableMonthly(this)">Amount Paid <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="tax" data-order="asc" onclick="sortTableMonthly(this)">Tax Amount <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="total_amount" data-order="asc" onclick="sortTableMonthly(this)">Total Amount <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTableMonthly(this)">Created On <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="status" data-order="asc" onclick="sortTableMonthly(this)">Status <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell cdsTYDashboard-table-cell-action">Action</div>
                            </div>
                            <div class="cdsTYDashboard-table-body" id="monthlyTableList"><div id="common-skeleton-loader" style="display:none;">
								@include('components.loaders.transaction-history-loader')              
							</div>
                            </div>  
							 <div class="cdsTYDashboard-table-footer">
                    <div class="cdsTYDashboard-table-footer-count">
                        <span>Page:</span>
                        <span id="monthlypageinfo"></span>
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

<script src="{{url('assets/js/custom-datepicker.js')}}"></script>
<link href="{{ url('assets/css/custom-datepicker.css') }}" rel="stylesheet" />

<script type="text/javascript">
const MonthlycookiePrefix  = 'monthly_payment_'; 
let monthlysortColumn = getCookie(MonthlycookiePrefix + 'monthlysortColumn') || 'created_at';
let monthlysortDirection = getCookie(MonthlycookiePrefix + 'monthlysortDirection') || 'desc';


	var pass_to_dates = "";
	var pass_from_dates = "";
	var search = "";
	var sort_by_column = "all";
    var sort_order = "desc";
    var status = "all";

  $(document).ready(function() {
	
	
       // Find the matching header cell
    const $el = $(".cdsTYDashboard-table-cell[data-column='" + monthlysortColumn + "']");

    if ($el.length) {
        // Remove any existing arrow classes and text
        $('.sort-header').removeClass('sorted-asc sorted-desc')
                         .attr('data-order', 'asc')
                         .find('.sort-arrow').text('');
        // Apply current sort direction
  $el.attr('data-order', monthlysortDirection)
           .addClass(monthlysortDirection === 'asc' ? 'sorted-asc' : 'sorted-desc');
    }

	let from_date = "";
	let filter_from_date = "";
	let filter_to_date = "";
	let to_date = "";  // to track the actual selected toDate

function destroyCalendar(inputId) {
  const input = document.getElementById(inputId);
  if (!input) return;
  const nextSibling = input.nextElementSibling;
  if (nextSibling && nextSibling.id && nextSibling.id.startsWith("datepicker-wrapper-")) {
    nextSibling.remove();
  }
}

// Initialize fromDate calendar
CustomCalendarWidget.initialize("fromDate", {
  onDateSelect: function (selectedDate) {
    from_date = selectedDate;

    // If to_date exists and is before from_date, clear it
    if (to_date && to_date < from_date) {
      to_date = "";
      filter_to_date = "";
      // Also clear the toDate input value
      const toInput = document.getElementById("toDate");
      if (toInput) toInput.value = "";
    }

    // Destroy existing toDate calendar
    destroyCalendar("toDate");

    // Initialize toDate calendar with minDate = from_date
    CustomCalendarWidget.initialize("toDate", {
      minDate: from_date,
      onDateSelect: function (selectedToDate) {
        to_date = selectedToDate;
        filter_to_date = selectedToDate;
      }
    });
  }
});

// Initialize toDate calendar on page load with current from_date (which is "")
CustomCalendarWidget.initialize("toDate", {
  minDate: from_date || null,
  onDateSelect: function (selectedToDate) {
    to_date = selectedToDate;
    filter_to_date = selectedToDate;
  }
});


	
    loadMonthlyData(1);

	
    // monthly
    $(".monthly-next").click(function() {
      if (!$(this).hasClass('disabled')) {
        changeMonthlyPage('next');
      }
    });
    $(".monthly-previous").click(function() {
      if (!$(this).hasClass('disabled')) {
        changeMonthlyPage('prev');
      }
    });

  })


    function sortTableMonthly(element) {
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
    monthlysortColumn = columnName;
    monthlysortDirection = newOrder;
   setCookie(MonthlycookiePrefix + 'monthlysortColumn', columnName, 24);
setCookie(MonthlycookiePrefix + 'monthlysortDirection', newOrder, 24);
   
        loadMonthlyData();
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



    function setMonthlyStatusFilter(statuses)
    {
        $(".cds-status-filter").removeClass("cds-active");
        $(`.cds-status-filter[data-status="${statuses}"]`).addClass("cds-active");
        status = statuses;
        loadMonthlyData();
    }

	function searchMonthlyPayment(inputElement) {
		search = inputElement.value;
		loadMonthlyData();
	}

	function monthlyDateFilter(){
		pass_to_dates = $("#toDate").val();
		pass_from_dates = $("#fromDate").val();
		loadMonthlyData();
	}

	function clearMonthlyDateFilter()
	{
		pass_to_dates = "";
		pass_from_dates = "";
		$("#toDate").val("");
		$("#fromDate").val("");
		loadMonthlyData();
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

  function loadMonthlyData(page = 1) {
  
    $.ajax({
      type: "POST",
      url: BASEURL + '/transactions/history/monthly-ajax-list?page=' + page,
      data: {
        _token: csrf_token,
        search: search,
        status:status,
        sort_by_column:sort_by_column,
        sort_order:sort_order,
		pass_to_dates:pass_to_dates,
		pass_from_dates:pass_from_dates,
     sort_direction:monthlysortDirection,
		sort_column:monthlysortColumn
      },
      dataType: 'json',
      beforeSend: function() {
		$("#common-skeleton-loader").show();
      },
      success: function(data) {
        $("#monthlyTableList").html(data.contents);
		$("#common-skeleton-loader").hide();
        if (data.total_records > 0) {
          var pageinfo = data.current_page + " of " + data.last_page + " <small class='text-danger'>(" + data.total_records + " records)</small>";
          $("#monthlypageinfo").html(pageinfo);
          $("#monthlypageno").val(data.current_page);
          if (data.current_page < data.last_page) {
            $(".monthly-next").removeClass("disabled");
          } else {
            $(".monthly-next").addClass("disabled", "disabled");
          }
          if (data.current_page > 1) {
            $(".monthly-previous").removeClass("disabled");
          } else {
            $(".monthly-previous").addClass("disabled", "disabled");
          }
          $("#monthlypageno").attr("max", data.last_page);
        } else {
          $(".cdsTYDashboard-table").find(".norecord").remove();
          var html = '<div class="cdsTYDashboard-empty-list norecord">No records available</div>';
          $(".cdsTYDashboard-table-body").append(html);
        }
      },
    });
  }

  function changeMonthlyPage(action) {
    var page = parseInt($("#monthlypageno").val());
    if (action == 'prev') {
      page--;
    }
    if (action == 'next') {
      page++;
    }
    if (!isNaN(page)) {
      loadMonthlyData(page);
    } else {
      errorMessage("Invalid Page Number");
    }

  }


    function toggleDropdown(id) {
      document.querySelectorAll('.cdsTYDashboard-custom-table01-dropdown-menu').forEach(menu => {
        menu.style.display = menu.id === id && menu.style.display !== 'block' ? 'block' : 'none';
      });
    }

  
</script>
@endpush
