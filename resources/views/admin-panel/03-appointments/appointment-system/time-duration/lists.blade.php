@if(checkPrivilege([
    'route_prefix' => 'panel.time-duration',
    'module' => 'professional-time-duration',
    'action' => 'add'
]))

<div class="CdsTYDashboardAppointment-settings-glass-container">
    <div class="CdsTYDashboardAppointment-settings-glass-header">
        <h3 class="CdsTYDashboardAppointment-settings-glass-title">Time Duration</h3>
        <a href="javascript:;" onclick="openCustomPopup(this)" data-href="{{ baseUrl('time-duration/add') }}" class="CdsTYDashboardAppointment-settings-glass-btn">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 3.5V12.5M3.5 8H12.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Add New
        </a>
    </div>
    @endif
    
    <div class="CdsTYDashboardAppointment-settings-controls">
        @if(checkPrivilege([
            'route_prefix' => 'panel.time-duration',
            'module' => 'professional-time-duration',
            'action' => 'delete'
        ]))
        <div class="CdsTYDashboardAppointment-settings-selection-info">
            <span class="CdsTYDashboardAppointment-settings-selected-count">
                <span id="datatableCounter">0</span> Selected
            </span>
            <a class="CdsTYDashboardAppointment-settings-delete-selected btn-multi-delete" 
               data-href="{{ baseUrl('time-duration/delete-multiple') }}" 
               onclick="deleteMultiple(this)" 
               href="javascript:;"
               id="deleteSelectedBtn"
               disabled>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M5.5 3.5V2.5C5.5 1.94772 5.94772 1.5 6.5 1.5H9.5C10.0523 1.5 10.5 1.94772 10.5 2.5V3.5M2.5 3.5H13.5M12 3.5V13C12 13.5523 11.5523 14 11 14H5C4.44772 14 4 13.5523 4 13V3.5M6.5 7V10.5M9.5 7V10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Delete
            </a>
        </div>
        @endif
        <div class="CdsTYDashboardAppointment-settings-search-wrapper">
            <form id="search-form">
                @csrf
                <div class="cdsserchbar">
                    <svg class="CdsTYDashboardAppointment-settings-search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 19L14.65 14.65" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <input type="text" class="CdsTYDashboardAppointment-settings-glass-search" id="search-input" name="search" placeholder="Search...">
                </div>
            </form>
        </div>
    </div>
    
    <div class="CdsTYDashboardAppointment-settings-glass-list" id="tableList">
        <!-- Dynamic content will be loaded here --><div id="common-skeleton-loader" style="display:none;">
        @include('components.loaders.time-duration-loader')              
    </div>
    </div>
     @include('components.table-pagination01') 

</div>

<script type="text/javascript">
$(document).ready(function() {
    // Initialize
    loadData();
    $(document).on("change","#search-input",function(){
      if($(this).val().length > 2){
        loadData();
      }else{
        if($(this).val() == ''){
          loadData();
        }
      }
    })
    // Next button click
    $(".next").click(function() {
        if (!$(this).hasClass('disabled')) {
            changePage('next');
        }
    });
    
    // Previous button click
    $(".previous").click(function() {
        if (!$(this).hasClass('disabled')) {
            changePage('prev');
        }
    });
    
    // Search form submit
    $("#search-form").submit(function(e) {
        e.preventDefault();
        loadData();
    });
    
    // Select all checkbox
    $(document).on('change', '#datatableCheckAll', function() {
        if ($(this).is(":checked")) {
            $(".row-checkbox").prop("checked", true);
        } else {
            $(".row-checkbox").prop("checked", false);
        }
        updateSelectionCount();
    });
    
    // Individual checkbox change
    $(document).on('change', '.row-checkbox', function() {
        updateSelectionCount();
        const item = $(this).closest('.CdsTYDashboardAppointment-settings-glass-list-item');
        if (item) {
            if ($(this).is(':checked')) {
                item.css({
                    'border-color': 'rgba(102, 126, 234, 0.5)',
                    'background': 'rgba(255, 255, 255, 0.8)'
                });
            } else {
                item.css({
                    'border-color': '',
                    'background': ''
                });
            }
        }
    });
});

// Update selection count
function updateSelectionCount() {
    const checkedBoxes = $('.row-checkbox:checked');
    const count = checkedBoxes.length;
    $('#datatableCounter').text(count);
    
    const deleteBtn = $('#deleteSelectedBtn');
    if (count > 0) {
        deleteBtn.removeAttr('disabled');
    } else {
        deleteBtn.attr('disabled', 'disabled');
    }
}

// Load data function
function loadData(page = 1) {
    var search = $("#search-input").val();
    $.ajax({
        type: "POST",
        url: BASEURL + '/time-duration/ajax-list?page=' + page,
        data: {
            _token: csrf_token,
            search: search
        },
        dataType: 'json',
        beforeSend: function() {
            $("#common-skeleton-loader").show();
        },
        success: function(data) {
            $("#tableList").html(data.contents);
            $("#common-skeleton-loader").hide();
            if (data.total_records > 0) {
                var pageinfo = "Page " + data.current_page + " of " + data.last_page + " (" + data.total_records + " records)";
                $("#pageinfo").html(pageinfo);
                $("#pageno").val(data.current_page);
                
                if (data.current_page < data.last_page) {
                    $(".next").removeClass("disabled");
                } else {
                    $(".next").addClass("disabled");
                }
                
                if (data.current_page > 1) {
                    $(".previous").removeClass("disabled");
                } else {
                    $(".previous").addClass("disabled");
                }
                
                $("#pageno").attr("max", data.last_page);
            } else {
                $("#tableList").html('<div class="text-center text-danger py-4">No records available</div>');
                $("#pageinfo").html('');
            }
        },
        error: function() {
            $("#tableList").html('<div class="text-center text-danger py-4">Error loading data</div>');
        }
    });
}

// Change page function
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