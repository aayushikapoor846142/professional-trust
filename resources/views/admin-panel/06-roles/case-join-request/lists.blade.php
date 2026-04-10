@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('accounts') !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/common-table-redesign.css') }}">
@endsection
@section('content')
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
                 @include("admin-panel.06-roles.roles.components.header-search") 
                <div class="cds-ty-dashboard-box-header">
                    <div class="cds-ty-dashboard-box-body">
                        <div class="cdsTYDashboard-table">
                            <div class="cdsTYDashboard-table-wrapper">
                                <div class="cdsTYDashboard-table-header">
                                    <div class="cdsTYDashboard-table-cell sorted-asc" data-column="name" data-order="asc" onclick="sortTable(this)">Associate Name <span class="sort-arrow"></span></div>
                                    <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Created At <span class="sort-arrow"></span></div>
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
                                                    <a class="btn btn-primary page-link paginate_button" aria-controls="datatable" data-dt-idx="0" tabindex="0" id="datatable_previous"><span aria-hidden="true">Prev</span></a>
                                                </li>
                                                <li class="me-2 ms-2 page-item paginate_item">
                                                    <input onblur="changePage('goto')" min="1" type="number" id="pageno" class="form-control text-center" />
                                                </li>
                                                <li class="disabled next page-item paginate_item">
                                                    <a class="btn btn-primary page-link paginate_button" aria-controls="datatable" data-dt-idx="3" tabindex="0"><span aria-hidden="true">Next</span></a>
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
</div>
@endsection
@section('javascript')
<script type="text/javascript">
const cookiePrefix = 'roles_'; 
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
    });
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

    function loadData(page = 1, search = "") {
        var search = $("#searchInput").val();
        $.ajax({
            type: "POST",
            url: BASEURL + '/case-join-requests/ajax-list?page=' + page,
            data: {
                _token: csrf_token,
                search: search,
                           sort_direction:sortDirection,
		sort_column:sortColumn
            },
            dataType: 'json',
            beforeSend: function() {
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
                        $(".next").removeClass('disabled');
                    } else {
                        $(".next").addClass('disabled');
                    }
                    if (data.current_page > 1) {
                        $(".previous").removeClass('disabled');
                    } else {
                        $(".previous").addClass('disabled');
                    }
                } else {
                    $("#tableList").html("<div class='norecord text-center py-2'>No records found</div>");
                    $("#pageinfo").html('');
                }
            }
        });
    }
    $("#pageno").on('change', function() {
        changePage('goto');
    });
    function changePage(type) {
        var currentPage = parseInt($("#pageno").val());
        if (type === 'next') {
            currentPage++;
        } else if (type === 'prev') {
            currentPage--;
        }
        loadData(currentPage);
    }
</script>
@endsection