@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')
@section('case-container')


<div class="cds-fs-case-details-overview-panel">
    <div class="cds-fs-case-details-overview-panel-main">
        <div class="cds-fs-case-details-overview-panel-header"></div>
        <div class="cds-fs-case-details-overview-panel-body">
            <div class="row align-items-center mb-2">
                <div class="col-sm mb-2 mb-sm-0">
                    <h2 class="h4 mb-0">{{$pageTitle}}</h2>
                </div>

                <div class="col-sm-auto">
                    <a href="jsvscript:;" class="CdsTYButton-btn-primary" onclick="showPopup('<?= baseUrl('case-with-professionals/stages/add/' . $case_id) ?>')">  Add</a>
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

        function loadData() {
            var case_id = "{{$case_id}}";
            $.ajax({
                type: "POST",
                url: BASEURL + '/case-with-professionals/stages/ajax-list',
                data: {
                    _token: csrf_token,
                    case_id: case_id
                },
                dataType: 'json',
                success: function(data) {
                    $("#stages-list").html(data.contents);

                },
            });
        }

    });

</script>

@endsection