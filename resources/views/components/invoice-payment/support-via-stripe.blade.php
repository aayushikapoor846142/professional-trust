
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
            <div class="tab-pane fade show active border-bottom-0" role="tabpanel" id="step1" aria-labelledby="step1-tab">
                <div class="cds-t35-content-support-form-segments card-detail inner-data">

                    <div id="card-detail-error" class="text-danger"></div>
                    <div class="cdsTYMainsite-support-header-outer p-0">
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
                            @include("components.invoice-payment.invoice-detail", ['record' => $record])
                        <p class="point-earns"></p>
                    </div>
                    <div class="cds-t35-content-support-form-segments-footer">
                        <div class="cds-t35-content-support-form-segments-footer-btn-box"><div></div>
                            <button type="button" onclick="validateForm('step1')"
                                class="CDSTy-framework-btn-primary CDSTy-framework-btn-small next step-1-CdsTYButton-btn-primary">Continue
                                <i class="fas fa-angle-right ms-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade donation-step2 disabled-form border-bottom-0" role="tabpanel" id="step2"
                aria-labelledby="step2-tab">
                <div class="cds-t35-content-support-form-segments card-detail  inner-data ">
                    <div class="cdsTYMainsite-support-header-outer p-0">
                        <div class="cds-t35-content-support-form-segments-header cdsTYMainsite-support-segments-header">
                            <span class="step-information">Step 2</span>
                            <h2>
                                Provide Supporter's Information
                            </h2>
                            <p> Thank You for selecting! Every dollar contributed will bring a positive change.</p>
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
                                'value'=>$adddress->address_1??'',
                                'input_class'=>"google-address address",
                                'required'=>true,
                                ]) !!}
                            </div>
                            <div class="col-xl-6 col-md-6 col-lg-6">
                                {!! FormHelper::formInputText([
                                "label"=>"City",
                                'name'=>"city",
                                'id'=>"city",
                                'value'=>$adddress->city??'',
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
                                'value'=>$adddress->state??'',
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
                                'value'=>$adddress->country??'',
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
                                'value'=>$adddress->pincode??'',
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
                                'readonly'=>true,
                                'value'=>auth()->user()->email,
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
                            <button type="button" onclick="previousStep(1)" class="CDSTy-framework-btn-secondary CDSTy-framework-btn-small prev CdsTYButton-btn-primary me-2"><i class="fas fa-angle-left me-1"></i> Previous </button>
                            <button type="button" onclick="validateForm('step2')" class="CDSTy-framework-btn-primary CDSTy-framework-btn-small next CdsTYButton-btn-primary">Continue<i class="fas fa-angle-right ms-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade disabled-form border-bottom-0" role="tabpanel" id="step3" aria-labelledby="step3-tab">
                <div class="cds-t35-content-support-form-segments card-detail" id="card-detail">
                    <div class="cdsTYMainsite-support-header-outer p-0">
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
                        <div id="card-errors" class="card-errors cds-t55-card-error" role="alert"> </div>
                        <div class="row card-fields">
                            <div class="col-12 ">
                                <div class="cds-form-container">
                                    <div class="js-form-message">
                                        <div class="form-group form-floating">
                                            <label for="card-number">Card Number <span class="danger">*</span></label>
                                            <div id="card-number" class="StripeElement form-control"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 ">
                                <div class="cds-form-container">
                                    <div class="js-form-message">
                                        <div class="form-group form-floating">
                                            <label class="form-label" for="card-expiry">Expiration Date
                                                <span class="danger">*</span></label>
                                            <div id="card-expiry" class="StripeElement form-control"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 ">
                                <div class="cds-form-container">
                                    <div class="js-form-message">
                                        <div class="form-group form-floating">
                                            <label class="form-label" for="card-cvc">CVC
                                                <span class="danger">*</span></label>
                                            <div id="card-cvc" class="StripeElement form-control"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Used to display form errors. --}}
                        {{-- I Accept Term --}}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="powered-by-stripe">
                                    <img src="https://stripe.com/img/v3/home/twitter.png" alt="Powered by Stripe" />
                                    <span>Payments powered by Stripe</span>
                                    </div>
                                    <div class="cds-t35-content-support-payment-accept-terms">
                                        <div class="cdsTYMainsite-support-accept-term mb-2">
                                            {!! FormHelper::formCheckbox(['name' =>
                                            'is_anonymous',
                                            'value' => 'yes', 'checkbox_class' => 'termsCheckbox',
                                            'required' => true, 'checked' => false]) !!}
                                            <label class="custom-control-label font16" for="flexCheckChecked">Keep identity anonymous
                                            </label>
                                        </div>
                                        <div class="cdsTYMainsite-support-accept-term">
                                            {!! FormHelper::formCheckbox(['name' =>
                                            'terms_condition',
                                            'value' => 1, 'checkbox_class' => 'termsCheckbox',
                                            'required' => true, 'checked' => false]) !!}
                                            <label class="custom-control-label font16" for="flexCheckChecked">By submitting this
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
                        </div>
                       
                    </div> <div class="cds-t35-content-support-form-segments-footer">
                            <div class="js-form-message">
                                <input type="hidden" name="amount_to_pay" id="amount-to-pay" value="{{$record->total_amount}}"/>
                            </div>
                            <div class="cds-t35-content-support-form-segments-footer-btn-box">

                                <button type="button" onclick="previousStep(2)" class="CDSTy-framework-btn-secondary CDSTy-framework-btn-small prev CdsTYButton-btn-primary me-2"><i class="fas fa-angle-left me-1"></i>Previous</button>
                                <button type="button" onclick="validateForm('step3')" disabled id="submit-btn"
                                    class="CDSTy-framework-btn-primary CDSTy-framework-btn-small submit-CdsTYButton-btn-primary"  data-secret="{{ stripeIntent()->client_secret }}">Submit</button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="errorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Please Wait</h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div><i class="fa fa-spin fa-spinner"></i></div>
                    <p id="custom-error-message"></p>
                    <p id="countdown-text" class="text-center text-bg-warning">Redirecting in <span id="countdown">40</span> seconds...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var paymentMethod = '';
    $(document).ready(function () {
        googleRecaptcha();
        // $("#payment-form").submit(function(e){
        //     	e.preventDefault();
        //         handlePayment(paymentMethod);
        // });
        $(".payment-type").html($('input[name="payment_type"]:checked').val());
        $('input[name="payment_type"]').change(function(){
            $(".payment-type").html($(this).val());
        });
        $('input[name="amount"]').change(function () {
            $(".step-1-btn").find("span").html("Update");
            // $("#amount-to-pay").val($(this).val());
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
           
            // $("#amount-to-pay").val($(this).val());
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
         var supportTax = {{ siteSetting('support_tax') }};
        if (step == 'step1') {
            $("#step1").removeClass("step-done");
            if($("#amount-to-pay").val() == ''){
                errorMessage("Select the amount");
            }else{
                $("#step1").removeClass("show active");
                $("#step2").removeClass("disabled-form");
                $("#step2").addClass("show active");
                // $("#amount-to-pay").val($("#customAmount").val());
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
    var elements;
    var cardNumber, cardExpiry, cardCvc;
    var stripe = Stripe('{{ apiKeys('STRIPE_KEY') }}');
    initStripe();
    async function submitPayment() {
        const recaptchaResponse = grecaptcha.getResponse();
        // const paymentType = $('input[name="payment_type"]:checked').val(); // One Time or Monthly
        // const isMonthly = paymentType.toLowerCase() === 'monthly';

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

        $("#submit-btn,.prev").attr("disabled", "disabled");
        $("#submit-btn").append(" <i class='fa fa-spin fa-spinner'></i>");
       
        // STEP 1: confirm card with Stripe
        let paymentMethodId = null;
        try {
            const { setupIntent, error } = await stripe.confirmCardSetup(
                submitButton.dataset.secret,
                {
                    payment_method: {
                        card: cardNumber,
                        billing_details: {
                            name: $("#first_name").val() + " " + $("#last_name").val(),
                            email: $("#email").val(),
                            phone: $("#phone").val(),
                            address: {
                                line1: $("#address").val(),
                                city: $("#city").val(),
                                state: $("#state").val(),
                                postal_code: $("#zip").val(),
                                // country: $("#country").val(),
                            },
                        },
                    },
                }
            );

            if (error) {
                throw error;
            }

            paymentMethodId = setupIntent.payment_method;

        } catch (error) {
            $("#card-errors").html(error.message);
            
            $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
            $("#submit-btn,.prev").removeAttr("disabled");
            $("#submit-btn .fa").remove();
            return;
        }

        // STEP 2: Send to Laravel controller for actual processing
        const formData = new FormData($("#payment-form")[0]);

        formData.append("payment_method_id", paymentMethodId);
        formData.append("cardholder_name", $("#first_name").val() + " " + $("#last_name").val());
        formData.append("address_line1", $("#address").val());
        formData.append("address_line2", $("#address2").val() ?? '');
        formData.append("city", $("#city").val());
        formData.append("state", $("#state").val());
        formData.append("country", $("#country").val());
        formData.append("postal_code", $("#zip").val());
      
        //formData.append("payment_type", paymentType);

        const url = $("#payment-form").attr('action');
        showPopup("<?php echo baseUrl('invoices/processing-payment') ?>");
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: async function (response) {
                
                if (response.requires_action && response.client_secret) {
                    // const result = await stripe.confirmCardPayment(response.client_secret, {
                    //     return_url: '{{ url('stripe/callback') }}'
                    // });
                    const result = await stripe.confirmCardPayment(response.client_secret);
                    if (result.error) {
                        $("#card-errors").html(result.error.message);
                        $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
                        showErrorModal(result.error.message);
                        return;
                    } else if (result.paymentIntent.status === 'succeeded') {
                        completePaymentAction(response.payment_intent_id,response.new_user);
                        // window.location.href = response.redirect_url;
                    }
                } else if (response.status === true) {
                    completePaymentAction(response.payment_intent_id,response.new_user);
                    // window.location.href = response.redirect_url;
                } else {
                    $("#submit-btn,.prev").removeAttr("disabled");
                    $("#submit-btn .fa").remove();
                    $("#card-errors").html(response.message || "Something went wrong. Please try again.");
                    $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
                    $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
                    showErrorModal(response.message || "Something went wrong. Please try again.");
                    // setTimeout(() => {
                    //     location.reload();
                    // }, 10000);
                }
            },
            error: function (xhr) {
                console.log(xhr);
                closeModal();
                $("#card-errors").html("Error submitting payment. Please try again.");
                showErrorModal("Error submitting payment. Please try again.");
                $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
                $("#submit-btn,.prev").removeAttr("disabled");
                $("#submit-btn .fa").remove();
            }
        });
    }

    function completePaymentAction(payment_intent_id,new_user){
        const formData = new FormData($("#payment-form")[0]);
        var paymentType = $('input[name="payment_type"]:checked').val();
        formData.append("payment_intent_id", payment_intent_id);
        formData.append("cardholder_name", $("#first_name").val() + " " + $("#last_name").val());
        formData.append("address_line1", $("#address").val());
        formData.append("address_line2", $("#address2").val() ?? '');
        formData.append("city", $("#city").val());
        formData.append("state", $("#state").val());
        formData.append("country", $("#country").val());
        formData.append("postal_code", $("#zip").val());
        formData.append("payment_type", paymentType);
        formData.append("new_user", new_user);
        formData.append("invoice_id","{{$record->unique_id}}");
        const url = $("#payment-form").attr('action');

        $.ajax({
            url: "{{ baseUrl('invoices/stripe/complete-payment') }}",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: async function (response) {
                closeModal();
                $("#submit-btn,.prev").removeAttr("disabled");
                $("#submit-btn .fa").remove();
                if (response.status === true) {
                    window.location.href = response.redirect_url;
                } else {
                    $("#card-errors").html(response.message || "Something went wrong. Please try again.");
                    $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
                }
            },
            error: function (xhr) {
                closeModal();
                $("#card-errors").html("Error submitting payment. Please try again.");
                showErrorModal("Error submitting payment. Please try again.");
                $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
                $("#submit-btn,.prev").removeAttr("disabled");
                $("#submit-btn .fa").remove();
            }
        });
    }

    // function handlePayment(paymentMethodId) {
    //     var formData = new FormData($("#payment-form")[0]);
    //     formData.append("payment_method_id", paymentMethodId);
    //     formData.append("cardholder_name", $("#first_name").val() + " " + $("#last_name").val());
    //     formData.append("address_line1", $("#address").val());
    //     formData.append("address_line2", $("#address2").val());
    //     formData.append("address_line2", '');
    //     formData.append("city", $("#city").val());
    //     formData.append("state", $("#state").val());
    //     formData.append("country", $("#country").val());
    //     formData.append("postal_code", $("#zip").val());
    //     var url = $("#payment-form").attr('action');
    //     $("#card-errors").html('');
    //     $.ajax({
    //         url: url,
    //         type: "post",
    //         data: formData,
    //         cache: false,
    //         contentType: false,
    //         processData: false,
    //         dataType: "json",
    //         beforeSend: function () {
    //             showLoader();
    //         },
    //         success: async function (response) {
    //             hideLoader();
    //             $("#submit-btn,.prev").removeAttr("disabled");
    //             $("#submit-btn .fa").remove();

    //             if (response.requires_action) {
    //                 const { error, paymentIntent } = await stripe.confirmCardPayment(response.client_secret);

    //                 if (error) {
    //                     $("#card-errors").html(error.message);
    //                     $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
    //                     initStripe();
    //                     googleRecaptcha();
    //                 } else if (paymentIntent.status === 'succeeded') {
    //                     // window.location.href = response.redirect_url;
    //                 }
    //             } else if (response.status === true) {
    //                 window.location.href = response.redirect_url;
    //             } else {
    //                 $("#card-errors").html(response.message);
    //                 $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
    //                 initStripe();
    //                 googleRecaptcha();
    //             }
    //         },
    //         error: function () {
    //             $("#card-errors").html("Error while submitting. Try again");
    //             $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
    //             $("#submit-btn,.prev").removeAttr("disabled");
    //             $("#submit-btn .fa").remove();
    //             initStripe();
    //             googleRecaptcha();
    //         }
    //     });
    // }
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
        });
    }
    function initStripe() {
        elements = stripe.elements();

       var style = {
            base: {
                color: '#32325d',  padding: '10px', // some versions accept this
                fontFamily: '',lineHeight: '1.75',
                fontSmoothing: 'antialiased',
                fontSize: '13px',
                '::placeholder': {
                    color: '#666'
                }
            }, focus: {
                    // Neutralize focus styles
                    color: '#32325d',
                    backgroundColor: '#fff',
                    // No borderColor or shadow applied
                },
                invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        // Create individual elements
        cardNumber = elements.create('cardNumber', {
            style: style
        });
        cardExpiry = elements.create('cardExpiry', {
            style: style
        });
        cardCvc = elements.create('cardCvc', {
            style: style
        });

        // Mount the elements to their respective divs
        cardNumber.mount('#card-number');
        cardExpiry.mount('#card-expiry');
        cardCvc.mount('#card-cvc');

        // Enable submit button only if all fields are filled

        // Listen for changes on each element
        cardNumber.on('change', handleCardInputChange);
        cardExpiry.on('change', handleCardInputChange);
        cardCvc.on('change', handleCardInputChange);
    }

    function handleCardInputChange(event) {
        // Check completion statuses
        const isCardNumberComplete = cardNumber._complete || event.elementType === 'cardNumber' && event.complete;
        const isCardExpiryComplete = cardExpiry._complete || event.elementType === 'cardExpiry' && event.complete;
        const isCardCvcComplete = cardCvc._complete || event.elementType === 'cardCvc' && event.complete;
        // Enable or disable the submit button
        if (isCardNumberComplete && isCardExpiryComplete && isCardCvcComplete) {
            $('#submit-btn').prop('disabled', false);
        } else {
            $('#submit-btn').prop('disabled', true);
        }

        // Display errors if any
        if (event.error) {
            $("#card-errors").text(event.error.message);
            
        } else {
            $("#card-errors").text("");
        }
        
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
<script>
    function showErrorModal(message){
        closeModal();
        let seconds = 10;
        $("#errorModal").modal("show");
        $("#countdown").html(seconds);
        $("#custom-error-message").html(message);
        const countdownElement = document.getElementById('countdown');
        const countdownInterval = setInterval(() => {
            seconds--;
            if (seconds >= 0) {
                countdownElement.textContent = seconds;
            }
            if (seconds === 0) {
                clearInterval(countdownInterval);
                // Redirect after countdown ends
                location.reload();
            }
        }, 1000);
    }
</script>
@endpush