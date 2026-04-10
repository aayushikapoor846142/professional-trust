@extends('admin-panel.layouts.app')

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <div class="cds-ty-dashboard-small-box-header-summary">
                        <h3>Plan Summary</h3>
                        <p>Please review details carefully</p>
                    </div>
                    <div class="cds-ty-dashboard-small-box-header-description">
                        <div class="cds-ty-dashboard-small-box-header-description-segment">
                            <span> Amount due </span>
                            <p>${{ number_format($plan->amount, 2) }}/Month</p>
                        </div>
                        <div class="cds-ty-dashboard-small-box-header-description-segment">
                            <span>Plan name</span>
                            <p>{{ $plan->plan_title }}</p>
                        </div>
                    </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <form id="payment-form" action="{{ baseUrl('membership-plans/subscription') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" id="plan" value="{{ $plan->id }}" />
                    <div class="cds-ty-dashboard-small-box-body">
                        <div id="step2">
                            <div class="cds-t35-content-support-form-segments-body">
                                <div class="cds-t35-content-support-form-segments-body-total">
                                    <div class="cds-t35-content-support-form-segments-body-total-header">
                                        <h5>Total ${{ number_format($plan->amount, 2) }}</h5>
                                    </div>
                                    <div class="cds-t35-content-support-form-segments-body-total-footer">
                                        <ul class="cds-t35-content-support-form-segments-body-total-footer-wrap">
                                            <li>
                                                <img src="{{url('assets/frontend/images/cards/visa.png') }}" alt="" class="Icon Visa" />
                                            </li>
                                            <li><img src="{{url('assets/frontend/images/cards/master.png') }}" alt="" class="Icon Mastercard" /></li>

                                            <li><img src="{{url('assets/frontend/images/cards/discover.png') }}" alt="" class="Discover Icon" /></li>

                                            <li><img src="{{url('assets/frontend/images/cards/diner.png') }}" alt="" class="Diner Icon" /></li>

                                            <li><img src="{{url('assets/frontend/images/cards/jcb.png') }}" alt="" class="Icon JCB" /></li>
                                        </ul>
                                    </div>
                                </div>
                                <div id="card-errors" class="card-errors cds-t55-card-error" role="alert"></div>
                                <div class="row mb-3">
                                    <div class="col js-form-message">
                                        <label class="form-label" for="card-number">Card Number*</label>
                                        <div id="card-number" class="form-control StripeElement"></div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6 js-form-message">
                                        <label class="form-label" for="card-expiry">Expiration Date*</label>
                                        <div id="card-expiry" class="form-control StripeElement"></div>
                                    </div>
                                    <div class="col-6 js-form-message">
                                        <label class="form-label" for="card-cvc">CVC*</label>
                                        <div id="card-cvc" class="form-control StripeElement"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cds-ty-dashboard-small-box-footer"><button type="submit" class="cds-ty-dashboard-subscribe-button" id="card-button" data-secret="{{ $intent->client_secret }}">Subscribe</button></div>
                </form>
       
			</div>
	
	</div>
  </div>
</div>

@endsection

@section('javascript')
<script src="https://js.stripe.com/v3/"></script>

