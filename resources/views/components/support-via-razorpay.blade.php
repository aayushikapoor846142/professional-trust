<div class=" w-100">
    <div class="wizard">
        <ul class="cdsTYMainsite-support-us-tab">
            <li class="nav-item">
                <div class="cdsTYMainsite-support-progress complete-step">
                    <span class="cdsTYMainsite-support-progress-indicator">
                        <i class="fa-duotone fa-solid fa-badge-check fa-2x"></i>
                    </span>
                </div>
                <a class="cdsTYMainsite-support-us-nav-link active" data-step="1">Select<span>Amount</span>
                </a>

            </li>
            <li class="nav-item">
                <div class="text-end complete-step">
                    <span class="text-success">
                        <i class="fa-duotone fa-solid fa-badge-check fa-2x"></i>
                    </span>
                </div>
                <a class="cdsTYMainsite-support-us-nav-link" data-step="2">Supporter's<span>Information</span></a>

            </li>
            <li class="nav-item">
                <div class="text-end complete-step">
                    <span class="text-success">
                        <i class="fa-duotone fa-solid fa-badge-check fa-2x"></i>
                    </span>
                </div>
                <a class="cdsTYMainsite-support-us-nav-link" data-step="3">Details<span>Payment</span></a>

            </li>
        </ul>

        <div class="tab-content support-tab-content" id="myTabContent">
            <div class="tab-pane fade show active " role="tabpanel" id="step1" aria-labelledby="step1-tab">

                <div class="cds-t35-content-support-form-segments card-detail inner-data">

                    <div id="card-detail-error" class="text-danger"></div>
                    <div class="cdsTYMainsite-support-header-outer">
                        <div class="cds-t35-content-support-form-segments-header cdsTYMainsite-support-segments-header">
                            <span class="step-information"> Step 1</span>
                            <h2 class="content-heading">

                                Please Choose Your Support Amount
                            </h2>
                            <p>
                                Pay any amount you wish—for each dollar, earns points. Collect
                                enough points to unlock a badge. <b>Members can redeem points
                                for future services.</b>
                            </p>
                        </div>
                    </div>
                    <div class="cds-t35-content-support-form-segments-body">
                        <div class="text-success">
                        </div>
                        <div class="cds-payment-type">
                            {!! FormHelper::formRadio([
                                'name' => 'payment_type',
                                'class' => 'cds-payment-type-options',
                                'required' => true,
                                'options' => [
                                    ['value' => 'One Time', 'label' => 'One Time'],
                                    ['value' => 'Monthly', 'label' => 'Monthly'],
                                ],
                                'value_column' => 'value',
                                'label_column' => 'label',
                                'selected' => 'One Time'
                            ]) !!}
                        </div>
                        <div class="cds-t35-content-support-form-segments-body-amount-list-wrap">
                            @foreach(supportAmount('CAD') as $amount)
                            <div class="cds-t35-content-support-form-segments-body-amount-list js-form-message">
                                <input type="radio" id="amount{{round($amount)}}" name="amount" value="{{round($amount)}}"
                                    class="donation-option support-amount">
                                <label for="amount{{round($amount)}}" class="donation-label">{{ currencySymbol('CAD') }}{{round($amount)}}</label>
                            </div>
                            @endforeach
                        </div>
                        <div class="cds-t35-content-support-form-segments-body-other-amount-list-wrap">

                            <div class="cds-t35-content-support-form-segments-body-other-amount-list-wrap-list cds-enteramount">
                                <div class="input-group" id="customAmountGroup">
                                    <span class="input-group-text mb-0 d-flex">$</span>
                                    <input type="text" class="form-control" id="customAmount" placeholder="Amount" name="custom_amount" min="20" max="10000" oninput="validateSupportAmount(this)">
                                    <span class="input-group-text mb-0 d-flex">CAD</span>
                                </div>
                            </div>
                        </div><div class="cds-t35-content-support-form-range-value"><span><span>Minimum amount:</span> $20</span> </div>
                        
                        <p class="point-earns"></p>
                    </div>
                    <div class="cds-t35-content-support-form-segments-footer">
                        <div class="cds-t35-content-support-form-segments-footer-btn-box"><div></div>
                            <button type="button" onclick="validateForm('step1')"
                                class="CDSTy-framework-btn-primary CDSTy-framework-btn-small next step-1-CdsTYButton-btn-primary">Continue
                                <i class="fas fa-angle-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade donation-step2 disabled-form " role="tabpanel" id="step2"
                aria-labelledby="step2-tab">


                <div class="cds-t35-content-support-form-segments card-detail  inner-data ">
                    <div class="cdsTYMainsite-support-header-outer">
                        <div class="cds-t35-content-support-form-segments-header cdsTYMainsite-support-segments-header">
                            <span class="step-information">Step 2</span>
                            <h2>

                                Provide Supporter's Information
                            </h2>
                            <p> Thank You for selecting! Every dollar contributed will bring
                                a
                                positive change. </p>
                        </div>
                        <div class="cdsTYMainsite-support-amount">
                            <div class="amount-paid-badge"></div><span class="payment-type"></span>
                        </div>
                    </div>

                    <div class="cdsTYMainsite-support-auth-segments">

                        @if(!auth()->check())

                        <div class="cdsTYMainsite-support-auth-segments-login-wrap">

                            <div class="cdsTYMainsite-support-auth-segments-login-account">
                                <span>Already have an account? </span><a
                                    href="{{ url('login?redirect_back='.url()->current()) }}">Click
                                    To Login</a>


                            </div>
                            <div class="cdsTYMainsite-support-auth-segments-signup">
                                <span>Need an account?</span> <a
                                    href="{{ url('registration/supporter?redirect_back='.url()->current()) }}">Click
                                    To Sign up</a>
                            </div>
                        </div>

                        @endif
                    </div>
                    <div class="cds-t35-content-support-form-segments-body">
                        <div class="row google-address-area">
                            <div class="col-xl-12">
                                <div class="form-check form-check-inline donation-type mb-4">
                                    <label class="form-label">How can we identify you? <span class="required-asterisk">*</span></label>
                                        {!! FormHelper::formRadio([
                                        'name' => 'donation_type',
                                        'required' => true,
                                        'options' => [
                                            ['value' => 'personal', 'label' => 'Personal'],
                                            ['value' => 'corporate', 'label' => 'Corporate'],
                                        ],
                                        'value_column' => 'value',
                                        'label_column' => 'label',
                                        'checked' => 'personal'
                                        ]) !!}
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="corporate-name" style="display:none">
                                    {!! FormHelper::formInputText([
                                    "label"=>"Company Name",
                                    'name'=>"company_name",
                                    'id'=>"company-name",
                                    'disabled'=>true,
                                    ]) !!}
                                </div>
                            </div>
                            {{-- Name --}}
                            <div class="col-xl-6 col-md-6 col-lg-6">
                                {!! FormHelper::formInputText([
                                "label"=>"First Name",
                                'name'=>"first_name",
                                'id'=>"first_name",
                                'value'=>$user->first_name??'',
                                'required'=>true,
                                'events'=>['oninput=validateName(this)'],
                                ]) !!}
                            </div>
                            <div class="col-xl-6 col-md-6 col-lg-6">
                                {!! FormHelper::formInputText([
                                "label"=>"Last Name",
                                'name'=>"last_name",
                                'id'=>"last_name",
                                'value'=>$user->last_name??'',
                                'required'=>true,
                                'events'=>['oninput=validateName(this)'],
                                ]) !!}
                            </div>
                            {{-- Mailing Address --}}
                            <div class="col-xl-12">
                                {!! FormHelper::formInputText([
                                "label"=>"Address Line 1",
                                'name'=>"address",
                                'id'=>"address",
                                'value'=>$record->address??'',
                                'input_class'=>"google-address address",
                                'required'=>true,
                                ]) !!}
                            </div>
                            <div class="col-xl-6 col-md-6 col-lg-6">
                                {!! FormHelper::formInputText([
                                "label"=>"City",
                                'name'=>"city",
                                'id'=>"city",
                                'value'=>$record->city??'',
                                'input_class'=>"ga-city city",
                                'required'=>true,
                                'events'=>['oninput=validateName(this)'],
                                ]) !!}
                            </div>
                            <div class="col-xl-6 col-md-6 col-lg-6">
                                {!! FormHelper::formInputText([
                                "label"=>"State/Province",
                                'name'=>"state",
                                'id'=>"state",
                                'value'=>$record->state??'',
                                'input_class'=>"ga-state state",
                                'required'=>true,
                                'events'=>['oninput=validateName(this)'],
                                ]) !!}
                              
                            </div>

                            <div class="col-xl-6 col-md-6 col-lg-6">
                                {!! FormHelper::formInputText([
                                "label"=>"Country",
                                'name'=>"country",
                                'id'=>"country",
                                'value'=>$record->country??'',
                                'input_class'=>"ga-country country",
                                'required'=>true,
                                'events'=>['oninput=validateName(this)'],
                                ]) !!}
                            </div>
                            <div class="col-xl-6 col-md-6 col-lg-6">
                                {!! FormHelper::formInputText([
                                "label"=>"Postal Code",
                                'name'=>"zip",
                                'id'=>"zip",
                                'value'=>$record->zip??'',
                                'input_class'=>"ga-pincode zip",
                                'required'=>true,
                                'events'=>['oninput=validateZipCode(this)'],
                                ]) !!}
                            </div>
                            {{-- Email --}}
                            <div class="col-xl-12">
                                {!! FormHelper::formInputEmail([
                                "label"=>"Email",
                                'name'=>"email",
                                'id'=>"email",
                                'readonly'=>$user->email??false,
                                'default_value'=>$user->email??'',
                                'required'=>true,
                                ]) !!}
                              
                            </div>
                            {{-- Phone Number --}}
                            <div class="col-xl-12">
                                {!! FormHelper::formPhoneNo([
                                "label" => "Phone Number (optional)",
                                'name' => "phone",
                                'id' => "phone",
                                'class' => "phone_no",
                                'country_code_name' => "country_code",
                                "value" => $record->phone_no ?? '',
                                "default_country_code"=>$record->country_code ?? '+1',
                                'events'=>['oninput=validatePhoneInput(this)']]
                                ) !!}
                            </div>
                        </div>
                    </div>
                    <div class="cds-t35-content-support-form-segments-footer">
                        <div class="cds-t35-content-support-form-segments-footer-btn-box">
                            <button type="button" onclick="previousStep(1)" class="CDSTy-framework-btn-secondary CDSTy-framework-btn-small prev CdsTYButton-btn-primary"><i class="fas fa-angle-left"></i> Previous </button>
                            <button type="button" onclick="validateForm('step2')" class="CDSTy-framework-btn-primary CDSTy-framework-btn-small next CdsTYButton-btn-primary">Continue<i class="fas fa-angle-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade disabled-form " role="tabpanel" id="step3" aria-labelledby="step3-tab">
                <div class="cds-t35-content-support-form-segments card-detail" id="card-detail">
                    <div class="cdsTYMainsite-support-header-outer">
                        <div class="cds-t35-content-support-form-segments-header cdsTYMainsite-support-segments-header">
                            <span>Step 3</span>
                            <h2 class="content-heading">

                                Payment Information
                            </h2>
                            <p>We provide 256 bit encryption form.</p>
                        </div>
                    </div>
                    <div class="cds-t35-content-support-form-segments-body">
                        <div class="cds-t35-content-support-form-segments-body-total">
                            <div class="cds-t35-content-support-form-segments-body-total-header">
                                <h5>Total <div><span class="amount-paid"></span> <span class="payment-type"></span></div> </h5>
                                
                                @if(siteSetting('support_tax') > 0)
                                <span class="support-tax">(includes
                                    {{ siteSetting('support_tax') }}% GST)</span>
                                @endif

                            </div>
                            <div class="cds-t35-content-support-form-segments-body-total-footer">
                                <ul class="cds-t35-content-support-form-segments-body-total-footer-wrap">
                                    <li><img src="{{url('assets/frontend/images/cards/visa.png') }}" alt=""
                                            class="Visa Icon" /></li>
                                    <li><img src="{{url('assets/frontend/images/cards/master.png') }}" alt=""
                                            class="Mastercard Icon" /></li>
                                    <li><img src="{{url('assets/frontend/images/cards/discover.png') }}" alt=""
                                            class="Discover Icon" /></li>
                                    <li><img src="{{url('assets/frontend/images/cards/diner.png') }}" alt=""
                                            class="Diner Icon" /></li>
                                    <li><img src="{{url('assets/frontend/images/cards/jcb.png') }}" alt=""
                                            class="JCB Icon" /></li>
                                </ul>
                            </div>
                        </div>
                        
                        {{-- Used to display form errors. --}}
                        {{-- I Accept Term --}}
                        <div class="row">
                            <div class="cds-t35-content-support-payment-accept-terms">
                                <div class="cdsTYMainsite-support-accept-term">
                                    {!! FormHelper::formCheckbox(['name' =>
                                    'terms_condition',
                                    'value' => 1, 'checkbox_class' => 'termsCheckbox',
                                    'required' => true, 'checked' => false]) !!}
                                    <label class="custom-control-label" for="flexCheckChecked">By submitting this
                                        form, you
                                        accept our
                                        <a href="{{ url('page/terms-conditions') }}" class="" target="_blank">Terms
                                            of
                                            Service</a>
                                        and
                                        <a href="{{ url('page/privacy-policy') }}" class="" target="_blank">Privacy
                                            Policy</a>.
                                    </label>
                                </div>
                            </div>
                            <div class="js-form-message grecaptcha">
                                <div class="google-recaptcha"></div>
                            </div>
                        </div>
                       
                    </div> <div class="cds-t35-content-support-form-segments-footer">
                            <div class="js-form-message">
                                <input type="hidden" value="" name="amount_to_pay" id="amount-to-pay" />
                            </div>
                            <div class="cds-t35-content-support-form-segments-footer-btn-box">

                                <button type="button" onclick="previousStep(2)" class="CDSTy-framework-btn-secondary CDSTy-framework-btn-small prev CdsTYButton-btn-primary"><i class="fas fa-angle-left"></i>Previous</button>
                                <button type="button" onclick="validateForm('step3')" id="submit-btn"
                                    class="CDSTy-framework-btn-primary CDSTy-framework-btn-small submit-CdsTYButton-btn-primary" >Submit</button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var paymentMethod = '';
    var supportTax = {{ siteSetting('support_tax') }};
    $(document).ready(function () {
        googleRecaptcha();
        
        $(".payment-type").html($('input[name="payment_type"]:checked').val());
        $('input[name="payment_type"]').change(function(){
            $(".payment-type").html($(this).val());
        });
        $('input[name="amount"]').change(function () {
            $(".step-1-btn").find("span").html("Update");
            $("#amount-to-pay").val($(this).val());
            $("#customAmount").val($(this).val());
            checkEarnPoint($(this).val());
           

        });
        $('input[name="donation_type"]').change(function () {
            if ($(this).val() != 'corporate') {
                $(".corporate-name").hide();
                $(".corporate-name").find("input").attr("disabled", "disabled");
                $(".corporate-name").find("input").removeAttr("required");

            } else {
                $(".corporate-name").show();
                $(".corporate-name").find("input").removeAttr("disabled");
                $(".corporate-name").find("input").attr("required", true);
            }
        });
        $(document).on("blur", "#customAmount", function () {
            if($(this).val() < 20){
                errorMessage("Amount should be minimum $20");
                $(this).val(20);
                $(this).focus();
                
            }
            if($(this).val() > 50000){
                errorMessage("Amount should less then $50000");
                $(this).val(50000);
                $(this).focus();
            }
            
        });
        $(document).on("keyup", "#customAmount", function () {
           
            $("#amount-to-pay").val($(this).val());
            $("#customAmount").val($(this).val());

            $(".support-amount").each(function() {
                if ($(this).val() === $("#customAmount").val()) {
                    $(this).prop("checked", true); // Check the radio button if value matches
                } else {
                    $(this).prop("checked", false); // Uncheck if it does not match
                }
            });
            if(parseInt($(this).val()) >= 20 && parseInt($(this).val()) <= 50000){
                checkEarnPoint($(this).val());
            }else{
               
            }
           
        });


    })

    // billing address
    $(document).on('change', '.sameAsMailing', function () {
        if ($(this).is(':checked')) {
            $('.billing_address').val($('.address').val());
            // alert($('.city').val());
            // alert($('.state').val());
            // alert($('.zip').val());
            // alert($('.country').val());
            $('.billing_city').val($('.city').val());
            $('.billing_state').val($('.state').val());
            $('.billing_zip').val($('.zip').val());
            $('.billing_country').val($('.country').val());

        } else {
            $('.billing_address').val('');
            $('.billing_city').val('');
            $('.billing_state').val('');
            $('.billing_zip').val('');
            $('.billing_country').val('');
        }
    });
    var form = document.getElementById('payment-form');
    var submitButton = document.getElementById('submit-btn');
   
    function previousStep(step){
        var cur_step = parseInt(step) + 1;
        $(".tab-pane").removeClass("show active");
        $("#step"+step).addClass("show active");
        $("#step"+step).removeClass("step-done");
        $(".soi-step-tab .nav-link[data-step="+cur_step+"]").removeClass("active");
        $("html, body").scrollTop($("#myTabContent").offset().top);

    }
    function validateForm(step) {
        var activeTab = $('.nav-tabs .active');
        var nextTab = activeTab.parent().next().find('a.nav-link');
       
        if (step == 'step1') {
            $("#step1").removeClass("step-done");
            if($("#amount-to-pay").val() == ''){
                errorMessage("Select the amount");
            }else{
                $("#step1").removeClass("show active");
                $("#step2").removeClass("disabled-form");
                $("#step2").addClass("show active");
                $("#amount-to-pay").val($("#customAmount").val());
                var taxAmount =  ($("#amount-to-pay").val() * supportTax) / 100;
                var TotalAmount = parseFloat($("#amount-to-pay").val()) + parseFloat(taxAmount);
                $(".amount-paid").html("{{ currencySymbol('CAD') }}" + TotalAmount);
                $(".amount-paid-badge").html("{{ currencySymbol('CAD') }}" + TotalAmount +" <span>(includes "+supportTax+"% GST)</span>");
                $("html, body").scrollTop($("#myTabContent").offset().top);
                $("#step1").addClass("step-done");
                $(".cdsTYMainsite-support-us-tab .cdsTYMainsite-support-us-nav-link[data-step=1]").removeClass("active");
                $(".cdsTYMainsite-support-us-tab .cdsTYMainsite-support-us-nav-link[data-step=1]").parents('.nav-item') .addClass("nav-step-done");
                $(".cdsTYMainsite-support-us-tab .cdsTYMainsite-support-us-nav-link[data-step=2]").addClass("active");
            }
            
            
        } else if (step == 'step2') {
            $("#step2").removeClass("step-done");
            if(validation){
                var is_valid = formValidation("step2");
                if (!$('input[name="donation_type"]:checked').val()) {
                    $('.donation-type').find(".required-error").remove();
                    var errmmsg = '<div class="required-error text-danger">This field is required</div>';
                    $('.donation-type').append(errmmsg);
                    is_valid = false;
                }
                if (!is_valid) {
                    return false;
                }
            }
            $("#step2").removeClass("show active");
            $("#step3").removeClass("disabled-form");
            $("#step3").addClass("show active");
            $("html, body").scrollTop($("#myTabContent").offset().top);
            // $('html,body').animate({
            //         scrollTop: $("#step3").offset().top
            //     },
            //     'slow');
            $("#step2").addClass("step-done");

            $(".cdsTYMainsite-support-us-tab .cdsTYMainsite-support-us-nav-link[data-step=2]").removeClass("active");
            $(".cdsTYMainsite-support-us-tab .cdsTYMainsite-support-us-nav-link[data-step=2]").parents('.nav-item').addClass("nav-step-done");
            $(".cdsTYMainsite-support-us-tab .cdsTYMainsite-support-us-nav-link[data-step=3]").addClass("active");
        } else if (step == 'step3') {
            $("#step3").removeClass("step-done");
            var is_valid = formValidation("step3");
            if (!is_valid) {
                return false;
            }
            submitPayment();
        }
    }

    function prevStep(step) {
        var activeTab = $('.nav-tabs .active');
        var prevTab = activeTab.parent().prev().find('a.cdsTYMainsite-support-us-nav-link');
        prevTab.tab('show');
    }

