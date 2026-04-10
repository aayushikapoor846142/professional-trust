
<style>
    .disabled-link {
        pointer-events: none;  
        color: #aaa;           
        text-decoration: none;  
        cursor: default;        
    }
</style>
<div class="border p-2 mt-3 add-{{$service_id}}-{{$service_type->id}}">
    <div class="row">
        <div class="col-md-10">{{$service_type->name}}</div>
        <div class="col-md-2">
            <div class="col-md-2">
            @if($professionalSubService->status == "pending")
                <i class="fa fa-exclamation-circle text-danger" data-bs-toggle="tooltip" title="Please Configure details pending" style="font-size:20px;"></i>
            @endif
            <a href="javascript:;" id="" class="btn btn-sm btn-primary openSubServicesSlideBtn" data-servicetypeid="{{$service_type->unique_id}}" data-serviceid="{{$service_id}}">
                Configure
            </a>
             </div>
        </div>
        <!-- <div class="col-md-2"><a href="javascript:;" class="CdsTYButton-btn-primary CdsTYButton-border-thick">
    <i class="fa fa-times"></i>
</a>
</div> -->
    </div>

</div>
<!-- slider -->
<div id="SubServicesSlideView" class="CDSBookingsFlow-duration-slide-view SubServicesSlideView">
    <div class="CDSBookingsFlow-duration-slide-content">
        <h3>Additional Settings</h3>
        <span id="" class="CDSBookingsFlow-duration-close-btn closeSubServicesSlideBtn"><i class="fa-sharp fa-regular fa-xmark" aria-hidden="true"></i></span>
        <div class="cds-t25n-content-professional-profile-container-main-navigation">
            <h6 class="text-danger">Please submit the Fees Detail first before adding Additional Detail.</h6>
            <ul class="status-tabs">
                <li class="cds-active fees-detail-li">
                    <a href="#" class="add-tab-link cds-active" data-tab="add-fees-detail">Fees Detail</a>
                </li>
                <li class="additional-detail-li">
                    <a href="#" class="add-tab-link disabled-link" data-tab="add-additional-detail">Additional Detail</a>
                </li>
            </ul>
        </div>
        <div id="add-fees-detail" class="add-tab-content add-fees-detail">
            <form id="form-{{ $service_type->unique_id }}" class="js-validate mt-3 add-sub-service-form service-form-{{$service_id}}-{{$service_type->unique_id}}" action="{{ baseUrl('my-services/add-sub-service-types/'.$service_id) }}" method="post">
                @csrf
                <div class="cds-ty-dashboard-box">
                    <div class="cds-ty-dashboard-box-header">
                    </div>
                    <div class="cds-ty-dashboard-box-body">

                        <div class="row">
                            
                            <div class="col-xl-6">
                                <label>Professional Fees</label>
                            </div>
                            <div class="col-xl-6">
                                <input type="hidden" name="sub_services_type_id" value="{{$service_type->unique_id}}">
                                <input type="hidden" name="professional_sub_service_id" value="0">
                                <input type="hidden" class="service-form-submitted" value="0">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    @php
                                        $professional_fees_class = "professional_fees professional-fees{$service_id}-{$service_type->unique_id}";
                                    @endphp
                                    <div class="cds-fees">
                                        {!! FormHelper::formInputText([
                                        'name'=>"professional_fees",
                                        'id'=>"professional_fees",
                                        "label"=> "Professional Fees",
                                        "input_class" => $professional_fees_class,
                                        "required"=>true,
                                        'events'=>['oninput=validateNumber(this)']
                                        ])!!}
                                    </div>
                                    <div class="cds-tbd">
                                        <label>To be decided later</label><br>
                                        <label class="CDSMainsite-switch">
                                        <input type="checkbox" name="tbd" value="1" class="cds-tbd-checkbox" data-id="{{$service_type->unique_id}}" data-service="{{$service_id}}">
                                            <span class="CDSMainsite-switch-button-slider CDSMainsite-round"></span>
                                        </label>
                                    </div>
                                </div>
                            
                                <div class="cds-price-range cds-price-range-{{$service_id}}-{{$service_type->unique_id}}" style="display:none">
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <div class="cds-fees">
                                            @php
                                                $inputClass = "min_fees min-fees-{$service_id}-{$service_type->unique_id}";
                                            @endphp
                                            {!! FormHelper::formInputText([
                                            'name'=>"minimum_fees",
                                            'id'=>"min_fees",
                                            'input_class' => $inputClass,
                                            "label"=> "Min Fees",
                                            "disabled" => 'disabled',
                                            "min" => $record->subServices->minimum_fees,
                                            'events'=>['oninput=validateNumber(this)']
                                            ])!!}
                                        </div>
                                        <div class="cds-fees">
                                            {!! FormHelper::formInputText([
                                            'name'=>"maximum_fees",
                                            'id'=>"max_fees",
                                            'input_class' => "max_fees",
                                            "label"=> "Max Fees",
                                            'events'=>['oninput=validateNumber(this)']
                                            ])!!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-6">
                                <label>Consultancy Fees</label>
                                <div>If you add fees as 0 the consultation will be free</div>
                            </div>

                            <div class="col-xl-6">
                                {!! FormHelper::formInputText([
                                'name'=>"consultancy_fees",
                                'id'=>"consultancy_fees",
                                "label"=> "Consultancy Fees",
                                "required"=>true,
                                'events'=>['oninput=validatePhoneNumber(this)']
                                ])!!}
                            </div>
                            
                            
                        </div>
                        <div class="text-end button-div">
                            <button class="btn add-CdsTYButton-btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div id="add-additional-detail" class="add-tab-content add-additional-detail" style="display: none;">
            <form class="form-control add-additional-sub-service-form" action="{{ baseUrl('my-services/add-sub-service-types/'.$service_id) }}" method="post">
                @csrf
                <div class="row">
                    <input type="hidden" name="professional_sub_service_id" class="professional_sub_service_id">
                    <input type="hidden" name="sub_services_type_id" value="{{$service_type->unique_id}}">
                    <div class="col-xl-12">
                            {!! FormHelper::formTextarea([
                                'name'=>"description",
                                'id'=>"description",
                                "label"=> "Description",
                                "input_class" => "description",
                                "required"=>false,
                            ])!!}
                        </div>
                    <div class="col-xl-12">
                            <label>Assesment Form</label>
                            <div>If you add fees as 0 the consultation will be free</div>
                        </div>
                        <div class="col-xl-12">
                            @if($forms->isEmpty())
                                        <span class="text-danger">You don't have any form to add generate</span><a href="{{baseUrl('my-services/generate-assessment/'.$service_id)}}">Click here</a>
                                    @else
                            <div class="cds-assessment-list">
                                @foreach($forms as $form)
                                    <div class="cds-assessment-row">
                                        <div class="cds-assessment-col">
                                            <div class="cds-form-container mb-2 js-form-message">
                                                <div class="radio-group ">
                                                    <div class="form-check">
                                                        <input type="radio" name="form_id" id="form-id-{{ $form->id }}-{{$service_type->unique_id}}" value="{{ $form->id }}" class="radio-input required">
                                                        <label for="form-id-{{ $form->id }}-{{$service_type->unique_id}}"> {{ $form->name }} </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cds-assessment-col">
                                            <a class="btn btn-sm btn-primary" target="_blank" href="{{ baseUrl('my-services/view-assesment/'.$form->unique_id) }}">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                            
                        </div>
                        <div class="col-xl-12">
                            <label>Select Documents</label>
                        </div>
                        <div class="col-xl-12">
                            @if($documents->isEmpty())
                                <span class="text-danger">You don't have any document to add</span> <a href="{{baseUrl('document-folders/add')}}">Click here</a>
                            @else
                                <div class="multi-selectbox">
                                    {!! FormHelper::formSelect([
                                        'name' => 'document[]',
                                        'label' => 'Select Document Folder',
                                        'select_class' => 'select2-input cds-multiselect add-multi',
                                        'id' => 'documents-folders',
                                        'options' => $documents,
                                        'value_column' => 'id',
                                        'label_column' => 'name',
                                        'is_multiple' => true,
                                        'required' => false,
                                    ]) !!}
                                </div>
                            @endif
                        </div>
                
                    </div>
                <div class="col-xl-12">
                    <button class="btn add-CdsTYButton-btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end slider -->
<script>

    $('.add-tab-link').on('click', function (e) {
        e.preventDefault();

        // Remove active classes
        $('.add-tab-link').removeClass('cds-active');
        $('.status-tabs li').removeClass('cds-active');
        $('.add-tab-content').hide();

        // Add active class to clicked tab and show corresponding content
        $(this).addClass('cds-active');
        $(this).closest('li').addClass('cds-active');

        const target = $(this).data('tab');
        $('.' + target).show();
    });

     $('.openSubServicesSlideBtn').on('click', function () {
        var serviceTypeId = $(this).data('servicetypeid');
        var serviceId = $(this).data('serviceid');

        // var formSubmitted = $(this).find('.service-form-'+serviceId+'-'+serviceTypeId).val();
        
        // if(formSubmitted == 1){
        //     $('.SubServicesSlideView').addClass('active');
        // }else{
        //     errorMessage('Please fill the form and save after that add additional details');
        // }
        $('.SubServicesSlideView').addClass('active');
    });

    $('.closeSubServicesSlideBtn').on('click', function () {
        $('.SubServicesSlideView').removeClass('active');
    });

    $(".cds-tbd-checkbox").change(function(){
        var typeid =  $(this).data('id');
        var serviceid = $(this).data('service');
        if($(this).is(":checked")){
            $(".professional-fees"+serviceid+"-"+typeid).attr("disabled","disabled");
            $(".professional-fees"+serviceid+"-"+typeid).removeClass("required");
            $(".min-fees-"+serviceid+"-"+typeid).removeAttr("disabled");
            // $(".min_fees").removeAttr("disabled");
            $(".cds-price-range-"+serviceid+"-"+typeid).show();
        }else{
            $(".professional-fees"+serviceid+"-"+typeid).removeAttr("disabled");
            $(".professional-fees"+serviceid+"-"+typeid).addClass("required");
            // $(".min_fees").attr("disabled","disabled");
            $(".min-fees-"+serviceid+"-"+typeid).attr("disabled","disabled");
            $(".cds-price-range-"+serviceid+"-"+typeid).hide();
        }
    });

     $(document).on("submit", ".add-sub-service-form", function(e) {
        e.preventDefault();

        var $form = $(this); // Get the current form
        var formData = $form.serialize(); // Serialize current form data
        var actionUrl = $form.attr("action"); // Get action URL from the form
       
        $.ajax({
            url: actionUrl,
            type: "post",
            data: formData,
            dataType: "json",
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response.status == true) {
                    successMessage(response.message);
                    $('.service-form-'+response.service_id+'-'+response.sub_services_type_id).find('.service-form-submitted').val(1);
                    $(".professional_sub_service_id").val(response.professional_sub_service_id);
                    $('.additional-detail-li .add-tab-link').removeClass('disabled-link');

                    // $('.SubServicesSlideView').addClass('active');
                    // location.reload(); // You can replace this with logic to update DOM if needed
                } else {
                    validation(response.message);
                }
            },
            error: function() {
                hideLoader();
                internalError();
            }
        });
    });

    $(document).on("submit", ".add-additional-sub-service-form", function(e) {
        e.preventDefault();

        var $form = $(this); // Get the current form
        var formData = $form.serialize(); // Serialize current form data
        var actionUrl = $form.attr("action"); // Get action URL from the form
       
        $.ajax({
            url: actionUrl,
            type: "post",
            data: formData,
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
                    validation(response.message);
                }
            },
            error: function() {
                hideLoader();
                internalError();
            }
        });
    });
    
</script>
