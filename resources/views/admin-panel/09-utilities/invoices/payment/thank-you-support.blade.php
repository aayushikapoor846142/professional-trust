@extends('layouts.app')
@section('content')
<section class="cdsTYMainsite-confirmation-thanks-page">
    <div class="container">
        <div class="row"><div class="cdsTYMainsite-confirmation-thanks-page-outer-container">
        <div class="cdsTYMainsite-alert-section alert alert-info">  <img src="{{url('/')}}/assets/images/d-icons/email.png"  /><b>Note:</b> Please check your spam folder for notification emails if not showing in inbox.</div>
            <div class="cdsTYMainsite-confirmation-thanks-page-container">
                <div class="cdsTYMainsite-confirmation-thanks-page-container-header">
                    <div id="confetti-container" style="width:175px; height:175px; margin:0 auto;"></div>
                </div>
                
                <p id="countdown-text" class="text-center text-bg-warning">Redirecting in <span id="countdown">60</span> seconds...</p>
                <div class="cdsTYMainsite-confirmation-thanks-page-container-body">
                    <h3><b>Dear {{ $name }},</b></h3>
					<p><b>Thank You For Your Support! </b></p>
                    @if($payment_type == 'Monthly')
                    <p><b>${{ $totalAmount }}</b> has been received as from you as monthly subscription for which you will be earning {{ $totalPoints }} points.</p>
                    @else
                    <p><b>${{ $totalAmount }}</b> has been received as from you for which you have earned <b>{{ $totalPoints }} </b> points.</p>
                    @endif
                    
                    @if(!auth()->check())
                    <p>You can <a href="{{ url('/login') }}">login</a> and check the points earn history</p>
                    @endif

                    <p>Click here to 
                        @if(auth()->check())
                            <a href="{{ baseUrl('/') }}"> continue </a>
                        @else
                            @if($back_to_support != '')
                                <a href="{{ $back_to_support }}"> continue </a>
                            @else
                                <a href="{{ url('professional/support') }}"> continue </a>
                            @endif
                        @endif
                    </p>
                </div>
                <div class="cdsTYMainsite-confirmation-thanks-page-container-footer">
                    
                </div>
            </div> </div>
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
            path: '{{ url('assets/plugins/animation/confetti.json')  }}' // Make sure this path is correct
        });
        confettiAnimation.goToAndPlay(0, true);
        setTimeout(() => {
            confettiAnimation.goToAndPlay(0, true);
        }, 1500);

         // Countdown logic
        // let seconds = 60;
        // const countdownElement = document.getElementById('countdown');
        // const countdownInterval = setInterval(() => {
        //     seconds--;
        //     if (seconds >= 0) {
        //         countdownElement.textContent = seconds;
        //     }
        //     if (seconds === 0) {
        //         clearInterval(countdownInterval);
        //         // Redirect after countdown ends
        //         @if(auth()->check())
        //             window.location.href = "{{ baseUrl('/') }}";
        //         @else
        //             @if($back_to_support != '')
        //                 window.location.href = "{{ $back_to_support }}";
        //             @else
        //                 window.location.href = "{{ url('/professional/support') }}";
        //             @endif
                    
        //         @endif
        //     }
        // }, 1000);
    })
</script>
@endsection
