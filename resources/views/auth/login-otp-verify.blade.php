@extends('layouts.app', ['pageTitle' => 'OTP Verification'])

@section('content')
<div id="layoutAuthentication" data-cover="{{ url('assets/images/covers/cover2.jpeg') }}" >
    <div id="layoutAuthentication_content">
        <div id="loader-text">Loading...</div>
        <div class="cdsTYMainsite-auth-form-wrapper cds-login-form">
            <div class="cdsTYMainsite-auth-form-side-container">
                <div class="cdsTYMainsite-auth-form-wrap">
                    <div class="cdsTYMainsite-login-form login-form">
                        <div class="cdsTYMainsite-login-form-container-header">
                            <span>Verify Otp</span>
                        </div>
                        <div class="cdsTYMainsite-login-form-container-body">
                            <form method="post" id="otp-form" class="validate text-left">
                                @csrf
                                <input type="hidden" name="otp_token" value="{{$token}}" />
                                <input type="hidden" name="email" value="{{$email}}" />
                                <div class="row mb-3">
                                    <!-- <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('First Name') }}<span class="text-danger">*</span></label> -->
                                    <div class="col-md-12 js-form-message">
                                        <div class="cds-inputotp">
                                            <input type="text" class="form-control m-1 otp-input" name="otp1" maxlength="1" oninput="validateDigit(this)" />
                                            <input type="text" class="form-control m-1 otp-input" name="otp2" maxlength="1" oninput="validateDigit(this)" />
                                            <input type="text" class="form-control m-1 otp-input" name="otp3" maxlength="1" oninput="validateDigit(this)" />
                                            <input type="text" class="form-control m-1 otp-input" name="otp4" maxlength="1" oninput="validateDigit(this)" />
                                            <input type="text" class="form-control m-1 otp-input" name="otp5" maxlength="1" oninput="validateDigit(this)" />
                                            <input type="text" class="form-control m-1 otp-input" name="otp6" maxlength="1" oninput="validateDigit(this)" />
                                        </div>
                                    </div>
                                    <div class="mt-3 text-end">
                                        <button type="button" onclick="submitForm(this,'Resent Otp')" class="CdsTYButton-btn-primary w-auto last-link ml-10" id="resendOtpButton">Resend Otp</button>
                                        <div class="d-flex justify-content-center otp-timer">
                                            <div><b>Time Left :</b></div>
                                            <div id="countdown" class="text-center"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 d-flex gap-2 justify-content-center mt-3">
                                        <a href="{{ url('login')  }}" class="CdsTYButton-btn-primary previous d-inline-block"> Back </a>
                                        <button type="button" class="CdsTYButton-btn-primary w-auto" onclick="verifyOtp(this)">Verify</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section("javascript")
<script>
// submit otp verification form 
        // submit signup 
   
$(document).keypress(function (event) {
    if (event.which == '13') {
        event.preventDefault();
    }
});

const targetTime = 120;
let timeLeft;

// Retrieve remaining time from localStorage or use the initial time
if (localStorage.getItem('timeLeft')) {
    timeLeft = parseInt(localStorage.getItem('timeLeft'), 10); // Parse time from localStorage
    if (isNaN(timeLeft)) { // If timeLeft is NaN, reset it
        timeLeft = targetTime;
    }
} else {
    timeLeft = targetTime;
}


function submitForm(e, text) {

    // disableResendButton();
    var formData = $("#otp-form").serialize();
    if (text == 'Resent Otp') {
        formData += "&type=resend_otp";
        formData += "&token={{$token}}";
        var requestUrl = "{{ $send_otp_url }}?type=resend_otp";
    } else {
        formData += "&type=send_otp";
        var requestUrl = "{{ $send_otp_url }}?type=send_otp";
    }

    $.ajax({
        url: requestUrl,
        type: "post",
        data: formData,
        dataType: "json",
        beforeSend: function () {
            showLoader();
            $(e).attr("disabled", "disabled");
            $(e).html("<i class='fa fa-spin fa-spinner'></i>");
        },
        success: function (response) {
            hideLoader();
            $(e).removeAttr("disabled");
            $(e).html(text);
            if (response.status == true) {
                successMessage(response.message);
                if (text == 'Resent Otp') {
                    setTimeout(() => {
                        location.reload();
                    }, 2500);
                }
            } else {
               
                if (response.error_type == 'validation') {
                    validation(response.message);
                } else{
                    if(response.redirect_back !== undefined){
                        window.location.href = response.redirect_back;
                    }else{
                        errorMessage(response.message);
                    }
                }   
            }
        },
        error: function () {
            internalError();
        }
    });
}

