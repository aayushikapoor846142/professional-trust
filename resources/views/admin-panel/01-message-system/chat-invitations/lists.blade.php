@extends('admin-panel.layouts.app')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/common-table-redesign.css') }}">
@endsection

@section('content')
<!-- Content -->
@php
$active_status = 0; 
if(request()->get('status') == 'accepted'){
  $active_status = 1;
}else if(request()->get('status') == 'pending'){
  $active_status = 0;
}
 @endphp
 <div class="ch-action">
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.chat-invitations',
                        'module' => 'professional-chat-invitations',
                        'action' => 'add'
                    ]))
                    <a href="javascript:;" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('connections/invitations/add') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-paper-plane fa-regular me-1"></i>
                        Send Invite
                    </a>
                    @endif
                </div>
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <ul class="status-tabs">
                        <li class="{{ $active_status == '0'?'cds-active':'' }}">
                            <a class="tab-link" href="{{ baseUrl('/connections/invitations?status=pending') }}">Pending</a>
                        </li>
                        <li class="{{ $active_status == '1'?'cds-active':'' }}">
                            <a class="tab-link" href="{{ baseUrl('/connections/invitations?status=accepted') }}">Accepted</a>
                        </li>
                    </ul>
 @if(checkPrivilege([
                        'route_prefix' => 'panel.chat-invitations',
                        'module' => 'professional-chat-invitations',
                        'action' => 'delete'
                    ]))
                    <div class="cds-action-elements">
                        <span class="font-size-sm mr-3">
                        <span id="datatableCounter">0</span>
                        Selected
                        </span>
                        <a class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-multi-delete ml-2" data-href="{{ baseUrl('connections/invitations/delete-multiple') }}"
                            onclick="deleteMultiple(this)" href="javascript:;">
                        Delete
                        </a>
                    </div>
                    @endif
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
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
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="email" data-order="asc" onclick="sortTable(this)">Email <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" data-column="status" data-order="asc" onclick="sortTable(this)">Status <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" >Added By <span class="sort-arrow"></span></div>
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

			</div>
	
	</div>
  </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    $(".row-checkbox").change(function() {
        if ($(".row-checkbox:checked").length > 0) {
            $("#datatableCounterInfo").show();
        } else {
            $("#datatableCounterInfo").show();
        }
        $("#datatableCounter").html($(".row-checkbox:checked").length);
    });
})
</script>
<script type="text/javascript">

const cookiePrefix = 'chat_invitations_'; 
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
      url: BASEURL + '/connections/invitations/ajax-list?page=' + page,
      data: {
        _token: csrf_token,
        search: search,
        status:"{{ $active_status }}",
                      sort_direction:sortDirection,
		sort_column:sortColumn
      },
      dataType: 'json',
      beforeSend: function() {
        var cols = $("#tableList").length;
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