<div class="CdsAppointmentSystem-header">
    <h1 class="CdsAppointmentSystem-title">Appointment Booking Payment</h1>
</div>

<form id="preview-form" class="js-validate" action="{{ baseUrl('appointments/appointment-booking/save') }}" method="post">
    @csrf
    <input type="hidden" name="amount_to_pay" id="amount_to_pay" value="{{$appointment_data->price}}" />
    <input type="hidden" id="professional_id"  name="professional_id" value="{{$professional_id}}" />
    <input type="hidden"id="type"  name="type" value="payment" />
    <input type="hidden"id="booking_id"  name="booking_id" value="{{$booking_id}}" />
    
    <div class="CdsAppointmentSystem-preview-card">
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Appointment Date (As per your timezone):</div>
            <div class="CdsAppointmentSystem-preview-value">
                {{ dateFormat($appointment_data->appointment_date) ?? '' }}<br>
                {{ (date("h:i A", strtotime($startInProfTz))??'').'-'.(date("h:i A", strtotime($endInProfTz)) ?? '') }}
            </div>
        </div>
        
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Booking Price:</div>
            <div class="CdsAppointmentSystem-preview-value" style="font-size: 20px; font-weight: 700; color: #e53e3e;">
                {{currencySymbol($appointment_data->currency).' '.$appointment_data->price}}
            </div>
        </div>
    </div>
    
    <div style="margin: 30px 0;">
        <button type="button" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-primary payment_type" data-value="Stripe" style="background: #635bff;">
            Pay Now with Stripe
        </button>
    </div>
    
    <div class="CdsAppointmentSystem-btn-group">
        <button type="button" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-secondary previous">Previous</button>
    </div>
</form>

@include('admin-panel.03-appointments.appointment-system.appointment-booking.payment.stripe.stripe-payment')

<script src="https://js.stripe.com/v3/"></script>

