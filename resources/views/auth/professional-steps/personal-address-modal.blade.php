<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="address-form" action="{{ url('/save-address-from-signup') }}" method="post">
                @csrf
                <input type="hidden" name="address_id" value="{{ $id }}" />
                <input type="hidden" name="type_label" value="personal" />
                <div class="google-address-area">
                    <div class="cds-form-group">
                        {!! FormHelper::formInputText([
                        'name' => 'address1',
                        'value' => $adddressInfo->address_1??'',
                        'label' => 'Address 1',
                        'input_class'=>"google-address",
                        'is_mark' => "yes",
                        'id' => 'personal_address',
                        ]) !!}
                    </div>
                    <div class="cds-form-group address2-div">
                        {!! FormHelper::formInputText([
                        'name' => 'address2',
                        'value' => $adddressInfo->address_2??'',
                        'label' => 'Address 2',
                        'input_class' => "ga-address2",
                        'id' => 'address_2',
                        ]) !!}
                    </div>
                    <div class="cds-form-group country-div">
                        {!! FormHelper::formSelect([
                        'name' => 'country',
                        'value' => $adddressInfo->country??'',
                        'id' => 'country',
                        'label' => 'Select Country',
                        'class' => 'select2-input ga-country',
                        'select_class' => 'ga-country',
                        'options' => $countries,
                        'value_column' => 'name',
                        'label_column' => 'name',
                        'selected' => $adddressInfo->country??'',
                        'is_multiple' => false,
                        'is_mark' => "yes",
                        ]) !!}
                    </div>
                    <div class="cds-form-group state-div">
                        {!! FormHelper::formInputText([
                        'name' => 'state',
                        'value' => $adddressInfo->state??'',
                        'label' => 'State',
                        'input_class' => 'ga-state',
                        'is_mark' => "yes",
                        'events'=>['oninput=validateName(this)']
                        ]) !!}
                    </div>
                    <div class="cds-form-group city-div">
                        {!! FormHelper::formInputText([
                        'name' => 'city',
                        'value' => $adddressInfo->city??'',
                        'label' => 'City',
                        'input_class' => 'ga-city',
                        'is_mark' => "yes",
                        'events'=>['oninput=validateName(this)']
                        ]) !!}
                    </div>
                    <div class="cds-form-group pincode-div">
                        {!! FormHelper::formInputNumber([
                        'name' => 'pincode',
                        'value' => $adddressInfo->pincode??'',
                        'label' => 'Pincode',
                        'input_class' => 'ga-pincode',
                        'is_mark' => "yes",
                        'events'=>['oninput=validateZipCode(this)', 'onblur=validateZipCode(this)']
                        ]) !!}
                    </div>
                </div>
                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
  
<script>
    $(document).ready(function(){
        initGoogleAddress();
          $("#address-form").submit(function(e) {
                e.preventDefault();
                var is_valid = formValidation("address-form");
                if(!is_valid){
                    return false;
                }
                var formData = new FormData($(this)[0]);
                var url = $("#address-form").attr('action');
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
                            loadPersonalAddress();
                            closeModal();
                        } else {
                            validation(response.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });

            });
        });
       
    
</script>