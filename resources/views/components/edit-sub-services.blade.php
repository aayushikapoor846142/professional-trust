@foreach($professionalSubService as $value)
<div class="border p-2 mt-3">
    <div class="row">
        <div class="col-md-10">{{$value->subServiceTypes->name}}</div>
        @if($value->status == "pending")
            <div class="col-md-2">
                <i class="fa fa-exclamation-circle text-danger" data-bs-toggle="tooltip" title="Please Configure details pending" style="font-size:20px;"></i>
                <a href="javascript:;" id="" class="btn btn-sm btn-primary openEditSubServicesSlideBtn" data-subserviceid="{{$value->unique_id}}">
                    Configure
                </a>
            </div>
        @endif
    </div>
    @if($value->status != "pending")
    <div>
        <b>Professional fees:</b>${{$value->professional_fees}}</br>
        @if($value->minimum_fees != '')
            <b>Minimum Fees:</b>
            ${{$value->minimum_fees}}</br>
        @endif
        @if($value->maximum_fees != '')
            <b>Maximum Fees:</b>
            ${{$value->maximum_fees}}</br>
        @endif
        <b>Consultancy fees:</b>${{$value->consultancy_fees}}</br>
        <b>Description:</b>{{$value->description}}</br>
        @if($value->form_id != '')
            <b>Assesment Form:</b>
            {{$value->forms->name}}</br>
        @endif
        @if($value->document_folders != '')
            <b>Document:</b>
            {{getServiceDocument($value->document_folders)}}
        @endif
        <a href="javascript:;" id="" class="CdsTYButton-btn-primary openEditSubServicesSlideBtn" data-subserviceid="{{$value->unique_id}}">
            Edit
        </a></br>
      
        @if(empty(checkCaseWithProfessional($value->service_id,$value->sub_services_type_id,$value->user_id)))
            <a href="javascript:;" onclick="confirmDeleteServiceType(this)" data-href="{{baseUrl('my-services/delete-sub-service-types/'.$value->unique_id)}}" class="CdsTYButton-btn-primary CdsTYButton-border-thick">Delete</a>
        @else
            <span class="text-danger">Case Connected to remove please delete the case</span>
        @endif
    </div>
    @endif



