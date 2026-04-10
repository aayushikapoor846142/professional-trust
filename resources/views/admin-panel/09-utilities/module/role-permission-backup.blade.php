@extends('admin-panel.layouts.app')
@section('content')
<div class="container-fluid">
 <div class="ch-action">
            <a href="{{ baseUrl('module') }}" class="CdsTYButton-btn-primary">
                <i class="fa-solid fa-angle-left"></i>
                Back
            </a>
        </div>
    <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-header">
        </div>
        <div class="cds-ty-dashboard-box-body">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        Accordion Item #1
                        </button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">Content for Section 1 goes here.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                        Accordion Item #2
                        </button>
                    </h2>
                    <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            Content for Section 2 goes here.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                        Accordion Item #3
                        </button>
                    </h2>
                    <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            Content for Section 3 goes here.
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ baseUrl('module/role-privileges')}}" method="POST" id="form">
                @csrf
                <div class="accordion">
                    @foreach(get_roles() as $key => $role)
                    <div class="accordion-item open mb-3">
                        <div class="accordion-header">
                            <h4 class="font20">{{$role}}</h4>
                        </div>
                        <div class="accordion-content" style="display: block;">
                            @foreach($records as $record)                                
                            <div class="card-box-round mb modules cdc-form-list cdc-admin-card">
                                <div class="d-flex header-card">
                                    <div class="float-left">
                                        {{$record->name}} <span class="sub-title">({{$record->slug}})</span>
                                    </div>
                                    <div class="form-check">
                                        <label class="checkbox required" for="check-{{$key}}-{{$key}}">
                                        <input type="checkbox" class="check-all"  value="1" id="check-{{$key}}-{{$record->id}}">
                                        <span class="checkmark"></span> Check All
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions">
                                    @foreach($record->moduleAction as $action)
                                    <div class="permission-block">
                                        <div class="form-check">
                                            <label class="checkbox required" for="check-{{$key}}-{{$record->id}}-{{$action->id}}">
                                            <input type="checkbox" @if(isset($role_wise_permissions[$role][$record->slug]) && in_array($action->action,$role_wise_permissions[$role][$record->slug])) checked @endif class="action-check" name="permission[{{$role}}][{{$record->slug}}][]" value="{{$action->action}}" id="check-{{$key}}-{{$record->id}}-{{$action->id}}">
                                            <span class="checkmark"></span> <span>{{$action->action}}</span>
                                            </label>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="text-start">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
<!-- End Content -->
@section('javascript')
    <script>
       $(document).ready(function(){
        pageLoad();
            $(".check-all").change(function(){
                if($(this).is(":checked")){
                    $(this).parents(".modules").find(".action-check").prop("checked",true);
                }else{
                    $(this).parents(".modules").find(".action-check").prop("checked",false);
                }
            });


            // When any checkbox is clicked
            $('.action-check').click(function() {
           
                if($(this).parents('.card-box-round').find('.action-check').length == $(this).parents('.card-box-round').find('.action-check:checked').length){
                    $(this).parents('.card-box-round').find('.check-all').prop('checked',true);
                }else{
                        $(this).parents('.card-box-round').find('.check-all').prop('checked',false);
                }
               
            });

            $("#form").submit(function(e) {
                e.preventDefault();
           
                var formData = new FormData($(this)[0]);
                var url = $("#form").attr('action');

                $.ajax({
                    url: url,
                    type: "post",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        hideLoader();
                        if (response.status == true) {
                            successMessage(response.message);
                            redirect(response.redirect_back);
                        } else {
                            errorMessage(response.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });
            });
        })

        function pageLoad()
        {
            $('.action-check').each(function() {
                if($(this).parents('.card-box-round').find('.action-check').length == $(this).parents('.card-box-round').find('.action-check:checked').length){
                    $(this).parents('.card-box-round').find('.check-all').prop('checked',true);
                }else{
                        $(this).parents('.card-box-round').find('.check-all').prop('checked',false);
                }
            });
           
        }
    </script>
@endsection

