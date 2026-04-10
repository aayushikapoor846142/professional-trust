
<div class="container">
    <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-header">
            <div class="search-area cds-form-container">
                <div class="row justify-content-end">
                    <div class="col-xxl-5 col-xl-6 col-md-12 col-lg-6">
                    <form id="search-form">
                        @csrf
                        <div class="input-group mb-3">
                            {!! FormHelper::formInputText([
                                'name' => 'search',
                                'label' => 'Search by Name',
                                'id' => 'search-input',
                            ]) !!}
                            <button class="btn btn-secondary"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <div class="cds-action-elements">
                Earnings: ${{totalProfessionalEarning('general_invoice',auth()->user()->id)}}
            </div>
        </div>

        <div class="cds-ty-dashboard-box-body">
            <!-- div table -->
            <div class="cdsTYDashboard-table">
                <div class="cdsTYDashboard-table-wrapper">
                    <div class="cdsTYDashboard-table-header">
                        <div class="cdsTYDashboard-table-cell cdsCheckbox" onclick="sortTable(0)">
                            <div class="custom-control custom-checkbox">
                                <input id="datatableCheckAll" type="checkbox" class="custom-control-input">
                                <label class="custom-control-label" for="datatableCheckAll"></label>
                            </div>
                        </div>
                        <div class="cdsTYDashboard-table-cell sorted-asc">Invoice No </div>
                        <div class="cdsTYDashboard-table-cell">Client Name </div>                   
                        <div class="cdsTYDashboard-table-cell sorted-asc" data-column="user_earn_amount" data-order="asc" onclick="sortTable(this)">Earning Amount <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell" >Paid Date <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Crated Date <span class="sort-arrow"></span></div>
                        
                    </div>
                    <div class="cdsTYDashboard-table-body" id="OtherTableList"> <div id="common-skeleton-loader" style="display:none;">
                        @include('components.loaders.earning-report-loader')              
                    </div>  
                    </div>
                   @include('components.table-pagination01') 							
                </div>
            </div>
            <!-- # div table -->
        </div>
        </div>
</div>

@push('scripts')
<script type="text/javascript">
const otherCookiePrefix = 'global_invoice_earnings_'; 
let otherSortColumn = getCookie(otherCookiePrefix + 'sortColumn') || 'created_at';
let otherSortDirection = getCookie(otherCookiePrefix + 'sortDirection') || 'desc';


    $(document).ready(function() {
         // Find the matching header cell
    const $el = $(".cdsTYDashboard-table-cell[data-column='" + otherSortColumn + "']");

    if ($el.length) {
        // Remove any existing arrow classes and text
        $('.sort-header').removeClass('sorted-asc sorted-desc')
                         .attr('data-order', 'asc')
                         .find('.sort-arrow').text('');
        // Apply current sort direction
  $el.attr('data-order', otherSortDirection)
           .addClass(otherSortDirection === 'asc' ? 'sorted-asc' : 'sorted-desc');
    }


        $("#search-form").submit(function(e) {
            e.preventDefault();
            otherLoadData();
        });

        $(".other-next").click(function() {
            if (!$(this).hasClass('disabled')) {
                changeOtherPage('next');
            }
        });
        $(".other-previous").click(function() {
            if (!$(this).hasClass('disabled')) {
                changeOtherPage('prev');
            }
        });
        $("#search-input").keyup(function() {
            var value = $(this).val();
            if (value == '') {
                otherLoadData();
            }
            if (value.length > 3) {
                otherLoadData();
            }
        });

        $("#btnNavbarSearch").click(function() {
            var value = $(this).val();
            if (value == '') {
                otherLoadData();
            }
            if (value.length > 3) {
                otherLoadData();
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
    otherLoadData();

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
    otherSortColumn = columnName;
    otherSortDirection = newOrder;
   setCookie(otherCookiePrefix + 'sortColumn', columnName, 24);
setCookie(otherCookiePrefix + 'sortDirection', newOrder, 24);
   

    otherLoadData();
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


    function otherLoadData(page = 1) {
        var search = $("#search-input").val();
        $.ajax({
            type: "POST",
            url: BASEURL + '/earning-global-invoice-report-ajax-list?page=' + page,
            data: {
                _token: csrf_token,
                search: search,
                status: "{{$status}}",
                        sort_direction:otherSortDirection,
		sort_column:otherSortColumn
            },
            dataType: 'json',
            beforeSend: function() {
                $("#common-skeleton-loader").show();
      },
      success: function(data) {
        $(".norecord").remove(); 
        $("#OtherTableList").html(data.contents);
         $("#common-skeleton-loader").hide();
        if (data.total_records > 0) {
          var pageOtherinfo = data.current_page + " of " + data.last_page + " <small class='text-danger'>(" + data.total_records + " records)</small>";
          $("#pageOtherinfo").html(pageOtherinfo);
          $("#pageOtherno").val(data.current_page);
          if (data.current_page < data.last_page) {
            $(".other-next").removeClass("disabled");
          } else {
            $(".other-next").addClass("disabled", "disabled");
          }
          if (data.current_page > 1) {
            $(".other-previous").removeClass("disabled");
          } else {
            $(".other-previous").addClass("disabled", "disabled");
          }
          $("#pageOtherno").attr("max", data.last_page);
        } else {
            $(".cdsTYDashboard-table").find(".norecord").remove();
            var html = '<div class="cdsTYDashboard-empty-list norecord">No records available</div>';
            $(".cdsTYDashboard-table").append(html);
        }
      },
    });
  }

    function changeOtherPage(action) {
        var page = parseInt($("#pageOtherno").val());
        if (action == 'prev') {
            page--;
        }
        if (action == 'next') {
            page++;
        }
        if (!isNaN(page)) {
            otherLoadData(page);
        } else {
            errorMessage("Invalid Page Number");
        }

    }

   
</script>

@endpush