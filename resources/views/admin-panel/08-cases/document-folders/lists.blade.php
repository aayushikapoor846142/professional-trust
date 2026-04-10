@extends('admin-panel.layouts.app')
@if(checkPrivilege([
                        'route_prefix' => 'panel.document-folders',
                        'module' => 'professional-document-folders',
                        'action' => 'view'
                    ]))
                    @php
                    $canViewDocumentFolders=true;
                    @endphp
@else
                    @php
                    $canViewDocumentFolders=false;
                    @endphp
@endif
@if(checkPrivilege([
                        'route_prefix' => 'panel.document-folders',
                        'module' => 'professional-document-folders',
                        'action' => 'add'
                    ]))
                    @php
                    $canAddDocumentFolders=true;
                    @endphp
@else
                    @php
                    $canAddDocumentFolders=false;
                    @endphp
@endif
@if(checkPrivilege([
                        'route_prefix' => 'panel.document-folders',
                        'module' => 'professional-document-folders',
                        'action' => 'delete'
                    ]))
                    @php
                    $canDeleteDocumentFolders=true;
                    @endphp
@else
                    @php
                    $canDeleteDocumentFolders=false;
                    @endphp
@endif
@php 
$page_arr = [
    'page_title' => 'Document Folders ',
    'page_description' => 'Manage document folders.',
    'page_type' => 'document-folders',
    'canViewDocumentFolders' => $canViewDocumentFolders,
    'canAddDocumentFolders' => $canAddDocumentFolders,
    'canDeleteDocumentFolders' => $canDeleteDocumentFolders,
    'documentFoldersFeatureStatus' => $documentFoldersFeatureStatus ?? null,
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
                @if(isset($documentFoldersFeatureStatus))
                    @if(!$canViewDocumentFolders)
                        <div class="alert alert-danger mb-3">
                            <strong>⚠ Document Folders Management</strong><br>
                            {{ $documentFoldersFeatureStatus['message']  }}
                           
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                                <strong>⚠ Document Folders Management</strong><br>
                           
                            {{ $documentFoldersFeatureStatus['message'] }}
                        </div>
                    @endif
                @endif
                @include("admin-panel.08-cases.document-folders.components.header-search") 
        <div class="cds-ty-dashboard-box-header">
            @if($canDeleteDocumentFolders)
            <div class="cds-action-elements">
                <span class="font-size-sm mr-3">
                    <span id="datatableCounter">0</span>
                    Selected
                </span>
                <a class="btn-multi-delete CdsTYButton-btn-primary CdsTYButton-border-thick ml-2" data-href="{{ baseUrl('document-folders/delete-multiple') }}" onclick="deleteMultiple(this)" href="javascript:;">
                    <i class="fa-solid fa-trash-arrow-up"></i>
                    Delete
                </a>
            </div>
            @endif
           
              </div>
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                @if($canViewDocumentFolders)
                <!-- div table -->
                <div class="cdsTYDashboard-table">
                    <div class="cdsTYDashboard-table-wrapper">
                        <div class="cdsTYDashboard-table-header">
                            <div class="cdsTYDashboard-table-cell cdsCheckbox">
                                <div class="custom-control custom-checkbox">
                                    <input id="datatableCheckAll" type="checkbox" class="custom-control-input" />
                                    <label class="custom-control-label" for="datatableCheckAll"></label>
                                </div>
                            </div>
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
				
						</div>
                @else
                    <div class="text-center text-muted">
                        <p>You don't have permission to view document folders.</p>
                    </div>
                @endif
	
	</div>
  </div>
</div>				

@endsection

@section('javascript')
<script type="text/javascript">

const cookiePrefix = 'document_folders_'; 
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


  function loadData(page = 1) {
    var search = $("#searchInput").val();
    $.ajax({
      type: "POST",
      url: BASEURL + '/document-folders/ajax-list?page=' + page,
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
          url: BASEURL + '/country/delete-user',
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