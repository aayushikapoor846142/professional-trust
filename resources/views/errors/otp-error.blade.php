
@extends('layouts.app')
@section('content')

<section class="page_404 otp-errorSection">
    <div class="container">
    <div class="row justify-content-center">
            <div class="col-xl-4 col-sm-12 col-md-6 col-lg-5">
                <div class="cds-page-404-container-wrap cds-content">
                    <div class="cds-page-404-container-wrap-header">
                        <h1 class="text-center"></h1>
                    </div>
                    <div class="cds-page-404-container-wrap-body">
                        <div id="confetti-container" class="cds-timeCount"></div>
                        <div class="cdsTYMainsite-confirmation-thanks-page-container-body">
                            <h3 class="font28">
                                OTP link is expired
                            </h3>
                            <p class="m-0"><b>OTP has expired please try again.</b></p>
                            <p id="countdown-text" class="text-center text-bg-warning"><span id="countdown"></span></p>
                            <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                                <a href="{{ route('login') }}" class="CdsTYButton-btn-primary">Login</a>
                            </div>                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('javascript')
<script>
    $(document).ready(function(){
        const confettiAnimation = lottie.loadAnimation({
            container: document.getElementById('confetti-container'),
            renderer: 'svg',
            loop: false,
            autoplay: false,
            path: '{{ url('/assets/plugins/animation/time-count.json')  }}'
        });
        confettiAnimation.goToAndPlay(0, true);
        setTimeout(() => {
            confettiAnimation.goToAndPlay(0, true);
        });  
        //Countdown logic
        let seconds = 6;
        const countdownElement = document.getElementById('countdown');
        const countdownInterval = setInterval(() => {
            seconds--;
            // if (seconds >= 0) {
            //     countdownElement.textContent = seconds;
            // }
            if (seconds === 0) {
                clearInterval(countdownInterval);                
                window.location.href = "{{ url('/login') }}";
            }
        }, 1000);      
    })
</script>
@endsection
