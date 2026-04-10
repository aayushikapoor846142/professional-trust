@extends('admin-panel.layouts.app')

@section('content')

<div class="container">
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                You will be charged ${{ number_format($plan->amount, 2) }}/Onetime for {{ $plan->plan_title
                }} Plan
            </div>
            <div class="card-body">
                <form id="payment-form" action="{{ baseUrl('membership-plans/pay-for-onetime') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" id="plan" value="{{ $plan->id }}">
                    <input type="hidden" name="amount" id="amount" value="{{ $plan->amount }}">
                    <div class="row step2" id="step2">
                        <div class="row mb-3">
                            <div class="col js-form-message">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control required" id="first_name" name="first_name" oninput="validateName(this)">
                            </div>
                            <div class="col js-form-message">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control required" id="last_name" name="last_name"
                                    oninput="validateName(this)">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col js-form-message">
                                <label for="address" class="form-label">Address *</label>
                                <input type="text" class="form-control required address google-address" id="address" name="address" 
                                    >
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col js-form-message">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control required city" id="city" name="city" oninput="validateName(this)">
                            </div>
                            <div class="col js-form-message">
                                <label for="state" class="form-label">State/Province *</label>
                                <input type="text" class="form-control required state" id="state" name="state"
                                    oninput="validateName(this)">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col js-form-message">
                                <label for="zip" class="form-label">Postal Code *</label>
                                <input type="text" class="form-control required zip" id="zip" name="zip" oninput="validateZipCode(this)">
                            </div>
                            <div class="col js-form-message">
                                <label for="country" class="form-label">Country *</label>
                                <div class="col">
                                    <input type="text" class="form-control required country" id="country" name="country" oninput="validateName(this)">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col js-form-message">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control required" id="email" name="email" 
                                    >
                            </div>
                        </div>
                        <!-- Phone Number -->
                        <div class="row mb-3">
                            <div class="col phoneno-with-code">
                                  {!! FormHelper::formPhoneNo([
                                    'name' => "phone",
                                    'country_code_name' => "country_code",
                                    "label" => "Phone Number",
                                    "default_country_code"=> '+1',
                                    "required" => false,
                                    'events'=>['oninput=validatePhoneInput(this)']]
                                    ) !!}
                                {{-- <label for="phone" class="form-label">Phone Number (optional)</label>
                                <input type="tel" class="form-control phone_no" id="phone" name="phone"
                                    placeholder="Enter phone number" oninput="validatePhoneInput(this)">
                                <input type="hidden" class="required country_code" name="country_code" id="user_country_code"  value="{{ $user->country_code??'+1' }}" placeholder="Your phone number" aria-label="Phone Number"> --}}
                            </div>
                        </div>
                        <div class="cds-t35-content-support-form-segments-body">
                            <div class="cds-t35-content-support-form-segments-body-total">
                                <div class="cds-t35-content-support-form-segments-body-total-header">
                                    <h5>Total  ${{ number_format($plan->amount, 2) }}</h5>
                                </div>
                                <div class="cds-t35-content-support-form-segments-body-total-footer">
                                    <ul class="cds-t35-content-support-form-segments-body-total-footer-wrap">
                                        <li>
                                            <img src="{{url('assets/frontend/images/cards/visa.png') }}" alt=""
                                                class="Visa Icon">
                                        </li>
                                        <li> <img src="{{url('assets/frontend/images/cards/master.png') }}" alt=""
                                            class="Mastercard Icon"></li>
                                        <li> <img src="{{url('assets/frontend/images/cards/discover.png') }}" alt=""
                                            class="Discover Icon"></li>
                                        <li> <img src="{{url('assets/frontend/images/cards/diner.png') }}" alt=""
                                            class="Diner Icon"></li>
                                        <li> <img src="{{url('assets/frontend/images/cards/jcb.png') }}" alt=""
                                            class="JCB Icon"></li>
                                    </ul>
                                </div>
                            </div>
                            <div id="card-errors" class="card-errors cds-t55-card-error" role="alert">
                            </div>
                            <div class="row mb-3">
                                <div class="col js-form-message">
                                    <label class="form-label" for="card-number">Card Number*</label>
                                    <div id="card-number" class="StripeElement form-control"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6 js-form-message">
                                    <label class="form-label" for="card-expiry">Expiration Date*</label>
                                    <div id="card-expiry" class="StripeElement form-control"></div>
                                </div>
                                <div class="col-6 js-form-message">
                                    <label class="form-label" for="card-cvc">CVC*</label>
                                    <div id="card-cvc" class="StripeElement form-control"></div>
                                </div>
                            </div>
                            <button type="button" class="CdsTYButton-btn-primary" id="submit-btn" onclick="submitPayment()">Purchase</button>
                        </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script src="https://js.stripe.com/v3/"></script>

