@extends('admin-panel.layouts.app')

@section('content')
<section class="cdsTYOnboardingDashboard-breadcrumb-section">
    <div class="cdsTYOnboardingDashboard-breadcrumb-section-header">
        <div class="cdsTYOnboardingDashboard-page-title">
            <h2>Add Card</h2>
        </div>
        <div class="breadcrumb-container">
            <ol class="breadcrumb">
                <i class="fa-regular fa-grid-2"></i>
                <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('/') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('payment-methods/cards') }}">Payment Methods</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$pageTitle}}</li>
            </ol>
        </div>
    </div>
</section>
<section class="cdsTYOnboardingDashboard-sub-action">
    <div class="ch-action">
        <a href="{{ baseUrl('payment-methods') }}" class="CdsTYButton-btn-primary">
            <i class="fa-solid fa-left me-1"></i>
            Back
        </a>
    </div>
</section>

<section class="cdsTYDashboard-add-card">
    <div class="cdsTYDashboard-add-card-container">
        <div class="cdsTYDashboard-add-card-container-body">
            <form id="form" class="js-validate" action="{{ baseUrl('payment-methods/save-card') }}" method="post">
                @csrf
                <div class="cdsTYDashboard-add-card-container-body-card-holder">
                    <div class="cdsTYDashboard-add-card-container-body-card-holder-header">Add Card</div> 
                    <input type="hidden" name="customer_id" id="customer_id" value="{{$user->stripe_id}}">
                    <input type="hidden" name="subscription_id" id="subscription_id" value="{{$record->subscription_id ?? ''}}">
                    <div class="cds-t35-content-support-form-segments-body cdsTYDashboard-add-card-container-body-card-holder-body">
                        <div id="card-errors" class="card-errors cds-t55-card-error" role="alert">
                        </div>
                        <h3>Personal Details</h3>
                        <div class="row">
                            <div class="col-xl-12 js-form-message mb-3">
                                <label for="first_name" class="form-label">Enter Name on Card *</label>
                                <input type="text" class="form-control required" id="cardholder" name="cardholder" oninput="validateName(this)">
                            </div>
                            <div class="col-xl-12 js-form-message mb-3">
                                <label class="form-label" for="card-number">Card Number*</label>
                                <div id="card-number" class="StripeElement"></div>
                            </div>
                            <div class="col-xl-12 js-form-message mb-3">
                                <label class="form-label" for="card-expiry">Expiration Date*</label>
                                <div id="card-expiry" class="StripeElement"></div>
                            </div>
                            <div class="col-xl-12 js-form-message mb-3">
                                <label class="form-label" for="card-cvc">CVC*</label>
                                <div id="card-cvc" class="StripeElement"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12">
                                <h3>Address Details</h3>
                            </div>
                            <div class="col-xl-12 js-form-message mb-3">
                                <label for="first_name" class="form-label">Address*</label>
                                <input type="text" class="form-control required" id="address" name="address" >    
                            </div>
                            <div class="col-xl-6 js-form-message mb-3">
                                <label class="form-label" for="card-expiry">City*</label>
                                <input type="text" class="form-control required" id="city" name="city" oninput="validateName(this)">
                            </div>
                            <div class="col-xl-6 js-form-message mb-3">
                                <label class="form-label" for="card-cvc">Province/State*</label>
                                <input type="text" class="form-control required" id="state" name="state" oninput="validateName(this)">
                            </div>
                            <div class="col-xl-6 js-form-message mb-3">
                                <div class="form-group">
                                    <label class="form-label">Select Country</label>
                                    <div class="select-custom required">
                                        <select name="country" class="form-control" id="country">
                                            <option value="">*Country</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->sortname }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 js-form-message mb-3">
                                <label class="form-label" for="card-cvc">Postal Code*</label>
                                <input type="text" class="form-control required" id="pincode" name="pincode">
                            </div>
                        </div>
                        <div id="address-errors" class="address-errors cds-t55-card-error text-danger" role="alert">
                        </div>
                    </div>            
                    <div class="cdsTYDashboard-add-card-container-body-card-holder-footer">
                        <button id="card-button" type="submit" class="btn add-CdsTYButton-btn-primary" data-secret="{{ $intent->client_secret }}">Save</button>
                    </div>
                </div>
            </form>
            <div class="cdsTYDashboard-add-card-container-footer">
            </div>
        </div>
    </div>
</section>
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
        const addressErrorsDiv = document.getElementById('address-errors');
        const address = document.getElementById('address').value.trim();
        const city = document.getElementById('city').value.trim();
        const state = document.getElementById('state').value.trim();
        const country = document.getElementById('country').value.trim();
        const pincode = document.getElementById('pincode').value.trim();
    

        // Validate cardholder name
        if (!cardholderName) {
            cardErrorsDiv.textContent = 'The name on the card is required.';
            return;
        }

        if (address == "" || city == "" || state == "" || country == "" || pincode == "") {
            addressErrorsDiv.textContent = 'All Address Field is Required.';
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
