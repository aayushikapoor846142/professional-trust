@extends('admin-panel.layouts.app')

@section('content')
 <div class="ch-action">
                    <a href="{{ baseUrl('my-services') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-solid fa-left me-1"></i>
                        Back
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
  <form id="form" class="js-validate mt-3" action="{{ baseUrl('my-services/save-sub-services/'.$service_id) }}" method="post">
                @csrf
                <div class="cds-ty-dashboard-box">
                   
                    <div class="cds-ty-dashboard-box-body">
                    
                            <div class="row">
                                <div class="col-xl-6">
                                    {!! FormHelper::formSelect([
                                        'name' => 'sub_services_type_id',
                                        'id' => 'sub_services_type_id',
                                        'label' => 'Type',
                                        'class' => 'select2-input ga-country',
                                        'options' => $sub_services_types,
                                        'value_column' => 'id',
                                        'label_column' => 'name',
                                        'is_multiple' => false,
                                        'required' => true,
                                    ]) !!}
                                </div>
                                <div class="col-xl-6">
                                    {!! FormHelper::formInputText([
                                        'name'=>"professional_fees",
                                        'id'=>"professional_fees",
                                        "label"=> "Professional Fees",
                                        "required"=>true,
                                        'events'=>['oninput=validatePhoneNumber(this)']
                                    ])!!}
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
                                <div class="col-xl-6">
                                    {!! FormHelper::formSelect([
                                        'name' => 'form_id',
                                        'id' => 'form_id',
                                        'label' => 'Assesment Form',
                                        'class' => 'select2-input ga-country',
                                        'options' => $forms,
                                        'value_column' => 'id',
                                        'label_column' => 'name',
                                        'is_multiple' => false,
                                        'required' => false,
                                    ]) !!}
                                </div>
                                <div class="col-xl-12">
                                    <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('my-services/add-document-folder/') ?>')">Add Document Folder</a>
                                </div>
                                <div class="col-xl-12 mt-3">
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
                                </div>
                                <div class="col-xl-12">
                                    {!! FormHelper::formTextarea([
                                        'name'=>"description",
                                        'id'=>"description",
                                        'required' => true,
                                        "label"=>"Enter Description",
                                        'class'=>"editor noval",
                                     
                                        ]) !!}
                                </div>
                                
                            </div>
                            
                            <div class="text-end button-div">
                                <button type="button" onclick="devideProfessionalFees()" class="btn add-CdsTYButton-btn-primary">Save</button>
                            </div>
                        
                    </div>
                </div>

                <div class="cds-ty-dashboard-box mt-3 divide-professional-fees" style="display:none;">
                    <div class="cds-ty-dashboard-box-header">
                    </div>
                    <div class="cds-ty-dashboard-box-body">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                {!! FormHelper::formCheckbox([
                                    'name' => 'wantto-divide-fees',
                                    'value' => 1,
                                    'id' => 'wantto-divide-fees',
                                    'required' => false,
                                    'checkbox_class' => 'wantto-divide-fees'
                                ]) !!}
                                <label class="cds-t66-radio-labels" for="do-you-more-license">What is your Payment schedule? Do you Want to installment?</label>
                            </div>
                            <div id="schedule-container" class="row mt-2 mb-2"></div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <a href="javascript:;" class="add-more CdsTYButton-btn-primary" style="display:none;">Add More Schedule</a>
                                </div>
                            </div>
                            <label class="pending-amount" id="pending-amount" style="display:none;"> You Pending Amount is 600</label>
                        </div>
                        <div class="text-end ">
                            <button type="submit" class="CdsTYButton-btn-primary">Save</button>
                        </div>
                    </div>
                
                </div>
            </form>
    
			</div>
	
	</div>
  </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-12">
              </div>
    </div>
</div>
    @endsection
<!-- End Content -->
@section('javascript')

