

<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise pt-4">
    <h3>{{ $pageTitle }}</h3>
    <form id="company-detail-form" class="js-validate" action="{{ baseUrl('/companies/save-company') }}" method="post">
        @csrf
        <div class="cds-t25n-content-professional-profile-container-top mt-0">
            <div class="cds-professional-banner">
                <div id="company-banner" class="cds-t25n-content-professional-profile-container-top-banner cds-professional-responsive-banner-bg" data-bg="{{ url('assets/images/c-profile-bg.jpg') }}"> </div>
                <a onclick="showPopup('<?php echo baseUrl('/companies/crop-company-banner-image/0') ?>')" href="javascript:;" class="cds-edit-profile-banner"><i class="fa fa-edit"></i> Edit</a>
            </div> 
            <div class="cds-t25n-content-professional-profile-container-top-professional-details">
                <div class="cds-t25n-content-professional-profile-container-top-professional-details-header">
                    <div class="cds-t25n-content-professional-profile-container-top-professional-image ">
                        <img id="showCompanyLogo" class="img-fluid cdsImg" src="{{ url('assets/images/c-profile-bg.jpg') }}" alt="Profile Image">
                        <a onclick="showPopup('<?php echo baseUrl('/companies/crop-company-logo/0') ?>')" href="javascript:;" class="cds-edit-profile-image"><i class="fa fa-edit"></i></a>
                    </div>                                
                </div>
            </div>
        </div>
        <div class="row">            
            <div class="col-xl-6 col-md-12 col-lg-12 col-sm-12">
                {!! FormHelper::formInputText([
                    'name'=>"company_name",
                    'id'=>"name",
                    "label"=> "Company name",
                    "required"=>true,
                ])!!}
            </div>
        
            <div class="col-xl-6 col-md-12 col-lg-12 col-sm-12">
                <div class="cds-selectbox">
                    {!! FormHelper::formSelect([
                        'name' => 'company_type',
                        'label' => 'Company Type',
                        'class' => 'select2-input',
                        'id' => 'company_type',
                        'options' => FormHelper::ownerCompanyType(),
                        'value_column' => 'value',
                        'label_column' => 'label',
                        'is_multiple' => false,
                        'required' => true,
                    ]) !!}
                </div>  
            </div>  
            
            <div class="col-md-12">
                <div class="form-check form-check-inline cds-gender-list me-0 p-0"> 
                    <label class="mb-1">Ownership Type <span class="text-danger">*</span></label>
                    {!! FormHelper::formRadio([
                        'name' => 'owner_type',
                        'options' => [
                            ['value' => 'Self Employed', 'label' => 'Self Employed'],
                            ['value' => 'Employed', 'label' => 'Employed']
                        ],
                        'value_column' => 'value',
                        'label_column' => 'label',
                        'id' => 'label',
                        'value' => 'label',                        
                    ]) !!}                                
                </div>
            </div>
            <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                {!! FormHelper::formTextarea([
                    'name'=>"about_company",
                    'id'=>"about",
                    'class' => 'bgcolor',
                    "required"=>true,
                    "label"=>"About Company",
                ]) !!}
            </div>
            
        </div>
        
        <div class="text-end mt-4">
            <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
        </div>
    </form>
</div>

@push("scripts")
<script>
    var company_address_id = '';
    $(document).ready(function(){
        initGoogleAddress();
        $("#company-detail-form").submit(function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#company-detail-form").attr('action');
           
            var is_valid = formValidation("company-detail-form");
          
            if (!is_valid) {
                return false;
            }
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
                        validation(response.message);
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
    })
   
</script>
@endpush