<script>
   var form = document.getElementById('payment-form');
   var submitButton = document.getElementById('submit-btn');

   var elements;
    var cardNumber, cardExpiry, cardCvc;
    var stripe = Stripe('{{ apiKeys('STRIPE_KEY') }}');
    initStripe(); 
    function submitPayment(){
       
        var is_valid = formValidation(("payment-form"));
        if(!is_valid){
            return false;
        }

        if(is_valid){
            $("#submit-btn").attr("disabled","disabled");
            $("#submit-btn").append(" <i class='fa fa-spin fa-spinner'></i>");
            stripe.createPaymentMethod({
                type: 'card',
                card: cardNumber,
            }).then(function(result) {
                if (result.error) {
                    $("#card-errors").html(result.error.message);
                    $('html,body').animate({
                        scrollTop: $("#card-errors").offset().top},
                    'slow');
                    submitButton.disabled = false;
                    $("#submit-btn").removeAttr("disabled");
                    $("#submit-btn .fa").remove();
                } else {
                    paymentMethod = result.paymentMethod.id;
                //    $("#payment_method_id").val(paymentMethod);
                    // form.submit();
                    handlePayment(paymentMethod);
                }
            });
            // Continue with form submission
            
        }
    }

    function handlePayment(paymentMethodId){
        var formData = new FormData($("#payment-form")[0]);
        formData.append("payment_method_id",paymentMethodId);
        var url = $("#payment-form").attr('action');
        $("#card-errors").html('');
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
                $("#submit-btn").removeAttr("disabled");
                $("#submit-btn .fa").remove();
                if (response.status == true) {
                    successMessage(response.message);
                    window.location.href = response.url;
                    
                } else {
                    $("#card-errors").html(response.message);
                    $('html,body').animate({
                        scrollTop: $("#card-errors").offset().top},
                    'slow');
                    initStripe();
                    googleRecaptcha();
                }
            },
            error: function() {
                $("#card-errors").html("Error while submitting. Try again");
                $('html,body').animate({
                    scrollTop: $("#card-errors").offset().top},
                'slow');
                $("#submit-btn").removeAttr("disabled");
                $("#submit-btn .fa").remove();
                initStripe();
                googleRecaptcha();
            }
        });
    }

    function initStripe() {
        elements = stripe.elements();

        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        // Create individual elements
        cardNumber = elements.create('cardNumber', {style: style});
        cardExpiry = elements.create('cardExpiry', {style: style});
        cardCvc = elements.create('cardCvc', {style: style});

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
        // Check if all Stripe elements are filled
        var isCardNumberComplete = cardNumber._complete;
        var isCardExpiryComplete = cardExpiry._complete;
        var isCardCvcComplete = cardCvc._complete;
        
        // Enable submit button if all fields are complete and valid
        if (event.elementType === 'cardNumber') {
            isCardNumberComplete = event.complete;
        } else if (event.elementType === 'cardExpiry') {
            isCardExpiryComplete = event.complete;
        } else if (event.elementType === 'cardCvc') {
            isCardCvcComplete = event.complete;
        }
        console.log("isCardNumberComplete",isCardNumberComplete);
        console.log("isCardExpiryComplete",isCardExpiryComplete);
        console.log("isCardCvcComplete",isCardCvcComplete);

        if (isCardNumberComplete && isCardExpiryComplete && isCardCvcComplete) {
            $('#submit-btn').prop('disabled', false);
        } else {
            $('#submit-btn').prop('disabled', true);
        }

        // Display error message if any element has an error
        const errorElement = document.getElementById('card-errors');
        if (event.error) {
            errorElement.textContent = event.error.message;
        } else {
            errorElement.textContent = '';
        }
    }
</script>

@endsection