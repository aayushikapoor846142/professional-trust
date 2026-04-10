
<div class="container">
    <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-header">
            @include("admin-panel.09-utilities.earning-reports.components.appointment-header-search")
            <div class="cds-action-elements">
                Earnings: ${{totalProfessionalEarning('appointment',auth()->user()->id)}}
            </div>
        </div>

        <div class="cds-ty-dashboard-box-body">
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
                        <div class="cdsTYDashboard-table-cell sorted-asc" onclick="sortTable(1)">Invoice No <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell" onclick="sortTable(2)">Client Name <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell" onclick="sortTable(3)">Appointment Id <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell" onclick="sortTable(6)">Earning Amount<span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell" onclick="sortTable(4)">Paid Date <span class="sort-arrow"></span></div>
                        <div class="cdsTYDashboard-table-cell" onclick="sortTable(5)">Crated Date <span class="sort-arrow"></span></div>
                        
                    </div>
                    <div class="cdsTYDashboard-table-body" id="appointmentTableList">
                    </div>
                    <div id="common-skeleton-loader" style="display:none;">
                        @include('components.loaders.earning-report-loader')              
                    </div> @include('components.table-pagination01') 							 
                </div>
            </div>
            <!-- # div table -->
        </div>
        </div>
</div>


@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $("#search-form").submit(function(e) {
            e.preventDefault();
            appointmentLoadData();
        });

        $(".appointment-next").click(function() {
            if (!$(this).hasClass('disabled')) {
                changeAppointmentPage('next');
            }
        });
        $(".appointment-previous").click(function() {
            if (!$(this).hasClass('disabled')) {
                changeAppointmentPage('prev');
            }
        });
        $("#search-input").keyup(function() {
            var value = $(this).val();
            if (value == '') {
                appointmentLoadData();
            }
            if (value.length > 3) {
                appointmentLoadData();
            }
        });

        $("#btnNavbarSearch").click(function() {
            var value = $(this).val();
            if (value == '') {
                appointmentLoadData();
            }
            if (value.length > 3) {
                appointmentLoadData();
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
    appointmentLoadData();

    function appointmentLoadData(page = 1) {
        
        var search = $("#appointment-searchInput").val();

        
         let price_range = $('.appointment-CdsTYDashboard-price-range:checked')
        .map(function () {
            return $(this).val();
        }).get();
        let hour_range = $('.appointment-CdsTYDashboard-hours-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();

        let min_range = $("#appointment-minPrice").val();
        let max_range = $("#appointment-maxPrice").val();
        let startDate = $("#appointmentStartDate").val();
        let endDate = $("#appointmentEndDate").val();

        $.ajax({
            type: "POST",
            url: BASEURL + '/earning-appointment-report-ajax-list?page=' + page,
            data: {
                _token: csrf_token,
                search: search,
                status: "{{$status}}",
                price_range:price_range,
                min_range:min_range,
                max_range:max_range,
                start_date:startDate,
                end_date:endDate,
                hour_range:hour_range
            },
            dataType: 'json',
            beforeSend: function() {
                $("#common-skeleton-loader").show();
      },
      success: function(data) {
        $(".norecord").remove(); 
        $("#appointmentTableList").html(data.contents);
         $("#common-skeleton-loader").hide();
        if (data.total_records > 0) {
          var pageinfo = data.current_page + " of " + data.last_page + " <small class='text-danger'>(" + data.total_records + " records)</small>";
          $("#pageinfo").html(pageinfo);
         
          $("#appointment-pageno").val(data.current_page);
          if (data.current_page < data.last_page) {
            $(".appointment-next").removeClass("disabled");
          } else {
            $(".appointment-next").addClass("disabled", "disabled");
          }
          if (data.current_page > 1) {
            $(".appointment-previous").removeClass("disabled");
          } else {
            $(".appointment-previous").addClass("disabled", "disabled");
          }
          $("#appointment-pageno").attr("max", data.last_page);
        } else {
            $(".cdsTYDashboard-table").find(".norecord").remove();
            var html = '<div class="cdsTYDashboard-empty-list norecord">No records available</div>';
            $(".cdsTYDashboard-table").append(html);
        }
      },
    });
  }

    function changeAppointmentPage(action) {
        var page = parseInt($("#appointment-pageno").val());
        if (action == 'prev') {
            page--;
        }
        if (action == 'next') {
            page++;
        }
        if (!isNaN(page)) {
            appointmentLoadData(page);
        } else {
            errorMessage("Invalid Page Number");
        }

    }

   
</script>
<script>
    function sortTable(columnIndex) {
    const rows = Array.from(document.querySelectorAll('#appointmentTableList .cdsTYDashboard-table-row'));
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
    rows.forEach(row => document.getElementById('appointmentTableList').appendChild(row));
}
</script>
@endpush