<script>
    // Initialize Stripe and Elements
    const stripe = Stripe('{{ apiKeys('STRIPE_KEY') }}');
   
    let elements, cardNumber, cardExpiry, cardCvc;

    document.addEventListener('DOMContentLoaded', () => {
        initStripe(); // Initialize Stripe elements when the DOM is ready

        const form = document.getElementById('payment-form');
        const cardBtn = document.getElementById('card-button');
        
       
        // Handle form submission
        form.addEventListener('submit', async (e) => {
            
            e.preventDefault();
            cardBtn.disabled = true;
        
            // Confirm the card setup with Stripe
            const { setupIntent, error } = await stripe.confirmCardSetup(
                cardBtn.dataset.secret, {
                    payment_method: {
                        card: cardNumber,
                     
                    }
                }
            );
    
            if (error) {
                cardBtn.disabled = false;
                 const errorDiv = document.getElementById('card-errors');
    errorDiv.textContent = error.message; // Set the error message
    errorDiv.style.display = 'block'; 
            } else {
                // Pass the payment method ID to the backend
                let paymentMethodInput = document.createElement('input');
                paymentMethodInput.setAttribute('type', 'hidden');
                paymentMethodInput.setAttribute('name', 'payment_method');
                paymentMethodInput.setAttribute('value', setupIntent.payment_method);
                form.appendChild(paymentMethodInput);

                form.submit();
            }
        });
    });

    // Initialize Stripe Elements
    function initStripe() {
        elements = stripe.elements();

        const style = {
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

        // Create individual card elements
        cardNumber = elements.create('cardNumber', { style: style });
        cardExpiry = elements.create('cardExpiry', { style: style });
        cardCvc = elements.create('cardCvc', { style: style });

        // Mount the elements to their respective containers
        cardNumber.mount('#card-number');
        cardExpiry.mount('#card-expiry');
        cardCvc.mount('#card-cvc');

        // Add event listeners to enable/disable the button based on input
        cardNumber.on('change', handleCardInputChange);
        cardExpiry.on('change', handleCardInputChange);
        cardCvc.on('change', handleCardInputChange);
    }

    // Handle card input changes
    function handleCardInputChange(event) {
        const cardBtn = document.getElementById('card-button');
        const errorDiv = document.getElementById('card-errors');
        console.log(event);
        if (event.error) {
            errorDiv.textContent = event.error.message; // Display validation errors
            cardBtn.disabled = false;
        } else {
            errorDiv.textContent = ''; // Clear validation errors
            if (isFormComplete()) {
                cardBtn.disabled = true;
            }
        }
    }

    // Check if all form fields are complete
    function isFormComplete() {
        return (
            cardNumber._complete &&
            cardExpiry._complete &&
            cardCvc._complete
        );
    }


    function validateForm(step){
    
        var activeTab = $('.nav-tabs .active');
        var nextTab = activeTab.parent().next().find('a.nav-link');
        if(step == 'step1'){
            $("#step1").removeClass("step-done");
            if($("input[name=amount]").is(":checked")){
                if($("input[name=amount]:checked").val() == 'other'){
                    if($("#customAmount").val() == ''){
                        errorMessage("Enter amount to pay");
                    }else{
                       $("#step2").removeClass("disabled-form");
                       $("#step2").addClass("show active");
                       $("#amount-to-pay").val($("#customAmount").val());
                        $('html,body').animate({
                            scrollTop: $("#step2").offset().top},
                        'slow');
                        $("#step1").addClass("step-done");
                    }
                }else{
                    $("#step2").removeClass("disabled-form");
                    $("#step2").addClass("show active");
                    $('html,body').animate({
                        scrollTop: $("#step2").offset().top},
                    'slow');
                    $("#amount-to-pay").val($("input[name=amount]:checked").val());
                    $("#step1").addClass("step-done");
                }
                $(".amount-paid").html("Amount {{ currencySymbol() }}"+$("#amount-to-pay").val());
            }else{
                errorMessage("Select the amount");
            }
        }
        else if(step == 'step2'){
            $("#step2").removeClass("step-done");
            var is_valid = formValidation("step2");
            if(!is_valid){
                return false;
            }
            $("#step3").removeClass("disabled-form");
            $("#step3").addClass("show active");
            $('html,body').animate({
                scrollTop: $("#card-detail").offset().top},
            'slow');
            $("#step2").addClass("step-done");
        }
        else if(step == 'step3'){
            $("#step3").removeClass("step-done");
            var is_valid = formValidation("step3");
            if(!is_valid){
                return false;
            }
            submitPayment();
        }
    }
</script>
@endsection