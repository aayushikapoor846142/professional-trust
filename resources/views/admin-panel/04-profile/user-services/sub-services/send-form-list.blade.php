@extends('admin-panel.layouts.app')

@section('content')
<div class="ch-action">
                    <a   href="{{ baseUrl('my-services/send-assesment-form/'.$id) }}" class="CdsTYButton-btn-primary">
                    <i class="fa-plus fa-solid me-1"></i>  
                      Add New
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <form id="search-form">
                                @csrf
                                <div class="input-group mb-3">
                                    {!! FormHelper::formInputText([ 'name' => 'search',  'id' => 'search-input', 'label' => 'Search' ]) !!}
                                    <button class="btn btn-secondary"><i class="fa fa-search"></i></button>
                                </div>
                            </form>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                   <!-- div table -->
                        <div class="cdsTYDashboard-table">
                            <div class="cdsTYDashboard-table-wrapper">
                                <div class="cdsTYDashboard-table-header">
                                    <div class="cdsTYDashboard-table-cell sorted-asc" onclick="sortTable(1)">Form Name <span class="sort-arrow"></span></div>
                                    <div class="cdsTYDashboard-table-cell" onclick="sortTable(2)">Form Type <span class="sort-arrow"></span></div>
                                    <div class="cdsTYDashboard-table-cell" onclick="sortTable(3)">Email <span class="sort-arrow"></span></div>
                                    <div class="cdsTYDashboard-table-cell" onclick="sortTable(4)">Status <span class="sort-arrow"></span></div>
                                    <div class="cdsTYDashboard-table-cell">Action</div>
                                </div>
                                <div class="cdsTYDashboard-table-body" id="tableList">
                                </div>  @include('components.table-pagination01') 
                            </div>
                        </div>  
						
                        <!-- # div table -->
			</div>
	
	</div>
  </div>
</div>


<!-- End Content -->
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

  function loadData(page = 1) {
    var search = $("#search-input").val();
    $.ajax({
      type: "POST",
      url: BASEURL + '/my-services/send-assesment-form-ajax-list?page=' + page,
      data: {
        _token: csrf_token,
        id: "{{$id}}"
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