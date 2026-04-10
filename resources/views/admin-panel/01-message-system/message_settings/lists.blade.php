@extends('admin-panel.layouts.app')
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header"><div class="cds-form-container search-area">
                        <div class="row justify-content-end">
                            <div class="col-lg-6 col-md-12 col-xl-6 col-xxl-5">
                                <form id="search-form">
                                    @csrf
                                    <div class="input-group mb-3">
                                        {!! FormHelper::formInputText([
                                        'name' => 'search',
                                        'label' => 'Search'
                                        ]) !!}
                                        <button class="btn btn-secondary"><i class="fa fa-search"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
   <h4>Add Emails</h4>
          
            @if ($errors->has('emails.*'))
            @foreach ($errors->get('emails.*') as $messages)
            @foreach ($messages as $message)
            <div style="color:red">{{ $message }}</div>
            @endforeach
            @endforeach
            @endif
  <div class="ch-head">
                            <i class="fa-table fas me-1"></i>
                            List all Data
                        </div>
                        <div class="d-flex justify-content-between">
                            <div id="datatableCounterInfo" style="display: none" >
                                <div class="align-items-center">
                                    <span class="font-size-sm mr-3">
                                    <span id="datatableCounter">0</span>
                                    Selected
                                    </span>
                                    <a class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-multi-delete ml-2" data-href="{{ baseUrl('professionals/mark-as-complete') }}"
                                        onclick="markAsComplete(this)" href="javascript:;">
                                    Mark as complete
                                    </a>
                                </div>
                            </div>
                          
                        </div>
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                    <!-- div table -->
                    <div class="cdsTYDashboard-table">
                        <div class="cdsTYDashboard-table-wrapper">
                            <div class="cdsTYDashboard-table-header">
                                <div class="cdsTYDashboard-table-cell sorted-asc" onclick="sortTable(0)">Company Name <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" onclick="sortTable(1)">Email <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell">Action</div>
                            </div>
                            <div class="cdsTYDashboard-table-body" id="tableList">
                            </div> 
							@include('components.table-pagination01') 
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
<link href="{{url('/inputTags.min.css')}}"></link>
<link href="{{url('/inputTags.css')}}"></link>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="{{url('/inputTags.jquery.min.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/jquery-form@4.3.0/dist/jquery.form.min.js"></script>

<script>
    function addEmailField() {
        const container = document.getElementById('email-container');
        const input = document.createElement('input');
        input.type = 'email';
        input.name = 'emails[]';
        input.placeholder = 'Enter email';
        container.appendChild(input);
    }
</script>

    <script type="text/javascript">
        // function submitform(){
        //     ;
        //     if($('#emails_form').submit()){
        //         alert('form-submitted');
        //     } 
        // }
        //   document.getElementById('emails_form').addEventListener('keydown', function(event) {
        //     if (event.key === 'Enter') {
        //     event.preventDefault(); // Prevent the default form submission
        //     }
        // });

        $(document).ready(function() {
			$('#emails').inputTags({
			});

            $(".fetch-professional").on("click", function() {
                $.ajax({
                    url: $(this).data('href'),
                    dataType:'json',
                    success: function (result) {
                        if(result.status == true){
                            successMessage(result.message);
                            loadData();
                        }else{
                            errorMessage(result.message);
                        }
                    },
                });
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
            $("#datatableSearch").keyup(function() {
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
            $("#search-form").submit(function(e){
                e.preventDefault();
                loadData();
            });

        })
        loadData();

        function loadData(page = 1) {
            var search = $("#datatableSearch").val();
            $.ajax({
                type: "POST",
                url: BASEURL + '/site-settings/ajax-list?page=' + page,
                data: {
                    _token: csrf_token,
                    search:  $("#search-form").serialize(),
                    status: "",
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
                        url: BASEURL + '/invitations-sent/delete/'+id,
                        data: {
                            _token: csrf_token,
                            id: id,
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.status == true) {
                                Swal.fire({
                                    type: "success",
                                    title: 'Deleted!',
                                    text: 'Record has been deleted.',
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

        function changeStatus(e) {
            var id = $(e).attr("data-id");
            if ($(e).is(":checked")) {
                $.ajax({
                    type: "POST",
                    url: BASEURL + '/user/status/active',
                    data: {
                        _token: csrf_token,
                        id: id,
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == true) {
                            successMessage(result.message);
                            loadData();
                        } else {
                            errorMessage(result.message);
                        }
                    },
                });
            } else {
                $.ajax({
                    type: "POST",
                    url: BASEURL + '/user/status/inactive',
                    data: {
                        _token: csrf_token,
                        id: id,
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == true) {
                            successMessage(result.message);
                            loadData();
                        } else {
                            errorMessage(result.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });
            }
        }

        function profileStatus(e) {
            var id = $(e).attr("data-id");
            if ($(e).is(":checked")) {
                $.ajax({
                    type: "POST",
                    url: BASEURL + '/user/profile-status/active',
                    data: {
                        _token: csrf_token,
                        id: id,
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == true) {
                            successMessage(result.message);
                        } else {
                            errorMessage(result.message);
                        }
                    },
                });
            } else {
                $.ajax({
                    type: "POST",
                    url: BASEURL + '/user/profile-status/inactive',
                    data: {
                        _token: csrf_token,
                        id: id,
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.status == true) {
                            successMessage(result.message);
                            loadData();
                        } else {
                            errorMessage(result.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });
            }
        }

        function markAsComplete(e){
            var url = $(e).attr("data-href");
            if($(".row-checkbox:checked").length <= 0){
                warningMessage("No records selected to delete");
                return false;
            }
            Swal.fire({
            title: 'Are you sure to mark as complete?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            confirmButtonClass: 'CdsTYButton-btn-primary',
            cancelButtonClass: 'CdsTYButton-btn-primary CdsTYButton-border-thick ml-1',
            buttonsStyling: false,
            }).then(function(result) {
                if(result.value){
                    if($(".row-checkbox:checked").length <= 0){
                        warningMessage("No records selected to delete");
                        return false;
                    }
                    var row_ids = [];
                    $(".row-checkbox:checked").each(function(){
                        row_ids.push($(this).val());
                    });
                    var ids = row_ids.join(",");
                    $.ajax({
                        type: "POST",
                        url: url,
                        data:{
                            _token:csrf_token,
                            ids:ids,
                        },
                        dataType:'json',
                        beforeSend:function(){

                        },
                        success: function (response) {
                            if(response.status == true){
                                location.reload();
                            }else{
                                errorMessage(response.message);
                            }
                        },
                        error:function(){
                            internalError();
                        }
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
