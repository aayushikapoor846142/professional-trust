    <div style="display:none" id="stripe-payment"> 
    <h5>Stripe Payment</h5>
            <div class="">
                <div class="cds-ty-dashboard-box-body">
                    <form id="payment-form" method="post" action="{{ baseUrl('pay-for-appointment-booking') }}">
                        @csrf
                        <input type="hidden" name="professional_id" value="{{$professional_id}}">
                        <input type="hidden" name="appointment_booking_id" value="{{$appointment_booking_id}}">
                        <div class="cds-t35-content-support-form-segments-body">
                            <div class="cds-t35-content-support-form-segments-body-total">
                                <div class="cds-t35-content-support-form-segments-body-total-header">
                                    <h5>Total  <span class="amount-paid"></span></h5>
                                    <span>One Time Purchase</span>
                                </div>
                                <div class="cds-t35-content-support-form-segments-body-total-footer">
                                    <ul class="cds-t35-content-support-form-segments-body-total-footer-wrap">
                                        <li><img src="{{url('assets/frontend/images/cards/visa.png') }}" alt="" class="Visa Icon" /></li>
                                        <li><img src="{{url('assets/frontend/images/cards/master.png') }}" alt="" class="Mastercard Icon" /></li>
                                        <li><img src="{{url('assets/frontend/images/cards/discover.png') }}" alt="" class="Discover Icon" /></li>
                                        <li><img src="{{url('assets/frontend/images/cards/diner.png') }}" alt="" class="Diner Icon" /></li>
                                        <li><img src="{{url('assets/frontend/images/cards/jcb.png') }}" alt="" class="JCB Icon" /></li>
                                    </ul>
                                </div>
                            </div>
                            <div id="card-errors" class="card-errors cds-t55-card-error" role="alert"> </div>
                            <div class="row mb-3">
                                <div class="col js-form-message">
                                    <label class="form-label" for="card-number">Card Number <span class="danger">*</span></label>
                                    <div id="card-number" class="StripeElement form-control"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6 js-form-message">
                                    <label class="form-label" for="card-expiry">Expiration Date <span class="danger">*</span></label>
                                    <div id="card-expiry" class="StripeElement form-control"></div>
                                </div>
                                <div class="col-6 js-form-message">
                                    <label class="form-label" for="card-cvc">CVC <span class="danger">*</span></label>
                                    <div id="card-cvc" class="StripeElement form-control"></div>
                                </div>
                            </div>                        
                            <div class="row ">
                                <div class="col-xl-6 mb-3">                                
                                    {!! FormHelper::formInputText([
                                        "label"=>"First Name",
                                        'name'=>"first_name",
                                        'id'=>"first_name",
                                        'input_class'=>"cds-samedata",
                                        'value' => auth()->user()->first_name,
                                        'required'=>true,
                                    ]) !!}
                                    <div class="js-form-message first-name-div">
                                        
                                    </div>
                                </div> 
                                  
                                <div class="col-xl-6 mb-3">                                
                                    {!! FormHelper::formInputText([
                                        "label"=>"Last Name",
                                        'name'=>"last_name",
                                        'id'=>"last_name",
                                        'input_class'=>"cds-samedata",
                                        'value' => auth()->user()->last_name,
                                        'required'=>true,
                                    ]) !!}
                                    <div class="js-form-message last-name-div">
                                        
                                    </div>
                                </div>  
                                <div class="col-xl-12 mb-3">                                
                                    {!! FormHelper::formInputText([
                                        "label"=>"Email",
                                        'name'=>"email",
                                        'id'=>"email",
                                        'input_class'=>"cds-samedata",
                                        'value' => auth()->user()->email,
                                        'required'=>true,
                                    ]) !!}
                                    <div class="js-form-message email-div">
                                        
                                    </div>
                                </div> 
                            </div>
                            <div class="row google-address-area">
                                <div class="col-xl-12 mb-3">                                
                                    {!! FormHelper::formInputText([
                                        "label"=>"Address",
                                        'name'=>"address",
                                        'id'=>"address-1",
                                        'input_class'=>"address google-address",
                                        'required'=>true,
                                    ]) !!}
                                    <div class="js-form-message address-div">
                                        
                                    </div>
                                </div>                                                    
                                <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                    {!! FormHelper::formInputText([
                                        "label"=>"City",
                                        'name'=>"city",
                                        'id'=>"city",
                                        'input_class'=>"ga-city city",
                                        'required'=>true,
                                    ]) !!}
                                    <div class="js-form-message city-div">
                                        
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                    {!! FormHelper::formInputText([
                                        "label"=>"State/Province",
                                        'name'=>"state",
                                        'id'=>"state",
                                        'input_class'=>"state cds-samedata ga-state",
                                        'required'=>true,
                                        'events'=>['oninput=validateName(this)'],
                                    ]) !!}
                                    <div class="js-form-message state-div">
                                        
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                    {!! FormHelper::formInputText([
                                        "label"=>"Postal Code",
                                        'name'=>"zip",
                                        'id'=>"zip",
                                        'input_class'=>"zip cds-samedata ga-pincode",
                                        'required'=>true,
                                        'events'=>['oninput=validateZipCode(this)'],
                                    ]) !!}
                                    <div class="js-form-message zip-div">
                                        
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                    {!! FormHelper::formInputText([
                                        "label"=>"Country",
                                        'name'=>"country",
                                        'id'=>"country",
                                        'input_class'=>"country cds-samedata ga-country",
                                        'required'=>true,
                                        'events'=>['oninput=validateName(this)'],
                                    ]) !!}
                                    <div class="js-form-message country-div">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row d-none">
                                <div class="col-xl-12">
                                    <div class="form-check d-flex gap-2 ps-0 mb-2">
                                        {!! FormHelper::formCheckbox(['name' => "same_as_mailing",'value' => 1, 'checkbox_class' => "sameAsMailing", 'required' => true]) !!}
                                        <label class="custom-control-label font16">Same as mailing address</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-none">
                            <div class="row google-address-area">
                                <div class="col-xl-12 mb-3">                                
                                    {!! FormHelper::formInputText([
                                        "label"=>"Billing address",
                                        'name'=>"billing_address",
                                        'id'=>"billing_address-1",
                                        'input_class'=>"billing_address google-address",
                                        'required'=>true,
                                    ]) !!}
                                    <div class="js-form-message billing-address-div">
                                        
                                    </div>
                                </div>                                                    
                                <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                    {!! FormHelper::formInputText([
                                        "label"=>"City",
                                        'name'=>"billing_city",
                                        'id'=>"billing_city",
                                        'input_class'=>"ga-city billing_city cds-samedata",
                                        'required'=>true,
                                    ]) !!}
                                    <div class="js-form-message billing-city-div">
                                        
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                    {!! FormHelper::formInputText([
                                        "label"=>"State/Province",
                                        'name'=>"billing_state",
                                        'id'=>"billing_state",
                                        'input_class'=>"ga-state billing_state cds-samedata",
                                        'required'=>true,
                                        'events'=>['oninput=validateName(this)'],
                                    ]) !!}
                                    <div class="js-form-message billing-state-div">
                                        
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                    {!! FormHelper::formInputText([
                                        "label"=>"Postal Code",
                                        'name'=>"billing_zip",
                                        'id'=>"billing_zip",
                                        'input_class'=>"ga-pincode billing_zip cds-samedata",
                                        'required'=>true,
                                        'events'=>['oninput=validateZipCode(this)'],
                                    ]) !!}
                                    <div class="js-form-message billing-zip-div">
                                        
                                        </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                    {!! FormHelper::formInputText([
                                        "label"=>"Country",
                                        'name'=>"billing_country",
                                        'id'=>"billing_country",
                                        'input_class'=>"ga-country billing_country cds-samedata",
                                        'required'=>true,
                                        'events'=>['oninput=validateName(this)'],
                                    ]) !!}
                                    <div class="js-form-message billing-country-div">
                                        
                                        </div>
                                </div>
                                </div>
                            </div>
                            <div class="cds-t35-content-support-payment-accept-terms">
                                <div class="d-flex gap-2 align-items-center mt-2">
                                    {!! FormHelper::formCheckbox(['name' => 'terms_condition', 'value' => 1, 'checkbox_class' => 'termsCheckbox', 'required' => true, 'checked' => false ,'id'=>'checkbox1']) !!}
                                    <label class="custom-control-label font16" for="flexCheckChecked">By submitting this form, you accept our 
                                        <a href="{{ mainTrustvisoryUrl().'/page/terms-conditions' }}" class="fs-6" target="_blank">Terms of Service</a> 
                                        and 
                                        <a href="{{ mainTrustvisoryUrl().'/page/privacy-policy' }}" class="fs-6" target="_blank">Privacy Policy</a>.
                                    </label>
                                </div>
                                <div class="js-form-message checkbox1"></div>
                            </div>
                            <div class="js-form-message grecaptcha">
                                <div class="google-recaptcha"></div>
                            </div>
                        </div>
                    </form>
                    <div class="cds-t35-content-support-form-segments-footer">
                        <div class="cds-t35-content-support-form-segments-footer-btn-box">
                            <div class="js-form-message">
                                <input type="hidden" value="{{$amount_to_pay}}" name="amount_to_pay" id="amount-to-pay" />
                            </div>
                            <button type="button" onclick="submitPayment()" disabled id="submit-btn" class="CdsTYButton-btn-primary"><span>Submit</span></button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    