function disableResendButton() {
    const resendButton = document.getElementById('resendOtpButton');

    // Disable the button
    resendButton.disabled = true;
    resendButton.style.pointerEvents = 'none';  // Prevent any interaction
    resendButton.style.opacity = '0.5';  // Dim the button to indicate it's disabled

    // Set a timeout to re-enable the button after 50 seconds
    setTimeout(() => {
        resendButton.disabled = false;
        resendButton.style.pointerEvents = 'auto';  // Re-enable interaction
        resendButton.style.opacity = '1';  // Restore button opacity
        resendButton.innerHTML = "Resend OTP";
    }, 50000); // 50 seconds in milliseconds
}


function verifyOtp(e) {

    // if ($(".otp-code").val() == '') {
    //     errorMessage("Otp cannot be blank");
    //     return false;
    // }
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: "{{$verify_otp_url}}",
        method: 'POST',
        data: $("#otp-form").serialize(),
        dataType: "json",
        beforeSend: function () {
            showLoader();
            $(e).attr("disabled", "disabled");
            $(e).html("<i class='fa fa-spin fa-spinner'></i>");
        },
        success: function (response) {
            hideLoader();
            $(e).removeAttr("disabled");
            $(e).html("Submit");
            if (response.status == true) {
                window.location.href = response.url;
            } else {

                if(response.error_type){
                    validation(response.message);
                }else{
                   
                    if(response.redirect_back !== undefined){
                        window.location.href = response.redirect_back;
                    }else{
                        errorMessage(response.message);
                    }
                }   

                
            }
        },
        error: function () {
            internalError();
        }
    });
}

function goBack(e) {
    $(".otp-form").hide();
    $(".register-form").show();
}


</script>
<script>
    const inputs = document.querySelectorAll('.otp-input');

    // Function to handle paste and distribute characters
    const handlePaste = (event) => {
        event.preventDefault();
        const pasteData = event.clipboardData.getData('text');

        if (pasteData.length === inputs.length) {
            inputs.forEach((input, index) => {
                input.value = pasteData[index] || '';
            });
        }
    };

    // Add event listener for paste to the first input
    inputs[0].addEventListener('paste', handlePaste);
    // Move focus to the next input on keyup
    inputs.forEach((input, index) => {
        input.addEventListener('keyup', (event) => {
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        // Handle backspace
        input.addEventListener('keydown', (event) => {
            if (event.key === "Backspace" && input.value === "") {
                if (index > 0) {
                    inputs[index - 1].focus();
                    inputs[index - 1].value = ''; // Clear the previous input
                }
            }
        });
    });
    // Set your expiry time in 'Y-m-d H:i:s' format
    const expiryTimeStr = '{{convertToUserTimezone($otpVerify->otp_expiry_time,$timezone)}}'; // Replace this with your expiry time
   
    
    // Convert expiry time to a Date object
    const expiryTime = new Date(expiryTimeStr.replace(' ', 'T')).getTime();
    function updateCountdown() {
      var timezone = "{{$timezone}}";
      var now = new Date().getTime();
      if(timezone == 'UTC'){
        var nowUTC = new Date().toISOString();
        now = new Date(nowUTC.replace('Z', '')).getTime();
      }
      const timeLeft = expiryTime - now;
      // Calculate minutes and seconds remaining
      const minutes = Math.floor(timeLeft / (1000 * 60));
      const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
 
      // Display countdown in MM:SS format
      document.getElementById("countdown").innerHTML = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
      
      // Check if time has expired
      if (timeLeft < 0) {
        clearInterval(countdownInterval);
        document.getElementById("countdown").innerHTML = "OTP Expired";
      }
    }
 
    // Update countdown every second
    const countdownInterval = setInterval(updateCountdown, 1000);
 
    // Initial call to display countdown immediately
    updateCountdown();
    
</script>
<script>
    function setCoverImage() {
    const layout = document.getElementById('layoutAuthentication');
    const loaderText = document.getElementById('loader-text');
    const imageUrl = layout.getAttribute('data-cover');

    if (imageUrl) {
        // Create a new Image object to preload
        const img = new Image();
        img.src = imageUrl;
        
        img.onload = function () {
            layout.style.backgroundImage = `url("${imageUrl}")`;
            layout.style.animation = 'none'; // Stop the background animation
            layout.style.transition = 'background 1.5s ease-in-out'; // Smooth transition
            layout.style.backgroundSize = 'cover';

            // Fade out loader text
            if (loaderText) {
                loaderText.style.opacity = '0';
                setTimeout(() => loaderText.remove(), 500);
            }
        };
    }
}

// Run on page load
window.addEventListener('load', setCoverImage);

// Adjust on resize
window.addEventListener('resize', setCoverImage);


</script>
@endsection
