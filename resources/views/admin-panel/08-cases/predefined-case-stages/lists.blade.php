@extends('admin-panel.layouts.app')
@if(checkPrivilege([
                        'route_prefix' => 'panel.predefined-case-stages',
                        'module' => 'professional-predefined-case-stages',
                        'action' => 'view'
                    ]))
                    @php
                    $canViewPredefinedCaseStages=true;
                    @endphp
@else
                    @php
                    $canViewPredefinedCaseStages=false;
                    @endphp
@endif
@if(checkPrivilege([
                        'route_prefix' => 'panel.predefined-case-stages',
                        'module' => 'professional-predefined-case-stages',
                        'action' => 'add'
                    ]))
                    @php
                    $canAddPredefinedCaseStages=true;
                    @endphp
@else
                    @php
                    $canAddPredefinedCaseStages=false;
                    @endphp
@endif
@php 
$page_arr = [
    'page_title' => 'Predefined Case Stages ',
    'page_description' => 'Manage predefined case stages.',
    'page_type' => 'predefined-case-stages',
    'canViewPredefinedCaseStages' => $canViewPredefinedCaseStages,
    'canAddPredefinedCaseStages' => $canAddPredefinedCaseStages,
    'predefinedCaseStagesFeatureStatus' => $predefinedCaseStagesFeatureStatus ?? null,
];
@endphp
@section('page-submenu')
{!! pageSubMenu('cases',$page_arr) !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/common-table-redesign.css') }}">
@endsection
@section('content')
              
				<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
                @if(isset($predefinedCaseStagesFeatureStatus))
                    @if(!$canViewPredefinedCaseStages)
                        <div class="alert alert-danger mb-3">
                            <strong>⚠ Predefined Case Stages Management</strong><br>
                            {{ $predefinedCaseStagesFeatureStatus['message']  }}
                           
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                                <strong>⚠ Predefined Case Stages Management</strong><br>
                           
                            {{ $predefinedCaseStagesFeatureStatus['message'] }}
                        </div>
                    @endif
                @endif
                @include("admin-panel.08-cases.predefined-case-stages.components.header-search") 

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                @if($canViewPredefinedCaseStages)
                <!-- div table -->
                        <div class="cdsTYDashboard-table">
                            <div class="cdsTYDashboard-table-wrapper">
                                <div class="cdsTYDashboard-table-header">
                                   
                                    <div class="cdsTYDashboard-table-cell sorted-asc" data-column="name" data-order="asc" onclick="sortTable(this)">Name <span class="sort-arrow"></span></div>
                                    <div class="cdsTYDashboard-table-cell sorted-asc" data-column="added_by" data-order="asc" onclick="sortTable(this)">Added by <span class="sort-arrow"></span></div>
                                       <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Created At <span class="sort-arrow"></span></div>
                                    <div class="cdsTYDashboard-table-cell">Action</div>
                                </div>
                                <div class="cdsTYDashboard-table-body" id="tableList">
								<div id="common-skeleton-loader" style="display:none;">
                                    @include('components.loaders.common-loader')              
                                </div>
                                </div>  
								
								@include('components.table-pagination01') 							
                            </div>
                        </div>
						
                        <!-- # div table -->
                @else
                    <div class="text-center text-muted">
                        <p>You don't have permission to view predefined case stages.</p>
                    </div>
                @endif
			</div>
	
	</div>
  </div>
</div>

<!-- End Content -->
@endsection

@section('javascript')
<script type="text/javascript">

const cookiePrefix = 'predefined_case_stages_'; 
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
    var search = $("#searchInput").val();
    $.ajax({
      type: "POST",
      url: BASEURL + '/predefined-case-stages/ajax-list?page=' + page,
      data: {
        _token: csrf_token,
        search: search,
                   sort_direction:sortDirection,
		sort_column:sortColumn
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