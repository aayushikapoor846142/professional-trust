@extends('admin-panel.layouts.app')
@section('styles')
<link href="{{ url('assets/css/29-CDS-retainer-aggrement.css') }}" rel="stylesheet" />
@endsection
@section('page-submenu')
{!! pageSubMenu('cases') !!}
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
    <!-- Stats Cards -->
    <div class="CdsCaseRetainer-stats-container">
        <div class="CdsCaseRetainer-stat-card">
            <div class="CdsCaseRetainer-stat-content">
                <div class="CdsCaseRetainer-stat-icon CdsCaseRetainer-total">📋</div>
                <div class="CdsCaseRetainer-stat-info">
                    <div class="CdsCaseRetainer-stat-value">{{$records->count()}}</div>
                    <div class="CdsCaseRetainer-stat-label">Total Agreements</div>
                </div>
            </div>
        </div>
        <div class="CdsCaseRetainer-stat-card">
            <div class="CdsCaseRetainer-stat-content">
                <div class="CdsCaseRetainer-stat-icon CdsCaseRetainer-accepted">✓</div>
                <div class="CdsCaseRetainer-stat-info">
                    <div class="CdsCaseRetainer-stat-value">{{collect($records)->where('status','accepted')->count()}}</div>
                    <div class="CdsCaseRetainer-stat-label">Accepted</div>
                </div>
            </div>
        </div>
        <div class="CdsCaseRetainer-stat-card">
            <div class="CdsCaseRetainer-stat-content">
                <div class="CdsCaseRetainer-stat-icon CdsCaseRetainer-pending">⏳</div>
                <div class="CdsCaseRetainer-stat-info">
                    <div class="CdsCaseRetainer-stat-value">{{collect($records)->where('status','pending')->count()}}</div>
                    <div class="CdsCaseRetainer-stat-label">Pending</div>
                </div>
            </div>
        </div>
        <div class="CdsCaseRetainer-stat-card">
            <div class="CdsCaseRetainer-stat-content">
                <div class="CdsCaseRetainer-stat-icon CdsCaseRetainer-draft">📝</div>
                <div class="CdsCaseRetainer-stat-info">
                    <div class="CdsCaseRetainer-stat-value">{{collect($records)->where('status','draft')->count()}}</div>
                    <div class="CdsCaseRetainer-stat-label">Draft</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="CdsCaseRetainer-filter-section">
        <h2 class="CdsCaseRetainer-filter-title">Filter Agreements</h2>
        <div class="CdsCaseRetainer-filter-buttons">
            <a class="CdsCaseRetainer-filter-btn {{$status == 'all' ? 'CdsCaseRetainer-active' : ''}}" href="{{baseUrl('case-with-professionals/retainers/?status=all')}}">All</a>
            <a class="CdsCaseRetainer-filter-btn {{$status == 'accepted' ? 'CdsCaseRetainer-active' : ''}}" href="{{baseUrl('case-with-professionals/retainers/?status=accepted')}}">Accepted</a>
            <a class="CdsCaseRetainer-filter-btn {{$status == 'pending' ? 'CdsCaseRetainer-active' : ''}}" href="{{baseUrl('case-with-professionals/retainers/?status=pending')}}">Pending</a>
            <a class="CdsCaseRetainer-filter-btn {{$status == 'draft' ? 'CdsCaseRetainer-active' : ''}}" href="{{baseUrl('case-with-professionals/retainers/?status=draft')}}">Draft</a>
        </div>
    </div>


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
<!-- div table -->
    <div class="cdsTYDashboard-table">
        <div class="cdsTYDashboard-table-wrapper">
            <div class="cdsTYDashboard-table-header">
                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="title" data-order="asc" onclick="sortTable(this)">
                    Title
                <span class="sort-arrow"></span>
                </div>
                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="professional_case_id" data-order="asc" onclick="sortTable(this)">Case <span class="sort-arrow"></span></div>
                <div class="cdsTYDashboard-table-cell">Client</div>
                <div class="cdsTYDashboard-table-cell">Service</div>
                <div class="cdsTYDashboard-table-cell">Date Sent</div>
                <div class="cdsTYDashboard-table-cell">Accepted</div>
                <div class="cdsTYDashboard-table-cell">Status </div>
                <div class="cdsTYDashboard-table-cell">Added by</div>
                                   <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Created At <span class="sort-arrow"></span></div>
                <div class="cdsTYDashboard-table-cell">Action</div>
            </div>
            <div class="cdsTYDashboard-table-body" id="CdsCaseRetainer-gridBody">
			<div id="common-skeleton-loader" style="display:none;">
                @include('components.loaders.retainers-loader')              
            </div>
			
			</div> 
            
        </div>
    </div>
    <!-- # div table -->
			</div>
	
	</div>
  </div>
</div>


@endsection

@section('javascript')
<script type="text/javascript">
const cookiePrefix = 'retain_agreements_'; 
let sortColumn = getCookie(cookiePrefix + 'sortColumn') || 'created_at';
let sortDirection = getCookie(cookiePrefix + 'sortDirection') || 'desc';

    	 let page = 1;
        let last_page = 1;
        let loading = false;
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


    $("#search-input").keyup(function() {
      var value = $(this).val();
      if (value == '') {
        loadData();
      }
      if (value.length > 3) {
        loadData();
      }
    });

    $("#search-form").submit(function(e) {
      e.preventDefault();
      loadData();
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


  function loadData(page = 1,search = "") {
    var search = $("#search-input").val();
    $.ajax({
        type: "POST",
        url: BASEURL + '/case-with-professionals/retainers-ajax-list?page=' + page,
        data: {
            _token: csrf_token,
            search: search,
            status:"{{$status}}",
                     sort_direction:sortDirection,
		sort_column:sortColumn
        },
        dataType: 'json',
        beforeSend: function() {
          $(".professional-view-more-link").remove(); // Remove the button immediately
          $("#common-skeleton-loader").show();
        },
        success: function(data) {
            
            $(".norecord").remove();  
           $("#common-skeleton-loader").hide();
            dataloading = false;
            // $(".professional-view-more-link").remove(); // This line is removed as per the edit hint

            last_page = data.last_page;

            if (data.contents.trim() === "") {
                loading = true;
                if (data.current_page === 1) {
                    $("#CdsCaseRetainer-gridBody").html(
                        '<div class="text-center text-danger mt-5">No professional found.</div>'
                        );
                }
            } else {
                if (data.current_page === 1) {
                    console.log(data.current_page);
                    $("#CdsCaseRetainer-gridBody").append(data.contents);
                } else {
                    $("#CdsCaseRetainer-gridBody").append(data.contents);
                }

            }
                // $("#CdsCaseRetainer-gridBody").append(data.contents);
            
        },
        complete: function () {
            dataloading = false; 
        }
    });
  }

  $(window).scroll(function () {
    const container = $("#CdsCaseRetainer-gridBody");

    if (container.length === 0) return;

    const containerOffsetTop = container.offset().top;
    const containerHeight = container.outerHeight();
    const windowBottom = $(window).scrollTop() + $(window).height();

    if (windowBottom >= containerOffsetTop + containerHeight - 100) {
        if (page < last_page && !dataloading && page < 3) {
            dataloading = true; // ✅ immediately set it before load starts
            page++;
            loadData(page, 'scrollload');
        }
    }
});

  
</script>

@endsection