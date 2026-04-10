@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('earnings') !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/common-table-redesign.css') }}">
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
@include("admin-panel.09-utilities.transactions.points-earned.components.header-search") 

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
       <!-- div table -->
            <div class="cdsTYDashboard-table">
                <div class="cdsTYDashboard-table-wrapper">
                    <div class="cdsTYDashboard-table-header">
                        <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Date <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell sorted-asc" data-column="points" data-order="asc" onclick="sortTable(this)">Actual Points <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell sorted-asc" data-column="bonus_points" data-order="asc" onclick="sortTable(this)">Bonus Points <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell sorted-asc" data-column="total_points" data-order="asc" onclick="sortTable(this)">Total Points <span class="sort-arrow"></span></div>
                    </div>
                    <div class="cds-point-earn-row-outer cdsTYDashboard-table-body">
                    </div>  
                    <div id="common-skeleton-loader" style="display:none;">
                        @include('components.loaders.common-loader')              
                    </div>
                </div>
            </div> 
			@include('components.table-pagination01') 
            <!-- # div table -->
			</div>
	
	</div>
  </div>
</div>
   

<!-- End Content -->
@endsection

@section('javascript')
<script type="text/javascript">
const cookiePrefix = 'point_earned_'; 
let sortColumn = getCookie(cookiePrefix + 'sortColumn') || 'created_at';
let sortDirection = getCookie(cookiePrefix + 'sortDirection') || 'desc';


    $(document).ready(function () {

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

        $(".next").click(function () {
            if (!$(this).hasClass('disabled')) {
                changePage('next');
            }
        });
        $(".previous").click(function () {
            if (!$(this).hasClass('disabled')) {
                changePage('prev');
            }
        });
        $("#datatableSearch").keyup(function () {
            var value = $(this).val();
            if (value == '') {
                loadData();
            }
            if (value.length > 3) {
                loadData();
            }
        });
        $("#datatableCheckAll").change(function () {
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
            url: BASEURL + '/earnings/points-earn-history/ajax-list?page=' + page,
            data: {
                _token: csrf_token,
                search: search,
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
            beforeSend: function () {
                $("#common-skeleton-loader").show();
            },
            success: function (data) {
                $(".cds-point-earn-row-outer").html(data.contents);
                $("#common-skeleton-loader").hide();
                if (data.total_records > 0) {
                    var pageinfo = data.current_page + " of " + data.last_page +
                        " <small class='text-danger'>(" + data.total_records + " records)</small>";
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
                    $(".datatable-custom").find(".norecord").remove();
                    var html = '<div class="cdsTYDashboard-empty-list norecord">No records available</div>';
                    $(".datatable-custom").append(html);
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
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: BASEURL + '/earnings/points-earn-history/delete-user',
                    data: {
                        _token: csrf_token,
                        user_id: id,
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result.status == true) {
                            Swal.fire({
                                type: "success",
                                title: 'Deleted!',
                                text: 'User has been deleted.',
                                confirmButtonClass: 'btn btn-success',
                            }).then(function () {

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

    //   let progress = document.querySelector(".progress-bar");
    //   let value = 70; // Change this dynamically

    //   if (value < 30) {
    //     progress.style.background = "red";
    //   } else if (value < 70) {
    //     progress.style.background = "orange";
    //   } else {
    //     progress.style.background = "green";
    //   }

</script>
@endsection