</script>
<script>

    async function submitPayment() {
        const recaptchaResponse = grecaptcha.getResponse();
        const paymentType = $('input[name="payment_type"]:checked').val(); // One Time or Monthly
        const isMonthly = paymentType.toLowerCase() === 'monthly';

        let is_valid = true;
        if (!recaptchaResponse) {
            is_valid = false;
            $(".grecaptcha").find(".required-error").remove();
            $(".grecaptcha").append('<div class="required-error text-danger">Please complete the CAPTCHA to proceed</div>');
        }

        if (!$(".termsCheckbox").is(":checked")) {
            is_valid = false;
            $(".cds-t35-content-support-payment-accept-terms").find(".required-error").remove();
            $(".cds-t35-content-support-payment-accept-terms").append('<div class="required-error text-danger">Terms and condition is required</div>');
        }

        if (!is_valid) return;

        if(isMonthly){
            submitSubscription();
        }else{
            submitOneTime();
        }
      

    }
    async function submitOneTime(){
        $("#submit-btn,.prev").attr("disabled", "disabled");
        $("#submit-btn").append(" <i class='fa fa-spin fa-spinner'></i>");
        var amount_to_pay = $("#customAmount").val();
        var taxAmount =  (amount_to_pay * supportTax) / 100;
        var totalAmount = parseFloat(amount_to_pay) + parseFloat(taxAmount);
        try {
            var url = "{{route('razorpay.create.support.order')}}";
            var payment_success_url = "{{route('razorpay.support.payment.success')}}";
            let response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                        amount: totalAmount,   
                        first_name:$("#first_name").val(),
                        last_name:$("#last_name").val(),
                        email:$("#email").val(),
                        address:$("#address").val(),
                        country:$("#country").val(),
                        state:$("#state").val(),
                        city:$("#city").val(),
                        pincode:$("#zip").val()
                    }) // Amount in INR
            });

            let order = await response.json();
            var options = {
                "key": "{{ apiKeys('RAZORPAY_KEY_ID') }}",
                "amount": order.amount,
                "total_amount": totalAmount,   
                "tax":supportTax,
                "sub_total":amount_to_pay,
                "currency": order.currency,
                "name": "Trustvisory",
                "description": "Paid for supporting initiatives",
                "order_id": order.order_id,
                "modal": {
                    "ondismiss": function () {
                        window.location.reload(); // ✅ reload the page
                    }
                },
                "handler": function (response) {
                    fetch(payment_success_url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            razorpay_payment_id:response.razorpay_payment_id,
                            razorpay_order_id:response.razorpay_order_id,
                            // razorpay_signature:response.razorpay_signature,
                            donation_type:$('input[name="donation_type"]:checked').val(),
                            payment_type:$('input[name="payment_type"]:checked').val(),
                            amount:order.amount,
                            first_name:$("#first_name").val(),
                            last_name:$("#last_name").val(),
                            email:$("#email").val(),
                            country_code:$("input[name=country_code]").val(),
                            phone:$("input[name=phone]").val(),
                            billing_address:$("#address").val(),
                            country:$("#country").val(),
                            state:$("#state").val(),
                            city:$("#city").val(),
                            pincode:$("#zip").val()
                        })
                    }).then(res => res.json())
                    .then(data => {
                        $("#submit-btn,.prev").removeAttr("disabled");
                        $("#submit-btn .fa").remove();
                        // $("#myTabContent").html(data.contents);
                        // $('html,body').animate({
                        //     scrollTop: $("#myTabContent").offset().top},
                        // 'slow');
                        window.location.href = '{{ url("razorpay/thankyou") }}';
                    })
                    .catch(err => {
                        $("#submit-btn,.prev").removeAttr("disabled");
                        $("#submit-btn .fa").remove();
                        console.log("Error Response:", err);
                        alert("An error occurred. Check the console for details.");
                    });
                },
                "prefill": {
                    "name": $("#first_name").val()+" "+$("#last_name").val(),
                    "email": $("#email").val(),
                    "contact": $(".country-code").val()+""+$(".phone-number").val()
                },
                "theme": {
                    "color": "#3399cc"
                }
            };

            var rzp1 = new Razorpay(options);
            rzp1.open();
        } catch (error) {
            alert("error 2");
            console.error(error);
        }
    }

    async function submitSubscription(){
        $("#submit-btn,.prev").attr("disabled", "disabled");
        $("#submit-btn").append(" <i class='fa fa-spin fa-spinner'></i>");
        var amount_to_pay = $("#customAmount").val();
        var amount_to_pay = $("#customAmount").val();
        var taxAmount =  (amount_to_pay * supportTax) / 100;
        var totalAmount = parseFloat(amount_to_pay) + parseFloat(taxAmount);
        var url = "{{route('razorpay.subscription')}}";
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                _token:"{{ csrf_token() }}",
                amount: totalAmount,
            }) // in INR
        });

        const data = await response.json();

        const options = {
            key: data.key,
            subscription_id: data.subscription_id,
            name: "Trustvisory Ltd.",
            "modal": {
                "ondismiss": function () {
                    // Action when user cancels payment
                    window.location.reload(); // ✅ reload the page
                }
            },
            handler: function (response) {
                console.log(response);
                var res = response;
                var payment_success_url = "{{route('razorpay.support.payment.success')}}";
                fetch(payment_success_url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            razorpay_payment_id:response.razorpay_payment_id,
                            razorpay_order_id:response.razorpay_order_id,
                            subscription_id:response.razorpay_subscription_id,
                            // razorpay_signature:response.razorpay_signature,
                            donation_type:$('input[name="donation_type"]:checked').val(),
                            payment_type:$('input[name="payment_type"]:checked').val(),
                            amount:totalAmount * 100,
                            first_name:$("#first_name").val(),
                            last_name:$("#last_name").val(),
                            email:$("#email").val(),
                            country_code:$("input[name=country_code]").val(),
                            phone:$("input[name=phone]").val(),
                            billing_address:$("#address").val(),
                            country:$("#country").val(),
                            state:$("#state").val(),
                            city:$("#city").val(),
                            pincode:$("#zip").val()
                        })
                    }).then(res => res.json())
                    .then(data => {
                        $("#submit-btn,.prev").removeAttr("disabled");
                        $("#submit-btn .fa").remove();
                        $("#myTabContent").html(data.contents);
                        // $('html,body').animate({
                        //     scrollTop: $("#myTabContent").offset().top},
                        // 'slow');
                        // window.location.href = '{{ url("razorpay/thankyou") }}';
                    })
                    .catch(err => {
                        $("#submit-btn,.prev").removeAttr("disabled");
                        $("#submit-btn .fa").remove();
                        console.error("Error Response:", err);
                        alert("An error occurred. Check the console for details.");
                    });
            }
        };
        const rzp = new Razorpay(options);
        rzp.open();
    }
    

    function checkEarnPoint(amount){
        $.ajax({
            url: "{{ url('check-earn-points') }}",
            type: "post",
            data: {
                _token:csrf_token,
                amount:amount,
            },
            dataType: "json",
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {
                hideLoader();
                $(".point-earns").html('');
                if(response.status){
                    var url = "<?php echo url('contribution-charts') ?>";
                    $(".point-earns").html("Contribute this amount to earn  <b>"+response.point_earns+"</b> points—accumulate points to unlock contributor <a href='javascript:;' onclick='showPopup(&apos;"+url+"&apos;)'>badges and a certificate</a> of contribution.");
                }
            },
            error: function () {
                $("#card-errors").html("Error while submitting. Try again");
                $('html,body').animate({
                        scrollTop: $("#card-errors").offset().top
                    },
                    'slow');
                $("#submit-btn,.prev").removeAttr("disabled");
                $("#submit-btn .fa").remove();
                googleRecaptcha();
            }
        });
    }
    

</script>
<script>
   
    $(document).ready(function () {
        // Handle the "same as mailing address" checkbox click event
        $('.sameAsMailing').on('click', function () {
            if ($(this).prop('checked')) {
                // Add class 'focused' to all relevant inputs
                $('.cds-samedata').each(function () {
                    $(this).addClass('focused'); // Add the 'focused' class
                });
            } else {
                // Remove class 'focused' and clear values
                $('.cds-samedata').each(function () {
                    $(this).val(''); // Clear the value
                    $(this).removeClass('focused'); // Remove the 'focused' class
                });
            }
        });
    });

</script>
@endpush