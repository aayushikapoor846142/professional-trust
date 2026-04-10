

@section('content')   
   
   
  
  <div class="cds-ty-dashboard-box">
      <div class="search-area">
          <div class="row justify-content-end">
              <div class="col-lg-6 col-md-6 col-sm-8 col-xl-6">
                  <form id="search-form">
                      @csrf
                      <div class="input-group mb-3">
                    {!! FormHelper::formInputText(['name'=>"leave_date", 'readonly'=>true, 'id'=>"calendarInput", 'label'=>"Select dates"]) !!}

                          <button class="btn btn-secondary" id="getDateBtn"><i class="fa fa-search"></i></button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
      <div class="cds-ty-dashboard-box-header">
          @if(checkPrivilege([
                  'route_prefix' => 'panel.block-dates',
                  'module' => 'professional-block-dates',
                  'action' => 'delete'
              ]))     
            <div class="cds-action-elements">
              
              <span class="font-size-sm mr-3">
                  <span id="datatableCounter">0</span>
                  Selected
              </span>
              <a class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-multi-delete ml-2" data-href="{{ baseUrl('appointments/block-dates/delete-multiple') }}" onclick="deleteMultiple(this)" href="javascript:;">
                  <i class="fa-solid fa-trash-arrow-up"></i>
                  Delete
              </a>
              
          </div>
        @endif
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
                          <div class="cdsTYDashboard-table-cell sorted-asc" onclick="sortTable(1)">Leave Date <span class="sort-arrow"></span></div>
                          <div class="cdsTYDashboard-table-cell" onclick="sortTable(2)">Location <span class="sort-arrow"></span></div>
                          <div class="cdsTYDashboard-table-cell" onclick="sortTable(3)">Reason <span class="sort-arrow"></span></div>
                          <div class="cdsTYDashboard-table-cell" onclick="sortTable(4)">Added By <span class="sort-arrow"></span></div>
                          <div class="cdsTYDashboard-table-cell">Action</div>
                      </div>
                      <div class="cdsTYDashboard-table-body" id="tableList">
                      </div>  
                  </div>
              </div>
              <!-- # div table -->    @include('components.table-pagination01') 	                       
          </div>
           </div>
  </div>

<!-- End Content -->
@endsection

@section('javascript')


<script src="{{url('assets/js/custom-editor.js')}}"></script>
<script src="{{url('assets/js/custom-datepicker.js')}}"></script>
<link href="{{ url('assets/css/custom-datepicker.css') }}" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script type="text/javascript">

  $(document).ready(function() {
       CustomCalendarWidget.initialize("calendarInput");

$('#getDateBtn').on('click', function () {
    const selectedDate = $('#calendarInput').val(); 
    loadData(1, selectedDate);
  });

     loadData();
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


  function loadData(page = 1,selectedDate = '') {
    var search = $("#search-input").val();
    $.ajax({
      type: "POST",
      url: BASEURL + '/appointments/block-dates/ajax-list?page=' + page,
      data: {
        _token: csrf_token,
        search: search,
        selected_date: selectedDate 
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
          url: BASEURL + '/appointments/block-dates/delete',
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