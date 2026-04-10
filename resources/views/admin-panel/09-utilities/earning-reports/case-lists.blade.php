

<div class="container">
    <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-header">
             @include("admin-panel.09-utilities.earning-reports.components.case-header-search")
            <div class="cds-action-elements">
                Earnings: ${{totalProfessionalEarning('case',auth()->user()->id)}}
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
                        <div class="cdsTYDashboard-table-cell">Invoice No </div>
                        <div class="cdsTYDashboard-table-cell" >Client Name </div>
                        <div class="cdsTYDashboard-table-cell">Case Title <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell">Associate Case <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell sorted-asc" data-column="user_earn_amount" data-order="asc" onclick="sortTable(this)">Earning Amount<span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell">Paid Date</div>
                        <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Created Date <span class="sort-arrow"></span></div>
                        
                    </div>
                    <div class="cdsTYDashboard-table-body" id="tableList">
					 <div id="common-skeleton-loader" style="display:none;">
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
const cookiePrefix = 'case_earnings_'; 
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


        $("#search-form").submit(function(e) {
            e.preventDefault();
            loadData();
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
        $("#search-input").keyup(function() {
            var value = $(this).val();
            if (value == '') {
                loadData();
            }
            if (value.length > 3) {
                loadData();
            }
        });

        $("#btnNavbarSearch").click(function() {
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
            url: BASEURL + '/earning-report/earning-report-ajax-list?page=' + page,
            data: {
                _token: csrf_token,
                search: search,
                status: "{{$status}}",
                        sort_direction:sortDirection,
		sort_column:sortColumn,
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
                } else {
                    $(".cdsTYDashboard-table").find(".norecord").remove();
                    var html = '<div class="cdsTYDashboard-empty-list norecord">No records available</div>';
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

@endpush