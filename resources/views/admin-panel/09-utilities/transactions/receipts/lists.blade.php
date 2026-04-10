@extends('admin-panel.layouts.app')

@section('page-submenu')
{!! pageSubMenu('transactions') !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/common-table-redesign.css') }}">
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
<div class="CDSDashboardContainer-main-content">
<div class="CDSDashboardContainer-main-content-inner">   
<div class="CDSDashboardContainer-main-content-inner-header">
			@include("admin-panel.09-utilities.transactions.receipts.components.header-receipt") 

				</div>
<div class="CDSDashboardContainer-main-content-inner-body">
  <div class="cdsTYDashboard-contribution-history-table-container">
              <div class="cds-ty-dashboard-box-body">
                <!-- div table -->
                <div class="cdsTYDashboard-table">
                    <div class="cdsTYDashboard-table-wrapper">
                        <div class="cdsTYDashboard-table-header">
                            <div class="cdsTYDashboard-table-cell sorted-asc" data-column="sorted-asc" data-column="name" data-order="asc" onclick="sortTable(this)"" data-order="asc" onclick="sortTable(this)">Name / Email <span class="sort-arrow"></span></div>
                            <div class="cdsTYDashboard-table-cell sorted-asc" data-column="total_amount" data-order="asc" onclick="sortTable(this)">Amount <span class="sort-arrow"></span></div>
                            <div class="cdsTYDashboard-table-cell sorted-asc" data-column="tax" data-order="asc" onclick="sortTable(this)">Tax <span class="sort-arrow"></span></div>
                            <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Created Date <span class="sort-arrow"></span></div>
                            <div class="cdsTYDashboard-table-cell sorted-asc" data-column="paid_date" data-order="asc" onclick="sortTable(this)">Paid Date <span class="sort-arrow"></span></div>
                            <div class="cdsTYDashboard-table-cell sorted-asc" data-column="payment_status" data-order="asc" onclick="sortTable(this)">Status <span class="sort-arrow"></span></div>
                            <div class="cdsTYDashboard-table-cell sorted-asc" data-column="invoice_type" data-order="asc" onclick="sortTable(this)">Type <span class="sort-arrow"></span></div> <div class="cdsTYDashboard-table-cell cdsTYDashboard-table-cell-action" data-column="action">
                           
                           Action</div>
                        </div>
                        <div class="cdsTYDashboard-table-body" id="tableList">
						<div id="common-skeleton-loader" style="display:none;">
							@include('components.loaders.global-invoice-loader')              
						</div>
                        </div>  
						
					@include('components.table-pagination01') 			
                    </div>
					  <!-- Pagination -->
                
     
					
                </div>
                <!-- # div table -->
            </div>

          
              
		  
		  
		  </div>
  

</div>
</div>
</div>
</div>


<!-- End Content -->
@endsection

@section('javascript')
<script type="text/javascript">
const cookiePrefix = 'invoices_'; 
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
    $("#datatableSearch").keyup(function() {
      var value = $(this).val();
      if (value == '') {
        loadData();
      }
      if (value.length > 3) {
        loadData();
      }
    });
    $("#datatableCheckAll").change(function() {
      if ($(this).is(":checked")) {
        $(".row-checkbox").prop("checked", true);
      } else {
        $(".row-checkbox").prop("checked", false);
      }
      if ($(".row-checkbox:checked").length > 0) {
        $("#datatableCounterInfo").show();
      } else {
        $("#datatableCounterInfo").hide();
      }
      $("#datatableCounter").html($(".row-checkbox:checked").length);
    });

  })
  loadData();
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
      url: BASEURL + '/transactions/receipts/ajax-list?page=' + page,
      data: {
        _token: csrf_token,
        search: search,
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
          // Update dynamic count in header
          $("#resultsCountDisplay").text(`Showing ${data.total_records} receipts`);
        } else {
          $(".cdsTYDashboard-table").find(".norecord").remove();
          var html = '<div class="cdsTYDashboard-empty-list norecord">No records available</div>';
          $(".cdsTYDashboard-table-body").append(html);
          $("#pageinfo").html("1 of 1 <small class='text-danger'>(0 records)</small>");
          $("#pageno").val(1).attr("max", 1);
          $(".next").addClass("disabled", "disabled");
          $(".previous").addClass("disabled", "disabled");
          // Update dynamic count in header for zero state
          $("#resultsCountDisplay").text("Showing 0 services");
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
          url: BASEURL + '/transactions/receipts/delete-user',
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
@endsection