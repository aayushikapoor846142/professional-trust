@extends('admin-panel.08-cases.my-cases.my-cases-master')
@section('case-container')


<div class="cds-fs-case-details-overview-panel">
    <div class="cds-fs-case-details-overview-panel-main">
        <div class="cds-fs-case-details-overview-panel-header"></div>
        <div class="cds-fs-case-details-overview-panel-body">
            <div class="row align-items-center mb-2">
                <div class="text-end mb-3">
                    <button type="button" onclick="showPopup('<?php echo baseUrl('my-cases/stages/workflow/'.$case_id) ?>')" class="CdsTYButton-btn-primary btn-sm">Generate Workflow via AI</button>

                 
                </div>
                <div class="cds-fs-case-stage-header-bx">
                    <div class="cds-fs-case-stage-header-inner">
                        <div class="col-sm mb-2 mb-sm-0 cds-fs-case-stage-page-title">
                            <h2 class="h4 mb-0">{{$pageTitle}}</h2>
                        </div>
                        <div class="col-sm-auto cds-fs-case-stage-add-btn">
                            <a href="jsvscript:;" class="CdsTYButton-btn-primary" onclick="showPopup('<?= baseUrl('my-cases/stages/add/' . $case_id) ?>')"><i class="fa-solid fa-plus"></i> Add</a>

                            <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('my-cases/stages/add-workflow/'.$case_id) ?>')" class="CdsTYButton-btn-primary">Add Predefined Flow</a>
                        </div>
                    </div>
                </div>
                <div id="case-stages-loader" class="mt-50" style="display: none;">
                @include('components.skelenton-loader.case-stages-skeleton')  
                </div>
                <div class="col-xl-12">
                    <div id="stages-list"></div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    loadData();
});
function loadData() {
    $("#case-stages-loader").show();
    var case_id = "{{$case_id}}";
    $.ajax({
        type: "POST",
        url: BASEURL + '/my-cases/stages/ajax-list',
        data: {
            _token: csrf_token,
            case_id: case_id
        },
        dataType: 'json',
        success: function(data) {
            $("#stages-list").html(data.contents);

        },
        complete: function() {
            $("#case-stages-loader").hide(); 
        }
    });
}
function generateWorkFlow(){
    var case_id = "{{$case_id}}";
    $.ajax({
        url: '{{ baseUrl('my-cases/stages/generate-workflow') }}',
        type: "post",
        data: {
            _token:csrf_token,
            case_id:case_id
        },
        dataType: "json",
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            hideLoader();
            // if (response.status == true) {
            //     successMessage(response.message);
            //     location.reload();
            //     // $("#description").val(response.message);
            // } else {
            //     errorMessage(response.message);
            // }
        },
        error: function() {
            internalError();
        }
    });
}
function markAsSubStageComplete(id) {


    Swal.fire({
        title: "Are you sure to Want to mark as complete?",
        // text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "{{baseUrl('/my-cases/stages/sub-stages/mark-as-complete')}}",
                data: {
                    _token: csrf_token,
                    id: id,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        successMessage(response.message);
                        location.reload();
                    } else {
                        errorMessage(response.message);
                    }
                },
            });
        }
    });


}
</script>

@endsection