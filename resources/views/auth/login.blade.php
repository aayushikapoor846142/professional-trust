@extends('layouts.app')

@section('content')
<div id="layoutAuthentication" data-cover="{{ url('assets/images/covers/cover2.jpeg') }}">
    <div id="loader-text">Loading...</div>
    <div id="layoutAuthentication_content">
        <div class="cdsTYMainsite-auth-form-wrapper cds-login-form">
            <div class="cdsTYMainsite-auth-form-side-container">
                <div class="cdsTYMainsite-auth-form-wrap">
                    
                    <div class="cdsTYMainsite-login-form login-form">
                        <div class="cdsTYMainsite-login-form-container-header"><span>Welcome to Trustvisory</span></div>

                        <div class="cdsTYMainsite-login-form-container-body">
                                <div class="login-via">
                            <a href="{{ url(  mainTrustvisoryUrl() .'/auth/google') . '?check_type=login' . (request()->has('redirect_back') ? '&redirect_back=' . urlencode(request()->get('redirect_back')) : '') }}" 
                                class="email-login"> <img src="{{ url('assets/images/icons/google.svg') }}" alt="Google Login" class="gLogo img-fluid" />
                                <span class="ms-3">Sign in with Google</span>
                            </a>

                
                                <br>
                                 <a href="{{ url(  mainTrustvisoryUrl() .'/auth/linkedin') . '?check_type=login' . (request()->has('redirect_back') ? '&redirect_back=' . urlencode(request()->get('redirect_back')) : '') }}" class="email-login">
                                    <img src="{{ url('assets/images/icons/linkedin.svg') }}" alt="Google Login" class="img-fluid">
                                    <span class="ms-3">Sign in With Linkedin</span>
                                </a> 

                                
                                <br>
                            </div>

                            <div class="cds-form-container">
                                <form id="form" method="POST" action="{{ route('login') }}">
                                    <input type="hidden" name="redirect_back" value="{{request()->query('redirect_back')}}" />
                                    <input type="hidden" name="type" id="type" />
                                    <input type="hidden" name="unique_id" id="unique_id" />
                                    @csrf @if (session('message'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('message') }}
                                    </div>
                                    @endif
                                    <div class="cdsTYMainsite-form-floating form-floating">
                                        {!! FormHelper::formInputEmail([
                                            'name' => "email",
                                            'label' => "Email address",
                                            'required' => true,
                                           
                                        ]) !!}
                                    </div>

                                    <div class="cdsTYMainsite-form-floating pass-box">
                                        {!! FormHelper::formPassordText(['name'=>"password","label"=>"Password","required"=>true]) !!}
                                    </div>
                                    <div class="cdsTYMainsite-form-floating form-floating">
                                        <div class="col-md-12 js-form-message">
                                            <div class="google-recaptcha"></div>
                                        </div>
                                        @if ($errors->has('g-recaptcha-response'))
                                        <span class="invalid-feedbacks" role="alert" style="width: 100%; margin-top: 0.25rem; font-size: 0.875em; color: #dc3545;">
                                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="cdsTYMainsite-form-floating">
                                        <div class="cdsTYMainsite-form-floating-segment">
                                            {!! FormHelper::formCheckbox(['name' => "remember", 'value' => 1, 'id' => "remember",'required' => true]) !!}
                                            <label for="remember">Remember Password</label>
                                        </div>
                                        <div class="cdsTYMainsite-form-floating-segment cdsTYMainsite-form-floating-segment-end">
                                            <a class="small" href="{{ route('password.request') }}">Forgot Password?</a>
                                        </div>
                                    </div>
                                    {{--
                                    <div class="form-check mb-3 ps-0 d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember','true') ? 'checked' : '' }} />
                                        <label class="form-check-label ps-2" for="inputRememberPassword">Remember Password</label>
                                    </div>
                                    --}}
                                    <div class="text-center">
                                        <button type="button" class="CdsTYButton-btn-primary signin-btn">Login</button>
                                    </div>
                                    {{--
                                    <div class="t-4 text-center">
                                        <div class="my-4">Don't have an account?</div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <a href="{{ route('user.register') }}">Join as Client</a>
                                            <a href="{{ route('professional.register') }}">Join as Professional</a>
                                        </div>
                                    </div>
                                    --}}
                                </form>
                            </div>
                        </div>
                        {{--
                        <div class="cdsTYMainsite-login-form-container-footer text-center py-3">
                            <div class="small"><a href="{{ route('user.register') }}">Need an account? Sign up!</a></div>
                        </div>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('javascript')
<script>
toastr.options = {
    containerId: "toast-container",  // ✅ Ensures the container ID is valid
    timeOut: 1000000,  // 10 seconds before disappearing
    extendedTimeOut: 50000,  // 5 seconds on hover
    positionClass: "toast-full-width-top",
    progressBar: true,  // Show progress bar
    closeButton: true  // Show close button
};

</script>

<script>function setCoverImage() {
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
<script>
    // $(document).ready(function () {
    //     googleRecaptcha();
    //     $('.btn-show-pass').click(function () {
    //         // Find the closest input of type "password" in the wrap-input div
    //         var $input = $(this).closest('.wrap-input').find('input[type="password"], input[type="text"]');

    //         // Toggle input type and icon class
    //         if ($input.attr('type') === 'password') {
    //             $input.attr('type', 'text'); // Show password
    //             $(this).find('.eye-pass').removeClass('flaticon-visibility').addClass('flaticon-invisible'); // Change icon
    //         } else {
    //             $input.attr('type', 'password'); // Hide password
    //             $(this).find('.eye-pass').removeClass('flaticon-invisible').addClass('flaticon-visibility'); // Change icon back
    //         }
    //     });
    // });
    $(document).ready(function () {
        $(".signin-btn").click(function(e) {
            const urlParams = new URLSearchParams(window.location.search);
            const source = urlParams.get('type'); 
            document.getElementById('type').value = source;
            const source1 = urlParams.get('unique_id'); 
            document.getElementById('unique_id').value = source1;

            e.preventDefault();
            $(".signin-btn").attr("disabled", "disabled");
            $(".signin-btn").find('.fa-spin').remove();
            $(".signin-btn").append("<i class='fa fa-spin fa-spinner ms-2'></i>");

            var formData = $("#form").serialize();
            
            $.ajax({
                url: $("#form").attr("action"),
                type: "post",
                data: formData,
                dataType: "json",
                beforeSend: function() {

                },
                success: function(response) {
                    $(".signin-btn").find(".fa-spin").remove();
                    $(".signin-btn").removeAttr("disabled");
                    if (response.status == true) {
                        window.location.href = response.redirct_url;
                    } else {
                        
                        if (response.error_type == 'validation') {
                            validation(response.message);
                        }else{
                            errorMessage(response.message);
                        } 
                    }
                },
                error: function(xhr) {
                    $(".signin-btn").find(".fa-spin").remove();
                    $(".signin-btn").removeAttr("disabled");
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
            validation(xhr.responseJSON.message);
        } else {
            errorMessage('An unexpected error occurred. Please try again.');
        }
                }
            });
        });
    });
</script>

@endsection