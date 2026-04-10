<div class="cds-ty-dashboard-box">
    <div class="cds-ty-dashboard-box-header">
        @include("admin-panel.06-roles.staff.components.header-search") 
        @if(checkPrivilege([
            'route_prefix' => 'panel.staff',
            'module' => 'professional-staff',
            'action' => 'delete'
        ]))
        
        <div class="cds-action-elements">
            <span class="font-size-sm mr-3">
                <span id="datatableCounter">0</span>
                Selected
            </span>
            <a class="btn btn-danger btn-multi-delete ml-2" data-href="{{ baseUrl('staff/delete-multiple') }}"
                onclick="deleteMultiple(this)" href="javascript:;">
                Delete
            </a>
        </div>
        @endif
    </div>
    <div class="cds-ty-dashboard-box-body">
        <div class="cdsTYDashboard-table">
            <div class="cdsTYDashboard-table-wrapper">
                <div class="cdsTYDashboard-table-header">
                    <div class="cdsTYDashboard-table-cell cdsCheckbox">
                        <div class="custom-control custom-checkbox">
                            <input id="datatableCheckAll" type="checkbox" class="custom-control-input">
                            <label class="custom-control-label" for="datatableCheckAll"></label>
                        </div>
                    </div>
                    <div class="cdsTYDashboard-table-cell sorted-asc" data-column="first_name" data-order="asc">Name <span class="sort-arrow"></span></div>
                    <div class="cdsTYDashboard-table-cell sorted-asc" data-column="email" data-order="asc">Email <span class="sort-arrow"></span></div>
                    <div class="cdsTYDashboard-table-cell">Phone no <span class="sort-arrow"></span></div>
                    <div class="cdsTYDashboard-table-cell">Role <span class="sort-arrow"></span></div>
                    <div class="cdsTYDashboard-table-cell sorted-asc" data-column="status" data-order="asc">Status <span class="sort-arrow"></span></div>
                       <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc">Created At <span class="sort-arrow"></span></div>
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
<script type="text/javascript">

var  cookiePrefix = 'active_staff_';
var sortColumn = getCookie(cookiePrefix + 'sortColumn') || 'created_at';
var sortDirection = getCookie(cookiePrefix + 'sortDirection') || 'desc';

function sendInvitation(email) {
    var url = "{{ baseUrl('connections/invitations/save') }}";
    $.ajax({
        url: url,
        type: "post",
        data: {
            _token: "{{csrf_token()}}",
            email: email
        },
        dataType: "json",
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            hideLoader();
            if (response.status == true) {
                successMessage(response.message);
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
$(document).on('click', '.cdsTYDashboard-table-cell[data-column]', function() {
    sortTable(this);
});
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
    var search = $("#search-input").val();
    let status = $('.CdsTYDashboard-status-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();
    let role = $('.CdsTYDashboard-role-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();
    $.ajax({
        type: "POST",
        url: BASEURL + '/staff/ajax-list?page=' + page,
        data:{
            status:status,
            role:role
        },
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
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

</script>