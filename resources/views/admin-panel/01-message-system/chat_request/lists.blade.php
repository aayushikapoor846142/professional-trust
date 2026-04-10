@extends('admin-panel.layouts.app')

@section('content')
 @if(checkPrivilege('company-locations', 'add'))
                <div class="ch-action">
                    <a href="{{ baseUrl('company-locations/add') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-plus fa-solid"></i>
                        Add New
                    </a>
                </div>
                @endif
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <div class="cds-ty-dashboard-box-header" style="display: none;">
                    <div class="cds-action-elements">
                        @if(checkPrivilege('company-locations','delete'))
                        <span class="font-size-sm mr-3">
                            <span id="datatableCounter">0</span>
                            Selected
                        </span>
                        <a class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-multi-delete ml-2"
                            data-href="{{ baseUrl('company-locations/delete-multiple') }}" onclick="deleteMultiple(this)"
                            href="javascript:;">
                            Delete
                        </a>
                        @endif
                    </div>
                </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="cdsTYDashboard-table">
                        <div class="cdsTYDashboard-table-wrapper">
                            <div class="cdsTYDashboard-table-header">
                                <div class="cdsTYDashboard-table-cell cdsCheckbox" onclick="sortTable(0)">
                                    <div class="custom-control custom-checkbox">
                                        <input id="datatableCheckAll" type="checkbox" class="custom-control-input">
                                        <label class="custom-control-label" for="datatableCheckAll"></label>
                                    </div>
                                </div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" onclick="sortTable(1)">Receiver Name <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" onclick="sortTable(2)">Status <span class="sort-arrow"></span></div>
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
	
	</div>
  </div>
</div>				

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {

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

function loadData(page = 1) {
    var search = $("#datatableSearch").val();
    $.ajax({
        type: "POST",
        url: BASEURL + '/chat-request/ajax-list?page=' + page,
        data: {
            _token: csrf_token,
            search: search
        },
        dataType: 'json',
        beforeSend: function() {
            var cols = $("#tableList thead tr > th").length;
        $("#tableList").html("<div class='text-center py-2'><i class='fa fa-spin fa-spinner fa-3x'></i></div>");
        // $("#paginate").html('');
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
<script>
    function sortTable(columnIndex) {
    const rows = Array.from(document.querySelectorAll('#tableList .cdsTYDashboard-table-row'));
    let isAscending = document.querySelectorAll('.cdsTYDashboard-table-header .cdsTYDashboard-table-cell')[columnIndex].classList.contains('sorted-asc');
    
    // Reset all header sorting classes
    document.querySelectorAll('.cdsTYDashboard-table-header .cdsTYDashboard-table-cell').forEach(cell => {
        cell.classList.remove('sorted-asc', 'sorted-desc');
    });
    // Toggle sort direction
    if (isAscending) {
        document.querySelectorAll('.cdsTYDashboard-table-header .cdsTYDashboard-table-cell')[columnIndex].classList.add('sorted-desc');
    } else {
        document.querySelectorAll('.cdsTYDashboard-table-header .cdsTYDashboard-table-cell')[columnIndex].classList.add('sorted-asc');
    }
    rows.sort((rowA, rowB) => {
        const cellA = rowA.querySelectorAll('.cdsTYDashboard-table-cell')[columnIndex].innerText.trim();
        const cellB = rowB.querySelectorAll('.cdsTYDashboard-table-cell')[columnIndex].innerText.trim();

        if (isAscending) {
            return cellA < cellB ? -1 : cellA > cellB ? 1 : 0;
        } else {
            return cellA > cellB ? -1 : cellA < cellB ? 1 : 0;
        }
    });
    // Reorder the rows in the table
    rows.forEach(row => document.getElementById('tableList').appendChild(row));
}
</script>
@endsection