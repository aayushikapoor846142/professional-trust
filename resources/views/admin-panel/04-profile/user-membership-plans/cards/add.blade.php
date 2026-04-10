@extends('admin-panel.layouts.app')

@section('content')
 <div class="ch-action">
                    <a href="{{ baseUrl('articles') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-solid fa-left me-1"></i>
                        Back
                    </a>
                </div>
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
---------------------

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <form id="form" class="js-validate mt-3" action="{{ baseUrl('my-membership-plans/save-card') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 col-sm-6">
                            
                            </div>
                            <div class="col-md-4 col-sm-6">
                                    <div class="col js-form-message">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" class="form-control required" id="cardholder" name="cardholder" oninput="validateName(this)">
                                    </div>

                                <input type="hidden" name="customer_id" id="customer_id" value="{{$user->stripe_id}}">
                                <input type="hidden" name="subscription_id" id="subscription_id" value="{{$record->stripe_subscription_id}}">
                                    
                                <div class="cds-t35-content-support-form-segments-body">
                                    <div class="cds-t35-content-support-form-segments-body-total">
                                    <div class="cds-t35-content-support-form-segments-body-total-header">
                    
                                    </div><div class="cds-t35-content-support-form-segments-body-total-footer">
                                    <ul class="cds-t35-content-support-form-segments-body-total-footer-wrap"><li>
                                    <img src="{{url('assets/frontend/images/cards/visa.png') }}" alt=""
                                class="Visa Icon"></li>
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
                                    </div>  </div>
                                    
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
                            </div>


                        </div>
                        <div class="text-end mt-4">
                            <button id="card-button" type="submit" class="btn add-CdsTYButton-btn-primary" data-secret="{{ $intent->client_secret }}">Save</button>
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
    const stripe = Stripe('{{ apiKeys('STRIPE_KEY') }}');
    let elements, cardNumber, cardExpiry, cardCvc;

    document.addEventListener('DOMContentLoaded', () => {
    initStripe();
    const form = document.getElementById('form');
    const cardBtn = document.getElementById('card-button');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const cardholderName = document.getElementById('cardholder').value.trim();
        const cardErrorsDiv = document.getElementById('card-errors');

        // Validate cardholder name
        if (!cardholderName) {
            cardErrorsDiv.textContent = 'The name on the card is required.';
            return;
        }

        // Validate Stripe card details
        const { setupIntent, error } = await stripe.confirmCardSetup(
            cardBtn.dataset.secret, {
                payment_method: {
                    card: cardNumber,
                    billing_details: {
                        name: cardholderName
                    }
                }
            }
        );

        if (error) {
            cardErrorsDiv.textContent = error.message;
            return;
        }

        // Append payment method and submit the form
        const paymentMethodInput = document.createElement('input');
        paymentMethodInput.setAttribute('type', 'hidden');
        paymentMethodInput.setAttribute('name', 'payment_method');
        paymentMethodInput.setAttribute('value', setupIntent.payment_method);
        form.appendChild(paymentMethodInput);
        submitForm();
    });
});


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

        cardNumber = elements.create('cardNumber', { style: style });
        cardExpiry = elements.create('cardExpiry', { style: style });
        cardCvc = elements.create('cardCvc', { style: style });

        cardNumber.mount('#card-number');
        cardExpiry.mount('#card-expiry');
        cardCvc.mount('#card-cvc');

        cardNumber.on('change', handleCardInputChange);
        cardExpiry.on('change', handleCardInputChange);
        cardCvc.on('change', handleCardInputChange);
    }

    function handleCardInputChange(event) {
        const cardBtn = document.getElementById('card-button');
        const errorDiv = document.getElementById('card-errors');

        if (event.error) {
            errorDiv.textContent = event.error.message;
            // cardBtn.disabled = true;
        } else {
            errorDiv.textContent = '';
            // cardBtn.disabled = !isFormComplete();
        }
    }

    function isFormComplete() {
        return cardNumber._complete && cardExpiry._complete && cardCvc._complete;
    }

    function submitForm() {
        const formData = new FormData(document.getElementById('form'));
        const url = document.getElementById('form').getAttribute('action');

        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response.status) {
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
    }
</script>
@endsection