<script>
    $(document).ready(function() {
        var scheduleCount = 0;
        // end google address
        $("#form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }

            if ($(".wantto-divide-fees").is(":checked")) {
                var isValid = true;
                var total = 0;
                $(".schedule-input").each(function () {
                    let val = parseFloat($(this).val().trim()) || 0;
                    total += val; 
                    if ($(this).val().trim() === "") {
                        isValid = false;
                        $(this).css("border", "2px solid red"); // Highlight empty fields
                    } else {
                        $(this).css("border", ""); // Reset border if filled
                    }
                });

                if (!isValid) {
                    $('.pending-amount').html("Your Pending Amount is 0 so remove extra schedule filed");
                    return false;
                }
                
                if(total > $('#professional_fees').val()){
                    $('.pending-amount').html("Your schedule is more than professional fees");
                    return false;
                }

                if(total < $('#professional_fees').val()){
                    
                    var less = $('#professional_fees').val() - total;
                    $('.pending-amount').html("Your schedule is less than professional fees. Your Pending Amount is " + less );
                    return false;
                }
            }
            

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

                        if(response.error_type == 'validation'){
                            validation(response.message);
                        }else{
                            errorMessage(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    internalError();
                                      if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
            validation(xhr.responseJSON.message);
        } else {
            errorMessage('An unexpected error occurred. Please try again.');
        }
                }
            });

        });

        $('.add-more').click(function() {

            if( $(".schedule-input").length != 0){
                
                let lastInput = $(".schedule-input").last().val().trim();
                if (lastInput === "") {
                    errorMessage("Please fill in the last price field before adding more!");
                } else {
                
                    // Increment the schedule count
                    scheduleCount++;

                    // Create a new div for the schedule input and professional fee
                    var newSchedule = `
                    <div class="col-md-12 mb-3" id="schedule-${scheduleCount}">
                        <label>Schedule ${scheduleCount}</label>
                        <input type="text" name="schedule[]" class="schedule-input" id="schedule-input-${scheduleCount}">
                        <button class="remove-schedule" data-id="${scheduleCount}">Remove</button>
                    </div>
                    `;

                    // Append the new schedule input to the container
                    $('#schedule-container').append(newSchedule);
                }
            }else{
                
                    // Increment the schedule count
                    scheduleCount++;

                    // Create a new div for the schedule input and professional fee
                    var newSchedule = `
                    <div class="col-md-12 mb-3" id="schedule-${scheduleCount}">
                        <label>Schedule ${scheduleCount}</label>
                        <input type="text" name="schedule[]" class="schedule-input" id="schedule-input-${scheduleCount}">
                        <button class="remove-schedule" data-id="${scheduleCount}">Remove</button>
                    </div>
                    `;

                    // Append the new schedule input to the container
                    $('#schedule-container').append(newSchedule);
            }
        });

        $(document).on("keyup", ".schedule-input", function(){
            calculatePending($(this).attr('id'));
        });

        $(document).on("keyup", "#professional_fees", function(){
           $('.schedule-input').val('');
           $('.pending-amount').html('Your Pending Amount '+$(this).val());
        });

        $('.wantto-divide-fees').change(function() {
            if ($(this).is(':checked')) {
                $('.add-more').show();
                $('.pending-amount').html("Your Pending Amount "+ $("#professional_fees").val());
                $('.pending-amount').show();
            } else {
                $('.add-more').hide();
                $('.pending-amount').hide();
            }
        });

        $(document).on('click', '.remove-schedule', function() {
            var scheduleId = $(this).data('id');
            $('#schedule-' + scheduleId).remove();
            calculatePending();
        });
    });
    
    function calculatePending(currentSchedule) {
        let total = 0;
        
                let hasEmpty = false;
              
                $(".schedule-input").each(function(){
                    let val = $(this).val().trim(); // Get value and remove extra spaces
                    
                    if (val === "") {
                        hasEmpty = true;
                    } else {
                        total += parseFloat(val) || 0;
                    }
                });

                let mainPrice = parseFloat($("#professional_fees").val()) || 0;

                // Show error if any field is empty
                // if (hasEmpty) {
                //     errorMessage("Please fill in all price fields before adding more!");
                //     return false;
                // }

                // Show error if total price exceeds main price
                if (total > mainPrice) {
                    errorMessage("Total price cannot be greater than the Main Price!");
                    $("#"+currentSchedule).val('');
                    return false;
                }else{
                    let pendingAmount = mainPrice - total;
                    $("#pending-amount").text("You Pending Amount is "+ pendingAmount); // Update pending amount
                    return true;
                }
               
               
    }

    function devideProfessionalFees()
    {
        var is_valid = formValidation("form");
        if (!is_valid) {
            return false;
        }
        $('.divide-professional-fees').show();
        $('.add-btn').hide();
        $('.button-div').remove();
     
    }
    

</script>
@endsection