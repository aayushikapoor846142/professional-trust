@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')

@section('case-container')

<div class="container">
  <div class="cds-fs-case-details-overview-panel">
    <div class="cds-fs-case-details-overview-panel-main">
        <div class="cds-fs-case-details-overview-panel-header"></div>
        <div class="cds-fs-case-details-overview-panel-body">
            <div class="row align-items-center mb-2">
                <div class="col-sm mb-2 mb-sm-0">
                    <h2 class="h4 mb-0">{{$pageTitle}}</h2>
                </div>

                <div class="col-sm-auto">
                    <a href="{{ baseUrl('case-with-professionals/invoices/add/' . $case_id)}}"  class="CdsTYButton-btn-primary">Add Invoice</a>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="CDSSupportPayment-main-content">
        <!-- Quick Actions -->
        <div class="CDSSupportPayment-quick-actions">
            <div class="CDSSupportPayment-search-container">
                <i class="fa-solid fa-search CDSSupportPayment-search-icon"></i>
                <input type="text" id="datatableSearch" class="CDSSupportPayment-search-input" placeholder="Search by name, email, or Invoice ID..." />
            </div>
        </div>

        <!-- Payment List -->
        <div class="CDSSupportPayment-payment-list">
            <div class="CDSSupportPayment-list-header">
                <h3 class="CDSSupportPayment-list-title">Case Invoices</h3>
            </div>

            <!-- div table -->
            <div class="cdsTYDashboard-table">
                <div class="cdsTYDashboard-table-wrapper">
                    <div class="cdsTYDashboard-table-header">
                        <div class="cdsTYDashboard-table-cell" data-column="first_name" data-order="asc" onclick="sortTable(this)">Customer <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell" data-column="invoice_number" data-order="asc" onclick="sortTable(this)">Invoice Number <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell" data-column="payment_gateway" data-order="asc">Payment Gateway</div>
                        <div class="cdsTYDashboard-table-cell" data-column="payment_gateway" data-order="asc">Copy Link</div>
                        <div class="cdsTYDashboard-table-cell" data-column="amount_paid" data-order="asc">Amount Paid</div>
                        <div class="cdsTYDashboard-table-cell" data-column="paid_status" data-order="asc">Payment Status</div>
                        <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Created At <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell">Actions</div>
                    </div>
                    <div class="cdsTYDashboard-table-body" id="tableList">
                        <div id="common-skeleton-loader" style="display:none;">
                            @include('components.loaders.global-invoice-loader')              
                        </div>
                    </div>  
                </div>
            </div>
            <!-- # div table -->

            {{--<!-- Table Header -->
            <div class="CDSSupportPayment-payment-item" style="background: #f8f9fb; font-weight: 600;">
                <div class="CDSSupportPayment-payment-content">
                    <div class="CDSSupportPayment-customer-details" data-column="first_name" data-order="asc" onclick="sortTable(this)" style="cursor: pointer;">Customer <span class="sort-arrow"></span></div>
                    <div data-column="invoice_number" data-order="asc" onclick="sortTable(this)" style="cursor: pointer;">Invoice Number <span class="sort-arrow"></span></div>
                    <div data-column="payment_gateway" data-order="asc" style="cursor: pointer;">Payment Gateway </div>
                    <div data-column="payment_gateway" data-order="asc" style="cursor: pointer;">Copy Link </div>
                    <div data-column="amount_paid" data-order="asc" style="cursor: pointer;">Amount Paid</div>
                    <div data-column="paid_status" data-order="asc" style="cursor: pointer;">Payment Status </div>
                    <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Created At <span class="sort-arrow"></span></div>
                    <div>Actions</div>
                </div>
            </div>
            <!-- Table Body -->
            <div id="tableList">
                <!-- Ajax content will load here -->
            </div>
            <div id="common-skeleton-loader" style="display:none;">
                @include('components.loaders.global-invoice-loader')              
            </div>--}}

            <!-- Pagination -->
            <div class="CDSSupportPayment-pagination-section">
                <div class="CDSSupportPayment-page-info" id="pageinfo">
                    <!-- Page info will be updated by Ajax -->
                </div>

                <div class="CDSSupportPayment-page-controls">
                    <button class="CDSSupportPayment-page-button previous" onclick="changePage('prev')">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <input type="number" id="pageno" class="CDSSupportPayment-page-button" style="width: 60px;" onblur="changePage('goto')" min="1" />

                    <button class="CDSSupportPayment-page-button next" onclick="changePage('next')">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
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
    var search = $("#datatableSearch").val();
    $.ajax({
      type: "POST",
      url: BASEURL + '/case-with-professionals/invoices/ajax-list?page=' + page,
      data: {
        _token: csrf_token,
        search: search,
                      sort_direction:sortDirection,
		sort_column:sortColumn,
    case_id:"{{$case_id}}"
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
        } else {
            $(".cdsTYDashboard-table").find(".norecord").remove();
            var html = '<div class="text-center text-danger norecord">No records available</div>';
            $(".cdsTYDashboard-table").append(html);
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

 
</script>


@endsection