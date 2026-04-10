<div class="CdsCompactCaseList-container mt-5">
    <div class="CdsCompactCaseList-header">
        <div class="CdsCompactCaseList-header-item CdsCompactCaseList-case-info">Case Information</div>
        <div class="CdsCompactCaseList-header-item CdsCompactCaseList-team">Team & Client</div>
        <div class="CdsCompactCaseList-header-item CdsCompactCaseList-progress">Progress</div>
        <div class="CdsCompactCaseList-header-item CdsCompactCaseList-posted">Posted</div>
        <div class="CdsCompactCaseList-header-item CdsCompactCaseList-actions">Actions</div>
    </div>

    <div class="CdsCompactCaseList-list" id="compactTableList">
        
    </div>
   
</div>
  @include('components.table-pagination01') 
@push('scripts')
<script>
    let c_page = 1;
    let c_last_page = 1;
    let c_loading = false;
    let c_dataloading = true;
    loadCompactData();
    function loadCompactData(c_page = 1) {
      
        $.ajax({
            type: "POST",
            url: BASEURL + '/case-with-professionals/compact-ajax-list?page=' + c_page,
            data: {
                _token: csrf_token,
            },
            dataType: 'json',
            beforeSend: function() {
                $("#common-skeleton-loader").show();
            },
            success: function(data) {
                $(".norecord").remove();
                $("#compactTableList").html(data.contents);
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
                    $("#compactTableList").html("<div class='norecord text-center py-2'>No records found</div>");
                    $("#pageinfo").html('');
                }
            }
        });
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
        loadCompactData(currentPage);
    }
</script>
@endpush