</div>
<!-- slide panel -->
<div id="EditSubServicesSlideView" class="CDSBookingsFlow-duration-slide-view EditSubServicesSlideView-{{$value->unique_id}}">
    <div class="CDSBookingsFlow-duration-slide-content">
        <h3>Additional Settings</h3>
        <span id="" class="CDSBookingsFlow-duration-close-btn closeEditSubServicesSlideBtn" data-closeid="{{$value->unique_id}}"><i class="fa-sharp fa-regular fa-xmark" aria-hidden="true"></i></span>
        <div class="cds-t25n-content-professional-profile-container-main-navigation">
            <ul class="status-tabs">
                <li class="cds-active fees-detail-li">
                    <a href="#" class="tab-link cds-active" data-tab="fees-detail">Fees Detail</a>
                </li>
                <li class="additional-detail-li">
                    <a href="#" class="tab-link" data-tab="additional-detail">Additional Detail</a>
                </li>
            </ul>
        </div>
        <div id="fees-detail" class="tab-content fees-detail">
            <form id="edit-form-{{ $value->unique_id }}" class="js-validate mt-3 edit-sub-service-form" action="{{ baseUrl('my-services/update-sub-service-types/'.$value->unique_id) }}" method="post">
                @csrf
                <input type="hidden" name="type" value="form">
                <div class="cds-ty-dashboard-box">
                    <div class="cds-ty-dashboard-box-header">
                    </div>
                    <div class="cds-ty-dashboard-box-body">

                        <div class="row">
                            
                            <div class="col-xl-6">
                                <label>Professional Fees</label>
                            </div>
                            <div class="col-xl-6">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    
                                    <div class="cds-fees">
                                        {!! FormHelper::formInputText([
                                        'name'=>"professional_fees",
                                        'id'=>"professional_fees",
                                        "label"=> "Professional Fees",
                                        "input_class" => "professional_fees",
                                        "required"=>true,
                                        "value" => $value->professional_fees ?? '',
                                        'events'=>['oninput=validateNumber(this)']
                                        ])!!}
                                    </div>
                                    <div class="cds-tbd">
                                        <label>To be decided later</label><br>
                                        <label class="CDSMainsite-switch">
                                        <input type="checkbox" name="tbd" value="1" class="cds-tbd-checkbox" {{ $value->tbd == 1 ? 'checked' : '' }}>
                                            <span class="CDSMainsite-switch-button-slider CDSMainsite-round"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="cds-price-range" style="@if($value->tbd == 0) display:none @endif">
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <div class="cds-fees">
                                            {!! FormHelper::formInputText([
                                            'name'=>"minimum_fees",
                                            'id'=>"min_fees",
                                            'input_class' => "min_fees",
                                            "label"=> "Min Fees",
                                            "disabled" => 'disabled',
                                            "value" => $value->minimum_fees ?? '',
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
                                            "value" => $value->maximum_fees ?? '',
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
                                "value" => $value->consultancy_fees ?? '',
                                'events'=>['oninput=validatePhoneNumber(this)']
                                ])!!}
                            </div>
                            {{--<div class="col-xl-12">
                                <a href="javascript:;" id="" class="CdsTYButton-btn-primary openEditSubServicesSlideBtn" data-subserviceid="{{$value->unique_id}}">
                                    <i class="fa-plus fa-solid me-1"></i>
                                    Additional Settings
                                </a>
                            </div>--}}
                        </div>
                        <div class="text-end button-div">
                            <button class="btn add-CdsTYButton-btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div id="additional-detail" class="tab-content additional-detail" style="display: none;">
            <form class="form-control edit-sub-service-form" action="{{ baseUrl('my-services/update-sub-service-types/'.$value->unique_id) }}" method="post">
                @csrf
                <input type="hidden" name="type" value="additional-detail">
                <div class="row">
                    <div class="col-xl-12">
                            {!! FormHelper::formTextarea([
                                'name'=>"description",
                                'id'=>"description",
                                "label"=> "Description",
                                "input_class" => "description",
                                "value" => $value->description ?? '',
                                "required"=>false,
                            ])!!}
                        </div>
                    <div class="col-xl-12">
                            <label>Assesment Form</label>
                            <div>If you add fees as 0 the consultation will be free</div>
                        </div>
                        <div class="col-xl-12">
                            <div class="cds-assessment-list">
                                @if($forms->isEmpty())
                                    <span class="text-danger">You don't have any form to add generate</span><a href="javascript:;">Click here</a>
                                @else
                                    @foreach($forms as $form)
                                        <div class="cds-assessment-row">
                                            <div class="cds-assessment-col">
                                                <div class="cds-form-container mb-2">
                                                    <div class="radio-group ">
                                                        <div class="form-check">
                                                            <input type="radio" name="form_id" id="form-id-{{ $form->id }}-{{$value->unique_id}}" value="{{ $form->id }}" class="radio-input required" {{ $value->form_id == $form->id ? 'checked' : '' }}>
                                                            <label for="form-id-{{ $form->id }}-{{$value->unique_id}}"> {{ $form->name }} </label>
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
                                @endif
                            </div>
                            
                        </div>
                        <div class="col-xl-12">
                            <label>Select Documents</label>
                        </div>
                        <div class="col-xl-12">
                            @if($documents->isEmpty())
                                <span class="text-danger">You don't have any documents to add please</span><a href="{{baseUrl('document-folders/add')}}">Click here</a>

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
                                        'selected' => explode(',',$value->document_folders) ?? [],
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
<!-- end slide panel -->
@endforeach


<script>
$('[data-bs-toggle="tooltip"]').tooltip();
    $('.tab-link').on('click', function (e) {
        e.preventDefault();

        // Remove active classes
        $('.tab-link').removeClass('cds-active');
        $('.status-tabs li').removeClass('cds-active');
        $('.tab-content').hide();

        // Add active class to clicked tab and show corresponding content
        $(this).addClass('cds-active');
        $(this).closest('li').addClass('cds-active');

        const target = $(this).data('tab');
        $('.' + target).show();
    });
$('.openEditSubServicesSlideBtn').on('click', function () {
    var subserviceid = $(this).data('subserviceid');
    let selector = '.CDSBookingsFlow-duration-slide-view:not([class*="EditSubServicesSlideView-' + subserviceid + '"])';
    $(selector).removeClass('active');
    $('.EditSubServicesSlideView-'+subserviceid).addClass('active');
});

$('.closeEditSubServicesSlideBtn').on('click', function () {
    var closeid = $(this).data('closeid');
    $('.status-tabs .fees-detail-li').addClass('cds-active');
    $('.fees-detail').show();
    $('.additional-detail').hide();
    $('.EditSubServicesSlideView-'+closeid).removeClass('active');
});


$(document).on("submit", ".edit-sub-service-form", function(e) {
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

function confirmDeleteServiceType(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to delete?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}
</script>