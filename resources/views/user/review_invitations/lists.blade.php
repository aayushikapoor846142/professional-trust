@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('reviews') !!}
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
   <form id="search-form">
                                    @csrf
                                    <div class="input-group mb-3">
                                        {!! FormHelper::formInputText([ 'name' => 'search', 'id' => 'search-input', 'label' => 'Search By Name' ]) !!}
                                        <button class="btn btn-secondary"><i class="fa fa-search"></i></button>
                                    </div>
                                </form>
 <div class="d-none cds-action-elements">
                        <span class="font-size-sm mr-3">
                            <span id="datatableCounter">0</span>
                            Selected
                        </span>
                        <a class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-multi-delete ml-2" data-href="{{ baseUrl('professionals/mark-as-complete') }}" onclick="markAsComplete(this)" href="javascript:;">
                            Mark as complete
                        </a>
                    </div>
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="cds-ty-dashboard-box-body">
                   
                        <div class="cdsTYDashboard-table-body" id="tableList"></div>
                   
                  
                  @include('components.table-pagination01')
                </div>
      
			</div>
	
	</div>
  </div>
</div>


@endsection

@section('javascript')
<link href="{{url('/inputTags.min.css')}}">
<link href="{{url('/inputTags.css')}}">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="{{url('/inputTags.jquery.min.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/jquery-form@4.3.0/dist/jquery.form.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#emails').inputTags({});

        $(".fetch-professional").on("click", function() {
            $.ajax({
                url: $(this).data('href'),
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
            url: BASEURL + '/reviews/review-invitations/ajax-list?page=' + page,
            data: {
                _token: csrf_token,
                search: search,
                status: "",
            },
            dataType: 'json',
            beforeSend: function() {
                 $(".datatable-custom").addClass("d-none");
    $(".card-table").html('<div class="text-center"><i class="fa fa-spin fa-spinner fa-3x"></i></div>');
                // $("#paginate").html('');
            },
            success: function(data) {
                $("#tableList").html(data.contents);

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
                    var html = '<div class="text-center text-danger norecord">No records available</div>';
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
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: BASEURL + '/invitations-sent/delete/' + id,
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



  
    function markAsComplete(e) {
        var url = $(e).attr("data-href");
        if ($(".row-checkbox:checked").length <= 0) {
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
            if (result.value) {
                if ($(".row-checkbox:checked").length <= 0) {
                    warningMessage("No records selected to delete");
                    return false;
                }
                var row_ids = [];
                $(".row-checkbox:checked").each(function() {
                    row_ids.push($(this).val());
                });
                var ids = row_ids.join(",");
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        _token: csrf_token,
                        ids: ids,
                    },
                    dataType: 'json',
                    beforeSend: function() {

                    },
                    success: function(response) {
                        if (response.status == true) {
                            location.reload();
                        } else {
                            errorMessage(response.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });
            }
        })
    }
</script>
@endsection