@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ url('assets/plugins/select2/select2.min.css') }}">

<main>
    <section class="cds-t21n-breadcrumbs-section">
        <div class="container">
            <div class="row">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Support Initiative</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    {{-- content --}}
    <section id="cds-t21n-content-section" class="cds-content mt-md-4 mt-lg-5 mb-5 pt-4 pt-md-0">
        <div class="container">
            <div class="row">
                <form id="payment-form" method="post" action="{{ url('pay-for-support') }}">
                    @csrf
                    <div class="row">
                        <div class="col-xl-6 col-md-12 col-lg-5 mt-3 mt-lg-0">
                            <div class="cds-t21n-content-section-sidebar">
                                <div class="cds-t21n-content-section-sidebar-support-form-content">
                                    <div class="cds-t21n-content-section-sidebar-support-form-content-header">
                                        <img src="{{url('assets/frontend/images/banner-support.svg') }}" alt=""
                                            class="Diner Icon">
                                        <span>Let's Join Together</span>
                                        <h4 class="headingh2"> Exposing the Shadows: Combating Modern-Day Slavery and Illegal Immigration Networks in Global Immigration.</h4>
                                    </div>
                                    <div class="cds-t21n-content-section-sidebar-support-body">
                                        <div class="cds-t35n-content-support-form-section-para">
                                            <div class="cds-t35n-content-support-form-section-para-highlight">
                                                <p>Modern-day slavery, illegal immigration, and human trafficking are pressing issues affecting nations like Canada, the United States, Australia, New Zealand, and the United Kingdom. Every year, millions of hopeful individuals embark on the immigration journey, seeking new opportunities and a better life for themselves and their families. Yet, for far too many, this path is overshadowed by unethical actors who exploit their dreams for profit. These individuals aren’t just scammers; they are integral players in illegal immigration networks that push vulnerable people into exploitative situations, often involving forced labor, abuse, and control. </p>
                                                <p><strong>This is the most ambitious project ever undertaken, and it marks the first time ever in the immigration industry where are not only directly engaging with these Unauthorized Practitioners (UAPs), Unauthorized Corporate Entities (UACEs), and Unethical Employers and Related Professionals (UERPs) but also creating a database of such individuals and entities for public awareness. </strong></p>
                                            </div>
                                            <div class="cds-t35n-content-support-form-section-para-details">
                                                <h5>Why is Exposing These Elements So Important?</h5>
                                                <ul>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i> <strong>Strengthening National Security :</strong> Unscrupulous agents facilitate illegal immigration, compromising national security by enabling organized crime and trafficking.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i> <strong>Safeguarding Public Health and Social Services :</strong>  Illegal immigration strains health and social systems, leaving trafficked individuals without proper care.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i> <strong>Preventing Human Rights Violations :</strong>  These networks deny victims their basic rights, subjecting them to forced labor, withheld wages, and violence.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i> <strong>Reducing Economic Exploitation :</strong>  Trafficked individuals are coerced into low-paying jobs, depressing wages and conditions; exposing these practices protects fair labor standards.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i> <strong>Promoting Community Safety :</strong>  Criminal elements involved in trafficking increase local crime rates, compromising community safety.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i> <strong>Preserving the Integrity of Immigration Processes :</strong> Unethical actors erode trust in immigration systems; exposing them restores faith in fair and transparent processes.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i> <strong>Dismantling the Business Model of Exploitation :</strong> Exposing traffickers cuts off financial incentives, weakening their operations.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i> <strong>Protecting Legal Migrants :</strong>  Unethical networks stigmatize legal immigrants; exposing these networks clarifies fair immigration practices.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i> <strong>Empowering Vulnerable Populations :</strong>  Shedding light on exploitation empowers marginalized groups to avoid exploitation and find safe, legitimate pathways.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i> <strong>Raising Awareness and Building Community Vigilance :</strong>  Public knowledge of risks associated with illegal immigration and unethical practitioners fosters community vigilance and protection for potential victims.</li>
                                                </ul>
                                                <h5>How Do We Combat This?</h5>
                                                <p>TrustVisory is a <strong> non-profit agency</strong> dedicated to creating awareness and alert systems for the public regarding such individuals, entities, and employers. We aim to bring awareness to job seekers before they fall prey to exploitative employers, recruitment agencies, and professionals who engage in unethical practices. At TrustVisory, we are committed to ending this exploitation through the following initiatives:</p>
                                                <h6>Advanced Technology Utilization:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li> Implementing cutting-edge AI and machine learning algorithms to analyze vast amounts of data.</li>
                                                    <li>  Using advanced software to track and monitor suspicious activities in real-time.</li>
                                                </ul>
                                                <h6>Data Analytics:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li> Uncovering the identities and operational details of human traffickers and illegal immigration facilitators.</li>
                                                    <li>  Compiling evidence-based profiles of unethical actors.</li>
                                                </ul>
                                                <h6>Centralized Database Creation:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li> Establishing a comprehensive, centralized database to catalog identified unethical actors.</li>
                                                    <li> Ensuring the database is regularly updated with verified information.</li>
                                                </ul>
                                                <h6>Public Accessibility:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li>  Making the database accessible to the public, enabling individuals to verify the legitimacy of immigration practitioners.</li>
                                                    <li> Providing an easy-to-use interface for the public to search and review profiles of identified unethical actors.</li>
                                                </ul>
                                                <h6>Empowering Informed Decisions:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li>  Educating the public on the importance of using verified and ethical immigration practitioners.</li>
                                                    <li>  Offering resources and guidance on how to identify and avoid fraudulent operators.</li>
                                                </ul>
                                                <h6>Community Engagement:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li> Encouraging the community to report suspicious activities and practitioners.</li>
                                                    <li> Collaborating with other organizations and stakeholders to strengthen the fight against unethical practices.</li>
                                                </ul>
                                                <h5>Our Mission</h5>
                                                <p>Our mission goes beyond protecting individuals; we aim to protect the integrity of immigration systems and the safety of communities. By highlighting these dangerous actors and educating the public, TrustVisory is fighting for an immigration system that is safe, transparent, and fair.
                                                </p>
                                                <p>This initiative is a commitment to ensuring that every individual’s journey toward a better life remains dignified, free from exploitation, and protected from those who would turn hope into a commodity.
                                                </p>
                                                <p>Together, we can dismantle these networks, defend the vulnerable, and build a future where immigration upholds integrity, humanity, and justice.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-12 col-lg-7 mt-4 mt-lg-0">
                            <div class=" w-100">
                                <div class="wizard">
                                    {{-- # --}}
                                    <div class="tab-content " id="myTabContent">
                                        {{-- @include("support.thank-you-support") --}}
                                        <div class="tab-pane fade show active " role="tabpanel" id="step1" aria-labelledby="step1-tab">
                                            <div class="text-end complete-step">
                                                <span class="text-success">
                                                <i class="fa-duotone fa-solid fa-badge-check fa-2x"></i>
                                                </span>
                                            </div>
                                            <div class="cds-t35-content-support-form-segments card-detail inner-data">
                                                <div id="card-detail-error" class="text-danger"></div>
                                                <div class="cds-t35-content-support-form-segments-header">
                                                    <h2 class="content-heading">
                                                        <span class="step-information"> Step 1</span>
                                                        Please Choose Your Support Amount
                                                    </h2>
                                                    <p>
                                                        You can  support us for the initiative and pay the amount as per your wish.
                                                    </p>
                                                </div>
                                                <div class="cds-t35-content-support-form-segments-body">
                                                    <div class="text-success">
                                                    </div>
                                                    <div class="cds-t35-content-support-form-segments-body-amount-list-wrap">
                                                        @foreach(supportAmount('INR') as $amount)
                                                        <div class="cds-t35-content-support-form-segments-body-amount-list js-form-message">
                                                            <input type="radio" id="amount{{$amount}}" name="amount" value="{{$amount}}" class="donation-option">
                                                            <label for="amount{{$amount}}" class="donation-label">₹{{$amount}}</label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="cds-t35-content-support-form-segments-body-other-amount-list-wrap">
                                                        <div class="cds-t35-content-support-form-segments-body-other-amount-list-wrap-list"> 
                                                            <input type="radio" id="otherAmount" name="amount" value="other"
                                                                class="donation-option">
                                                            <label for="otherAmount" class="donation-label">Other</label>
                                                        </div>
                                                        <div class="cds-t35-content-support-form-segments-body-other-amount-list-wrap-list cds-enteramount">
                                                            <div id="customAmountGroup" style="display: none;">
                                                                <input type="number" class="form-control" id="customAmount" placeholder="Enter Amount" name="custom_amount" min="1" oninput="validateSupportAmount(this)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="cds-t35-content-support-form-segments-body-note-section">
                                                        <p>
                                                            @if(currency() == 'CAD')
                                                            Amount displayed is in CAD  
                                                            @else
                                                            Amount displayed is in USD 
                                                            @endif
                                                        </p>
                                                    </span>
                                                </div>
                                                <div class="cds-t35-content-support-form-segments-footer">
                                                    <div class="cds-t35-content-support-form-segments-footer-btn-box">
                                                        <button type="button" onclick="validateForm('step1')" class="btn-royalblue next step-1-btn"><span>Continue</span> <i class="fas fa-angle-right"></i></button>                                            
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- # --}}
                                        <div class="tab-pane fade donation-step2 disabled-form " role="tabpanel" id="step2" aria-labelledby="step2-tab">
                                            <div class="text-end complete-step">
                                                <span class="text-success">
                                                <i class="fa-duotone fa-solid fa-badge-check fa-2x"></i>
                                                </span>
                                            </div>
                                            <div class="cds-t35-content-support-form-segments card-detail  inner-data ">
                                                <div class="cds-t35-content-support-form-segments-header">
                                                    <h2 class="content-heading">
                                                        <span class="step-information">Step 2</span>
                                                        Please Provide Supporter's Information
                                                    </h2>
                                                    <p> Thank You for selecting ! Every dollar contributed will bring a positive change. </p>
                                                </div>
                                                <div class="cds-t35-content-support-form-segments-body">
                                                    <div class="row google-address-area">
                                                        <div class="col-xl-12">                                                            
                                                            <div class="form-check form-check-inline cds-gender-list me-0 mt-3 mt-md-0 p-0 donation-type"> 
                                                                <label class="form-label">How can you identify you? <span class="required-asterisk">*</span></label>
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
                                                            {{-- <div class="js-form-message">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input required" checked type="radio" name="donation_type" id="personal" value="personal" required>
                                                                    <label class="form-check-label" for="personal">Personal</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input required" type="radio" name="donation_type" id="corporate" value="corporate">
                                                                    <label class="form-check-label" for="corporate">Corporate</label>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                        <div class="col-xl-12 mb-3">
                                                            <div class="corporate-name" style="display:none">
                                                                {!! FormHelper::formInputText([
                                                                    "label"=>"Company Name",
                                                                    'name'=>"company_name",
                                                                    'id'=>"company-name",
                                                                    'disabled'=>true,
                                                                ]) !!}
                                                                {{-- <div class="col js-form-message">
                                                                    <label for="first_name" class="form-label">Company Name *</label>
                                                                    <input type="text" class="form-control" disabled id="company-name" name="company_name" required>
                                                                </div> --}}
                                                            </div>
                                                        </div>
                                                        {{-- Name --}}
                                                        <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                                            {!! FormHelper::formInputText([
                                                                "label"=>"First Name",
                                                                'name'=>"first_name",
                                                                'id'=>"first_name",
                                                                'required'=>true,
                                                                'events'=>['oninput=validateName(this)'],
                                                            ]) !!}
                                                            {{-- <div class="col js-form-message">
                                                                <label for="first_name" class="form-label">First Name *</label>
                                                                <input type="text" class="form-control required" id="first_name" name="first_name" required oninput="validateName(this)">
                                                            </div> --}}
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                                            {!! FormHelper::formInputText([
                                                                "label"=>"Last Name",
                                                                'name'=>"last_name",
                                                                'id'=>"last_name",
                                                                'required'=>true,
                                                                'events'=>['oninput=validateName(this)'],
                                                            ]) !!}
                                                            {{-- <label for="last_name" class="form-label">Last Name *</label>
                                                            <input type="text" class="form-control required" id="last_name" name="last_name"
                                                                required oninput="validateName(this)"> --}}
                                                        </div>
                                                        {{-- Mailing Address --}}
                                                        <div class="col-xl-12 mb-3">
                                                            {!! FormHelper::formInputText([
                                                                "label"=>"Address",
                                                                'name'=>"address",
                                                                'id'=>"address",
                                                                'input_class'=>"google-address address",
                                                                'required'=>true,
                                                            ]) !!}
                                                            {{-- <label for="address" class="form-label">Address *</label>
                                                            <input type="text" class="form-control required address google-address" id="address" name="address"
                                                                required> --}}
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                                            {!! FormHelper::formInputText([
                                                                "label"=>"City",
                                                                'name'=>"city",
                                                                'id'=>"city",
                                                                'input_class'=>"ga-city city",
                                                                'required'=>true,
                                                                'events'=>['oninput=validateName(this)'],
                                                            ]) !!}
                                                            {{-- <label for="city" class="form-label">City *</label>
                                                            <input type="text" class="form-control required city" id="city" name="city" required oninput="validateName(this)"> --}}
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                                            {!! FormHelper::formInputText([
                                                                "label"=>"State/Province",
                                                                'name'=>"state",
                                                                'id'=>"state",
                                                                'input_class'=>"ga-state state",
                                                                'required'=>true,
                                                                'events'=>['oninput=validateName(this)'],
                                                            ]) !!}
                                                            {{-- <label for="state" class="form-label">State/Province *</label>
                                                            <input type="text" class="form-control required state" id="state" name="state"
                                                                required oninput="validateName(this)"> --}}
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                                            {!! FormHelper::formInputText([
                                                                "label"=>"Postal Code",
                                                                'name'=>"zip",
                                                                'id'=>"zip",
                                                                'input_class'=>"ga-pincode zip",
                                                                'required'=>true,
                                                                'events'=>['oninput=validateZipCode(this)'],
                                                            ]) !!}
                                                            {{-- <label for="zip" class="form-label">Postal Code *</label>
                                                            <input type="text" class="form-control required zip" id="zip" name="zip" required oninput="validateZipCode(this)"> --}}
                                                        </div>
                                                        <div class="col-xl-6 col-md-6 col-lg-6 mb-3">
                                                            {!! FormHelper::formInputText([
                                                                "label"=>"Country",
                                                                'name'=>"country",
                                                                'id'=>"country",
                                                                'input_class'=>"ga-country country",
                                                                'required'=>true,
                                                                'events'=>['oninput=validateName(this)'],
                                                            ]) !!}
                                                            {{-- <label for="country" class="form-label">Country *</label>
                                                            <input type="text" class="form-control required country" id="country" name="country" required oninput="validateName(this)"> --}}
                                                        </div>
                                                        {{-- Email --}}
                                                        <div class="col-xl-12 mb-3">
                                                            {!! FormHelper::formInputEmail([
                                                                "label"=>"Email",
                                                                'name'=>"email",
                                                                'id'=>"email",
                                                                'required'=>true,
                                                            ]) !!}
                                                            {{-- <label for="email" class="form-label">Email *</label>
                                                            <input type="email" class="form-control required" id="email" name="email"
                                                                required> --}}
                                                        </div>
                                                        {{-- Phone Number --}}
                                                        <div class="col-xl-12">
                                                            {!! FormHelper::formPhoneNo([
                                                                "label" => "Phone Number (optional)",
                                                                'name' => "phone",
                                                                'id' => "phone",
                                                                'class' => "phone_no",
                                                                'country_code_name' => "country_code",
                                                                "value" => $user->country_code ?? '',
                                                                "default_country_code"=>$user->country_code ?? '+1',
                                                                "required" => true,
                                                                'events'=>['oninput=validatePhoneInput(this)']]
                                                            ) !!}

                                                            {{-- <div class="col js-form-message phoneno-with-code">
                                                                <label for="phone" class="form-label">Phone Number (optional)</label>
                                                                <input type="tel" class="form-control phone_no" id="phone" name="phone"
                                                                    placeholder="Enter phone number" oninput="validatePhoneInput(this)">
                                                                <input type="hidden" class="required country_code" name="country_code" id="user_country_code"  value="{{ $user->country_code??'+1' }}" placeholder="Your phone number" aria-label="Phone Number">
                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="cds-t35-content-support-form-segments-footer">
                                                    <div class="cds-t35-content-support-form-segments-footer-btn-box"><button type="button" onclick="validateForm('step2')" class="btn-royalblue next btn"><span>Continue</span> <i class="fas fa-angle-right"></i></button></div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- # --}}
                                        <div class="tab-pane fade disabled-form " role="tabpanel" id="step3" aria-labelledby="step3-tab">
                                            <div class="text-end complete-step">
                                                <span class="text-success">
                                                <i class="fa-duotone fa-solid fa-badge-check fa-2x"></i>
                                                </span>
                                            </div>
                                            <div class="cds-t35-content-support-form-segments card-detail" id="card-detail">
                                                <div class="cds-t35-content-support-form-segments-header">
                                                    <h2 class="content-heading">
                                                        <span class="step-information">Step 3</span>
                                                        Payment
                                                    </h2>
                                                </div>
                                                <div class="cds-t35-content-support-form-segments-body">
                                                    <div class="cds-t35-content-support-form-segments-body-total">
                                                        <div class="cds-t35-content-support-form-segments-body-total-header">
                                                            <h5>Total  <span class="amount-paid"></span></h5>
                                                            <span>One Time Purchase</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="js-form-message grecaptcha">
                                                        <div class="google-recaptcha"></div>
                                                    </div>
                                                </div>
                                                <div class="cds-t35-content-support-form-segments-footer">
                                                    <div class="cds-t35-content-support-form-segments-footer-btn-box">
                                                        <div class="js-form-message">
                                                            <input type="hidden" value="" name="amount_to_pay" id="amount-to-pay" />
                                                        </div>
                                                        <button type="button" onclick="validateForm('step3')" disabled id="submit-btn" class="btn-royalblue submit-btn"><span>Submit</span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
@endsection
@section('javascript')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var paymentMethod = '';
    $(document).ready(function(){
        googleRecaptcha();
        // $("#payment-form").submit(function(e){
        //     	e.preventDefault();
        //         handlePayment(paymentMethod);
        // });
        $('input[name="amount"]').change(function(){
            $(".step-1-btn").find("span").html("Update");
            if($(this).val() !=  'other'){
                if($(this).val() != '' && $("#amount-to-pay").val() != '' && $(this).val()){
                    $(".step-1-btn").find("span").html("Update");
                }else{
                    $(".step-1-btn").find("span").html("Continue");
                }   
            }else{
                $(".step-1-btn").find("span").html("Continue");
            }

        });
        $('input[name="donation_type"]').change(function(){
            if($(this).val() !=  'corporate'){
                $(".corporate-name").hide();
                $(".corporate-name").find("input").attr("disabled","disabled");
                $(".corporate-name").find("input").removeAttr("required");
                
            }else{
                $(".corporate-name").show();
                $(".corporate-name").find("input").removeAttr("disabled");
                $(".corporate-name").find("input").attr("required",true);
            }
        });
        
        $(document).on("keyup","#customAmount",function(){
            // if($("#customAmount").val() != ''){
            //     $(".amount-paid").html("Pay {{currencySymbol()}}"+$(this).val());
            // }else{
            //     $(".amount-paid").html("");
            // }
            $(".step-1-btn").find("span").html("Update");
            if($("#customAmount").val() != ''){
                if($(this).val() != '' && $("#amount-to-pay").val() != '' && $(this).val()){
                    $(".step-1-btn").find("span").html("Update");
                }else{
                    $(".step-1-btn").find("span").html("Continue");
                }   
            }else{
                $(".step-1-btn").find("span").html("Continue");
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
    // Show custom amount field when "Other Amount" is selected
    document.getElementById('otherAmount').addEventListener('change', function () {
      document.getElementById('customAmountGroup').style.display = 'block';
    });

    // Hide custom amount field for predefined options
    document.querySelectorAll('input[name="amount"]').forEach(function (element) {
      element.addEventListener('change', function () {
        if (this.value !== 'other') {
          document.getElementById('customAmountGroup').style.display = 'none';
        }
      });
    });

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
            if(!$('input[name="donation_type"]:checked').val()){
                $('.donation-type').find(".required-error").remove();
                var errmmsg ='<div class="required-error text-danger">This field is required</div>';
                $('.donation-type').append(errmmsg);
                is_valid = false;
            }
            if(!is_valid){
                return false;
            }
            if($("#amount-to-pay").val() == 0 || $("#amount-to-pay").val() == ''){
                errorMessage("Amount is invalid");
                $("#submit-btn").attr("disabled","disabled");
                return false;
            }else{
                $("#submit-btn").removeAttr("disabled");
            }

            $("#step3").removeClass("disabled-form");
            $("#step3").addClass("show active");
            $('html,body').animate({
                scrollTop: $("#step3").offset().top},
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

    function prevStep(step){
        var activeTab = $('.nav-tabs .active');
        var prevTab = activeTab.parent().prev().find('a.nav-link');
        prevTab.tab('show');
    }

    async function submitPayment(){
        var amount_to_pay = $("#amount-to-pay").val();
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
                        amount: amount_to_pay,   
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
                "key": "{{ config('services.razorpay.key') }}",
                "amount": order.amount,
                "currency": order.currency,
                "name": "Trustvisory",
                "description": "Paid for supporting initiatives",
                "order_id": order.order_id,
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
                            amount:order.amount,
                            billing_address:$("#address").val(),
                            country:$("#country").val(),
                            state:$("#state").val(),
                            city:$("#city").val(),
                            pincode:$("#zip").val()
                        })
                    }).then(res => res.json())
                    .then(data => {
                        $("#myTabContent").html(data.contents);
                        $('html,body').animate({
                            scrollTop: $("#myTabContent").offset().top},
                        'slow');
                        // window.location.href = 'http://127.0.0.1:8000/';
                    })
                    .catch(err => {
                        console.error("Error Response:", err);
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
  </script>

<script>
$(document).ready(function() {
    // Handle the "same as mailing address" checkbox click event
    $('.sameAsMailing').on('click', function() {
        if ($(this).prop('checked')) {
            // Add class 'focused' to all relevant inputs
            $('.cds-samedata').each(function() {
                $(this).addClass('focused'); // Add the 'focused' class
            });
        } else {
            // Remove class 'focused' and clear values
            $('.cds-samedata').each(function() {
                $(this).val(''); // Clear the value
                $(this).removeClass('focused'); // Remove the 'focused' class
            });
        }
    });
});
</script>

@endsection