<!-- End Content -->
@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
    
    <script>
      
    var paymentMethod = '';
    $(document).ready(function(){
         function appointmentUniqueCheck(){
         //   alert('hhhh');
            $.ajax({
                url: "{{baseUrl('appointment-check/'.$appointment_data->unique_id)}}",
                type: "GET",
                data: '',
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    showLoader();
                },
                success: async function (response) {
                    hideLoader();
                    if(response.status==true){
                        errorMessage("Slot has been booked by someone else");
                        redirect(response.redirect_back);
                    }
                    console.log(response);
                
                },
                error: function (xhr) {
                      }
            });
        }
        //setInterval(appointmentUniqueCheck, 50000);
        setTimeout(appointmentUniqueCheck, 5000);

        googleRecaptcha();
       
        setTimeout(() => {
            initStripe();
            initGoogleAddress();
        }, 1000); 

        $(document).on('change', '.sameAsMailing', function () {
            if ($(this).is(':checked')) {            
                $('.billing_address').val($('.address').val());
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
    });

    var elements;
    var cardNumber, cardExpiry, cardCvc;
    var stripe;
    // initStripe(); 
  
    function submitPayment(){
        const recaptchaResponse = grecaptcha.getResponse();
        var is_valid = true;
        if (!recaptchaResponse) {
            is_valid = false;
            $(".grecaptcha").find(".required-error").remove();
            var errmmsg ='<div class="required-error text-danger">Please complete the CAPTCHA to proceed</div>';
            $(".grecaptcha").append(errmmsg);
           
        }

        if($("#address-1").val() == ''){
            is_valid = false;
            $(".address-div").find(".required-error").remove();
            var errmmsg ='<div class="required-error text-danger">Please Enter Address</div>';
            $(".address-div").append(errmmsg);
        }

        if($("#city").val() == ''){
            is_valid = false;
            $(".city-div").find(".required-error").remove();
            var errmmsg ='<div class="required-error text-danger">Please Enter City</div>';
            $(".city-div").append(errmmsg);
        }
        if($("#state").val() == ''){
            is_valid = false;
            $(".state-div").find(".required-error").remove();
            var errmmsg ='<div class="required-error text-danger">Please Enter State</div>';
            $(".state-div").append(errmmsg);
        }
        if($("#zip").val() == ''){
            is_valid = false;
            $(".zip-div").find(".required-error").remove();
            var errmmsg ='<div class="required-error text-danger">Please Enter Zip</div>';
            $(".zip-div").append(errmmsg);
        }
        if($("#country").val() == ''){
            is_valid = false;
            $(".country-div").find(".required-error").remove();
            var errmmsg ='<div class="required-error text-danger">Please Enter Billing Country</div>';
            $(".country-div").append(errmmsg);
        }
        if(!$("input[name=terms_condition]").is(":checked")){
            is_valid = false;
            $(".checkbox1").find(".required-error").remove();
            var errmmsg ='<div class="required-error text-danger">Please Accept to our Terms and Conditions</div>';
            $(".checkbox1").append(errmmsg);
        }else{
            var errmmsg ='';
            $(".checkbox1").append(errmmsg);
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
            //        submitButton.disabled = false;
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
    function initStripe() {
        stripe = Stripe('{{ apiKeys("STRIPE_KEY") }}');
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
    function handlePayment(paymentMethodId){
        var formData = new FormData($("#payment-form")[0]);
        formData.append("payment_method_id",paymentMethodId);
        formData.append("amount_to_pay","{{$amount_to_pay}}")
        var url = $("#payment-form").attr('action');
        $("#card-errors").html('');
        showPopup("<?php echo url('processing-payment') ?>");
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: async function (response) {
                console.log(response);
                if (response.requires_action && response.client_secret) {
                    // const result = await stripe.confirmCardPayment(response.client_secret, {
                    //     return_url: '{{ url('stripe/callback') }}'
                    // });
                    const result = await stripe.confirmCardPayment(response.client_secret);
                    if (result.error) {
                        $("#card-errors").html(result.error.message);
                        $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
                        // showErrorModal(result.error.message);
                        errorMessage(result.error.message);
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
                    // showErrorModal(response.message || "Something went wrong. Please try again.");
                     errorMessage(response.message || "Something went wrong. Please try again.");
                    // setTimeout(() => {
                    //     location.reload();
                    // }, 10000);
                }
            },
            error: function (xhr) {
                console.log(xhr);
                closeModal();
                $("#card-errors").html("Error submitting payment. Please try again.");
                // showErrorModal("Error submitting payment. Please try again.");
                errorMessage("Error submitting payment. Please try again.");
                $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
                $("#submit-btn,.prev").removeAttr("disabled");
                $("#submit-btn .fa").remove();
                initStripe();
                googleRecaptcha();
            }
         });
       
    }
    function completePaymentAction(payment_intent_id,new_user){
        const formData = new FormData($("#payment-form")[0]);
        var paymentType = $('input[name="payment_type"]:checked').val();
        formData.append("payment_intent_id", payment_intent_id);
        formData.append("cardholder_name", $("#first_name").val() + " " + $("#last_name").val());
        formData.append("address_line1", $("#address").val());
        formData.append("address_line2", $(".billing_address").val() ?? '');
        formData.append("city", $("#city").val());
        formData.append("state", $("#state").val());
        formData.append("country", $("#country").val());
        formData.append("postal_code", $("#zip").val());
        formData.append("payment_type", paymentType);
        formData.append("new_user", new_user);
        
        formData.append("amount_to_pay", $("#amount_to_pay").val());

        formData.append("email", $("#email").val());

        const url = $("#payment-form").attr('action');

        $.ajax({
            url: "{{ url('stripe/complete-payment') }}",
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
                // showErrorModal("Error submitting payment. Please try again.");
                errorMessage("rror submitting payment. Please try again.");
                $('html,body').animate({ scrollTop: $("#card-errors").offset().top }, 'slow');
                $("#submit-btn,.prev").removeAttr("disabled");
                $("#submit-btn .fa").remove();
            }
        });
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

 // billing address
 
</script>
<script>
    $(document).on('click', '.payment_type', function () {
        var type_val = $(this).attr("data-value");
        if(type_val=="Stripe"){
            $('#stripe-payment').show();
        }
        
    //    showPopup('<?php echo baseUrl('choose-payment-type-for-appointment') ?>/'+type_val+'/'+$('#professional_id').val()+'/'+$("#booking_id").val()+'/'+$("#amount_to_pay").val());
    });
    $(document).ready(function() {

        $("#preview-form").submit(function(e) {

            e.preventDefault();
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $("#preview-form").attr('action');

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
                error: function() {
                    internalError();
                }
            });

        });

    
    });

    // function showErrorModal(message){
    //     closeModal();
    //     let seconds = 10;
    //     $("#errorModal").modal("show");
    //     $("#countdown").html(seconds);
    //     $("#custom-error-message").html(message);
    //     const countdownElement = document.getElementById('countdown');
    //     const countdownInterval = setInterval(() => {
    //         seconds--;
    //         if (seconds >= 0) {
    //             countdownElement.textContent = seconds;
    //         }
    //         if (seconds === 0) {
    //             clearInterval(countdownInterval);
    //             // Redirect after countdown ends
    //             location.reload();
    //         }
    //     }, 1000);
    // }
</script>
@endpush