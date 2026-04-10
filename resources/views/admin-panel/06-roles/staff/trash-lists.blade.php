@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('accounts') !!}
@endsection
@section('content')
<!-- Content -->
<div class="container-fluid">
    <section class="cds-ty-dashboard-breadcrumb-container">
        <div class="cds-main-layout-header">
            <div class="breadcrumb-conatiner">
                <ol class="breadcrumb">
                    <i class="fa-grid-2 fa-regular"></i>
                    <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('/') }}">Dashboard</a></li>
                    <li class="active breadcrumb-item" aria-current="page">{{$pageTitle}}</li>
                </ol>
            </div>
            <div class="cds-heading">
                <div class="cds-heading-icon">
                    <i class="fa-light fa-pen"></i>
                </div>
                <h1>{{$pageTitle}}</h1>
            </div>
        </div>
    </section>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
                <div class="cds-ty-dashboard-box-header">
                    <div class="search-area">
                        <div class="cds-form-container">
                            <form id="search-form">
                                @csrf
                                <div class="row justify-content-between">
                                    <div class="col-lg-3 col-md-5 col-sm-6 col-xl-3">
                                        {!! FormHelper::formSelect([
                                            'name' => 'role',
                                            'label' => 'Select Role',
                                            'class' => 'select2-input ga-country',
                                            'options' => getRoles(),
                                            'value_column' => 'name',
                                            'label_column' => 'name',
                                            'selected' => old('role') ?? null,
                                            'is_multiple' => false
                                        ]) !!}
                                    </div>
                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xl-6">
                                        <div class="input-group mb-3 mt-3 mt-sm-0">
                                            {!! FormHelper::formInputText([
                                                'name' => 'search',
                                                'id'=> 'search-input',
                                                'label' => 'Search'
                                            ]) !!}
                                            <button class="btn btn-secondary"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="cds-action-elements">
                        <span class="font-size-sm mr-3">
                            <span id="datatableCounter">0</span>
                            Selected
                        </span>
                        <a class="btn btn-danger btn-multi-delete ml-2" data-href="{{ baseUrl('staff/restore-multiple') }}"
                            onclick="deleteMultiple(this)" href="javascript:;" data-action="Are you sure to Restore">
                            Restore
                        </a>
                    </div>
                </div>
                <div class="cds-ty-dashboard-box-body">
                    <div class="cdsTYDashboard-table">
                        <div class="cdsTYDashboard-table-wrapper">
                            <div class="cdsTYDashboard-table-header">
                                <div class="cdsTYDashboard-table-cell cdsCheckbox" onclick="sortTable(0)">
                                    <div class="custom-control custom-checkbox">
                                        <input id="datatableCheckAll" type="checkbox" class="custom-control-input">
                                        <label class="custom-control-label" for="datatableCheckAll"></label>
                                    </div>
                                </div>
                                <div class="cdsTYDashboard-table-cell sorted-asc" onclick="sortTable(1)">Name <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" onclick="sortTable(2)">Email <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" onclick="sortTable(3)">Phone no <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" onclick="sortTable(4)">Role <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell" onclick="sortTable(5)">Status <span class="sort-arrow"></span></div>
                                <div class="cdsTYDashboard-table-cell">Action</div>
                            </div>
                            <div class="cdsTYDashboard-table-body" id="tableList">
                            </div>
                            <div id="common-skeleton-loader" style="display:none;">
                                @include('components.loaders.common-loader')              
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cds-ty-dashboard-box-footer">
                    <div class="row align-items-sm-center justify-content-center justify-content-sm-between">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                                <span class="mr-2">Page:</span>
                                <span id="pageinfo"></span>
                            </div>
                        </div>
                        <div class="col-md-6 pull-right">
                            <div class="d-flex justify-content-center justify-content-md-end">
                                <nav id="datatablePagination" aria-label="Activity pagination">
                                    <div class="dataTables_paginate paging_simple_numbers" id="datatable_paginate">
                                        <ul id="datatable_pagination" class="justify-content-center justify-content-md-start datatable-custom-pagination pagination">
                                            <li class="disabled page-item paginate_item previous">
                                                <a class="btn btn-primary page-link paginate_button" aria-controls="datatable"
                                                    data-dt-idx="0" tabindex="0" id="datatable_previous"><span aria-hidden="true">Prev</span></a>
                                            </li>
                                            <li class="me-2 ms-2 page-item paginate_item">
                                                <input onblur="changePage('goto')" min="1" type="number" id="pageno"
                                                    class="form-control text-center" />
                                            </li>
                                            <li class="disabled next page-item paginate_item">
                                                <a class="btn btn-primary page-link paginate_button" aria-controls="datatable"
                                                    data-dt-idx="3" tabindex="0"><span aria-hidden="true">Next</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Content -->
@endsection
@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    $(".ga-country").change(function() {
        loadData();
    });
    $("#search-form").submit(function(e) {
        e.preventDefault();
        loadData();
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
});
loadData();
function loadData(page = 1) {
    var search = $("#search-input").val();
    $.ajax({
        type: "POST",
        url: BASEURL + '/staff/trash-staff-ajax-list?page=' + page,
        data: $("#search-form").serialize(),
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
        confirmButtonClass: 'btn btn-primary',
        cancelButtonClass: 'btn btn-danger ml-1',
        buttonsStyling: false,
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: BASEURL + '/staff/delete-user',
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
                            confirmButtonClass: 'btn btn-primary',
                            buttonsStyling: false,
                        });
                    }
                },
            });
        }
    });
}
function sortTable(columnIndex) {
    const rows = Array.from(document.querySelectorAll('#tableList .cdsTYDashboard-table-row'));
    let isAscending = document.querySelectorAll('.cdsTYDashboard-table-header .cdsTYDashboard-table-cell')[columnIndex].classList.contains('sorted-asc');
    document.querySelectorAll('.cdsTYDashboard-table-header .cdsTYDashboard-table-cell').forEach(cell => {
        cell.classList.remove('sorted-asc', 'sorted-desc');
    });
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
    rows.forEach(row => document.getElementById('tableList').appendChild(row));
}
</script>
@endsection
