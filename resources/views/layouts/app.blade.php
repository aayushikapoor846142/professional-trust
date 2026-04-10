<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
   
  
@if(Str::contains(request()->getHost(), 'trustvisory.com'))
		<!-- Google Tag Manager -->
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-W6V5N272');</script>
<!-- End Google Tag Manager -->
@endif
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

     <!-- Meta Title  -->
    <title>{{ $seoData->meta_title ?? 'TrustVisory' }}</title>
    <meta property="og:title" content="{{ $seoData->meta_title ?? 'TrustVisory' }}" />
    @if(!empty($seoData->meta_description))
        <meta name="description" content="{{ $seoData->meta_description ?? ''  }}">
        <meta property="og:description" content="{{ $seoData->meta_description ?? ''  }}" />
    @endif
    @if(!empty($seoData->meta_keywords))
        <meta name="keywords" content="{{ $seoData->meta_keywords ?? ''  }}">
    @endif
    <meta property="og:image" content="{{ url('assets/images/og_logo.png') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image:width" content="138" />
    <meta property="og:image:height" content="26" />

    <meta name="author" content="" />
    <base id="siteurl" href="{{ url('/') }}/" />
    <title>TrustVisory | {{ $pageTitle??'Home' }}</title>
    <link rel="icon" type="image/png" href="favicon.ico" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    
     <link href="{{url('assets/css/framework.css?v='.time()) }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{url('assets/plugins/dropzone/dropzone.min.css')}}">
    <link rel="stylesheet" href="{{ url('assets/plugins/select2/select2.min.css') }}">
    <link type="text/css" rel="stylesheet" href="{{url('assets/plugins/intl-tel-input/build/css/intlTelInput.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('assets/plugins/sweetalert2/sweetalert2.min.css')}}">

    <link href="{{url('assets/css/styles.css?v='.time()) }}" rel="stylesheet" />
    <link href="{{url('assets/css/h-file.css?v='.mt_rand()) }}" rel="stylesheet" />
    <link type="text/css" rel="stylesheet" href="{{url('assets/css/CDS-loader-styles.css')}}">
    <link type="text/css" rel="stylesheet" href="{{ url('assets/css/responsive.css?v='.time())}}">
    <link rel="stylesheet" href="{{url('assets/plugins/toastr/toastr.css') }}">
    <link href="{{url('assets/css/flatpickr.min.css') }}" rel="stylesheet" />
    <!-- font css  -->
    <!-- <link rel="stylesheet" href="{{ url('assets/css/all.min.css') }}"> -->
    
    <link href="assets/css/flaticon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- <script src="https://cdn.jsdelivr.net/npm/pusher-js@7.0.3/dist/web/pusher.min.js"></script> -->
    <!-- Include Echo via CDN -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.10.0/dist/echo.iife.js"></script> -->

    <script>
        var BASEURL = "{{ baseUrl('') }}";
        var SITEURL = "{{ url('') }}";
        var csrf_token = "{{ csrf_token() }}";
        var assetBaseUrl = "{{ asset('public') }}";
        var PSKEY = "{{ apiKeys('PUSHER_APP_KEY') }}";
            var PSCLS = "{{ apiKeys('PUSHER_APP_CLUSTER') }}";
    </script>
</head>
<body style="position:relative; font-family: 'Inter', system-ui;">
@if(Str::contains(request()->getHost(), 'trustvisory.com'))
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="
https://www.googletagmanager.com/ns.html?id=GTM-W6V5N272"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif
 <div class="cds-ty-dashboard-frame-content">
    <!-- Header -->
    @include('layouts.header')
	 <!-- Body -->
    
	  <main class="cds-ty-dashboard-frame-main-container" id="mainContainer">
        @yield('content')    
     </main>
  
    <!-- Footer -->
    @include('layouts.front-footer')
    <div id="popupModal" class="cdsTYDashboard-modal-standard"></div>
    <!-- <div class="modal fade" id="popupModal" tabindex="-1" role="dialog" aria-labelledby="popupModalLabel" aria-hidden="true"> </div>--> 
    <!-- Cookies Acceptance Code -->
    {{-- <div class="position-relative">
        <div id="cookiesCode" class="alert alert-warning alert-dismissible h6 fade show fixed-bottom bd-callout bd-callout-info" style="display: none;" role="alert">
            <button type="button" onclick="closecookie()" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            <div class="cookies-block">
                <i class="fa-solid fa-cookie"></i>
                <p>
                    We utilize cookies on this website to distinguish you from other users for an enhanced experience and targeted advertising. Your continued use of this website implies your consent for us to use cookies. Please refer to our
                    <a href="{{ url('page/cookie-policy') }}" target="_blank"><u>Cookie Policy</u></a>
                    for more details.
                </p>
            </div>
            <div class="cookie-btn-wrapper">
                <button id="accept-cookies" onclick="acceptCookies()" class="btn cookie-btn">Accept</button>
                <button id="reject-cookies" onclick="rejectCookies()" class="btn cookie-btn">Decline</button>
                <button id="reject-cookies" class="btn cookie-btn modal-link">Manage Options</button>
            </div>
        </div>
    </div>
        <!-- Modal for Cookies consent -->
        <!-- Modal  -->
        <div id="custom-modal" class="custom-modal">
            <div class="custom-modal-dialog">
                <div class="custom-modal-content">
                    <span class="close-modal">X</span>
                    <div class="custom-modal-body">
                        <div class="cookies-header">
                            <h5>Trust Visory Cookie Policy</h5>
                            <p>At Trust Visory, we employ cookies on our website to enhance the site, providing the best service and customer experience
							possible</p>
                        </div>
                        <div class="cookies-settings">
                            <h4>Category</h4>
                            <div class="cookies-settings-elements">
                                <h5>Necessary (Always active)</h5>
                                <p>These cookies enable essential site features like secure log-in and consent preference
								adjustments, without storing any personally identifiable data</p>
                                <label class="switch">
                                    <input type="checkbox" id="necessary" checked disabled>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="cookies-settings-elements">
                                <h5>Functional</h5>
                                <p>This category aids in specific functions such as sharing website content on social media platforms,
								receiving feedback, and incorporating third-party features</p>
                                <label class="switch">
                                    <input type="checkbox" id="functional" >
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="cookies-settings-elements">
                                <h5>Analytics</h5>
                                <p>Analytical cookies are utilized to comprehend visitor interactions on the website, offering insights into
								metrics like visitor numbers, bounce rates, and traffic sources</p>
                                <label class="switch">
                                    <input type="checkbox" id="analytics" >
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="cookies-settings-elements">
                                <h5>Performance</h5>
                                <p>These cookies help in understanding and analyzing important performance indicators of the website to
								enhance the user experience</p>
                                <label class="switch">
                                    <input type="checkbox" id="performance" >
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="cookies-settings-elements">
                                <h5>Advertisement</h5>
                                <p>Tailored advertisements are provided to visitors based on previously visited pages, while also
								evaluating the effectiveness of ad campaigns</p>
                                <label class="switch">
                                    <input type="checkbox" id="advertising" >
                                    <span class="slider round"></span>
                                </label>
                            </div>
                         
                        </div>
                        <div class="cookies-confirm">
                            <button id="confirm-cookies">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>  --}}
		</div>
    <script src="{{ url('assets/js/jquery.min.js') }}"></script>
    <script src="{{url('assets/plugins/intl-tel-input/build/js/intlTelInput.min.js')}}"></script>
     <!-- bootstrap bundle js -->     
     <script src="{{ url('assets/js/bootstrap.bundle.min.js') }}"></script>
     <script src="{{ url('assets/plugins/select2/select2.min.js') }}"></script>
    <script src="{{url('assets/plugins/toastr/toastr.min.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ apiKeys('GOOGLE_API_KEY') }}&libraries=places"></script>
    <script src="{{url('assets/plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    <script src="{{ url('assets/plugins/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ url('assets/plugins/inputmask/dist/jquery.inputmask.min.js') }}"></script>
    <script src="{{ url('assets/js/flatpickr.js') }}"></script>
    <script src="{{ url('assets/js/scripts.js') }}"></script>
    <script src="{{ url('assets/js/form-inputs.js') }}"></script>
    <script src="{{ url('assets/frontend/js/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.10.2/lottie.min.js"></script>
    <script src="https://kit.fontawesome.com/4f41ba0c55.js" crossorigin="anonymous"></script>
    

    @yield('javascript')
    @stack('scripts')
    <script>
        @if(Session::has("error"))
            errorMessage("{{ Session::get('error') }}");
        @endif

        @if(Session::has("success"))
            successMessage("{{ Session::get('success') }}");
        @endif


        function stateList(country_id, id) {
            $.ajax({
                url: "{{ url('states') }}",
                data: {
                    country_id: country_id
                },
                dataType: "json",
                beforeSend: function() {
                    $("#" + id).html('');
                },
                success: function(response) {
                    if (response.status == true) {
                        $("#" + id).html(response.options);
                    }
                },
                error: function() {

                }
            });
        }

        function cityList(state_id, id) {
            $.ajax({
                url: "{{ url('cities') }}",
                data: {
                    state_id: state_id
                },
                dataType: "json",
                beforeSend: function() {
                    $("#" + id).html('');
                },
                success: function(response) {
                    if (response.status == true) {
                        $("#" + id).html(response.options);
                    }
                },
                error: function() {

                }
            });
        }
		function setMainMinHeight() {
  var mainElement = document.querySelector('main');
  var viewportHeight = window.innerHeight;

  // Set the min-height of the <main> element
  mainElement.style.minHeight = viewportHeight + 'px';
}

// Call the function initially to set the height
setMainMinHeight();

// Optional: Add an event listener to adjust the height when the window is resized
window.addEventListener('resize', setMainMinHeight);
$(document).ready(function() {
      $(document).on("click",".logout-link",function(){
            $.ajax({
                url: "{{ url('logout') }}",
                type: "get",
                dataType: "json",
                success: function(data) {
                    localStorage.setItem('logout-event', Date.now());
                    window.location.href = "{{ url('login') }}";
                }
            });
        });
        
            $('.modal-link').on('click', function() {
                $('body').addClass("modal-opens");
                $('.header-wrapper').addClass("z-0");
                $('.cds-fs-admin-sidebar').addClass("z-0");
                $('body').css('overflow', 'hidden'); // Hide scrollbar
            });
            $('.close-modal').on('click', function() {
                $('body').removeClass("modal-opens");

                $('.header-wrapper').removeClass("z-0");
                $('.cds-fs-admin-sidebar').removeClass("z-0");
                $('body').css('overflow', 'auto'); // Hide scrollbar

            });
        });
    </script>

{{-- <script>
    	function checkCookie(name) {
            		var cookies = document.cookie.split(';');
            		for (var i = 0; i < cookies.length; i++) {
            			var cookie = cookies[i].trim();
            			if (cookie.startsWith(name + '=')) {
            				return true;
            			}
            		}
            		return false;
            	}
          
            	if (checkCookie('cookie_consent_update')) {
            		console.log('Cookie exists');
                    $('#cookiesCode').hide();
            
            	} else {
            		console.log('Cookie does not exist');
            		$('#cookiesCode').show();
            
            	}
</script>

<script>
      function setCookie(name, value, days) {
            	var expires = "";
            	if (days) {
            		var date = new Date();
            		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            		expires = "; expires=" + date.toUTCString();
            	}
            	document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }

    function acceptCookies() {

var cookieConsentValue = JSON.stringify({
    'necessary_data': 'granted',
    'ad_user_data': 'granted',
    'analytics_storage': 'granted',
    'ad_storage': 'granted',
    'ad_personalization': 'granted'
});

// // Set a cookie to remember the consent for 90 day
setCookie("cookie_consent_update", cookieConsentValue, 90);
gtag('set', 'cookie_consent', {
    ad_storage: 'denied',
    analytics_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalizatio: 'denied',
    functionality_storage: 'denied',
    personalization_storage: 'denied',
    security_storage: 'denied',
    necessary: true,
    preferences: true,
    marketing: true,
    statistics: true
});

const consentData = {
    ad_storage: 'granted',
    analytics_storage: 'granted',
    ad_user_data: 'granted',
    ad_personalization: 'granted',
    functionality_storage: 'granted',
    personalization_storage: 'granted',
    security_storage: 'granted',
    necessary: true,
    preferences: true,
    marketing: true,
    statistics: true
};

updateConsentState(consentData);

gtag("consent", "update", {
    ad_storage: 'granted',
    analytics_storage: 'granted',
    ad_user_data: 'granted',
    ad_personalization: 'granted',
    functionality_storage: "granted"
})
closecookie();

}
function closecookie() {
$('#cookiesCode').hide();
}
function rejectCookies() {
var cookieConsentValue = JSON.stringify({
    'necessary_data': 'granted',
    'ad_user_data': 'denied',
    'analytics_storage': 'granted',
    'ad_storage': 'granted',
    'ad_personalization': 'denied'
});

dataLayer.push({
    'event': 'reject_cookies',
    'necessary_data': 'granted',
    'ad_user_data': 'denied',
    'analytics_storage': 'granted',
    'ad_storage': 'granted',
    'ad_personalization': 'denied'
});
// Set a cookie to remember the consent for 90 day
setCookie("cookie_consent", cookieConsentValue, 90);
gtag('set', 'cookie_consent', {
    ad_storage: 'denied',
    analytics_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalizatio: 'denied',
    functionality_storage: 'denied',
    personalization_storage: 'denied',
    security_storage: 'denied',
    necessary: false,
    preferences: false,
    marketing: false,
    statistics: false

});
const consentData = {
    ad_storage: 'denied',
    analytics_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied',
    functionality_storage: 'granted',
    personalization_storage: 'denied',
    security_storage: 'denied',
    necessary: false,
    preferences: false,
    marketing: false,
    statistics: false
};

updateConsentState(consentData);
gtag("consent", "update", {
    ad_storage: 'denied',
    analytics_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied',
    functionality_storage: "granted"
})
closecookie();
}
function rejectCookies() {
var cookieConsentValue = JSON.stringify({
    'necessary_data': 'granted',
    'ad_user_data': 'denied',
    'analytics_storage': 'granted',
    'ad_storage': 'granted',
    'ad_personalization': 'denied'
});

dataLayer.push({
    'event': 'reject_cookies',
    'necessary_data': 'granted',
    'ad_user_data': 'denied',
    'analytics_storage': 'granted',
    'ad_storage': 'granted',
    'ad_personalization': 'denied'
});
// Set a cookie to remember the consent for 90 day
setCookie("cookie_consent", cookieConsentValue, 90);
gtag('set', 'cookie_consent', {
    ad_storage: 'denied',
    analytics_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalizatio: 'denied',
    functionality_storage: 'denied',
    personalization_storage: 'denied',
    security_storage: 'denied',
    necessary: false,
    preferences: false,
    marketing: false,
    statistics: false

});
const consentData = {
    ad_storage: 'denied',
    analytics_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied',
    functionality_storage: 'granted',
    personalization_storage: 'denied',
    security_storage: 'denied',
    necessary: false,
    preferences: false,
    marketing: false,
    statistics: false
};

updateConsentState(consentData);
gtag("consent", "update", {
    ad_storage: 'denied',
    analytics_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied',
    functionality_storage: "granted"
})
closecookie();
}
function updateConsentState(data) {

// Setting the consent cookies based on the data object
document.cookie = 'ad_storage=' + data.ad_storage + '; path=/';
document.cookie = 'analytics_storage=' + data.analytics_storage + '; path=/';
document.cookie = 'ad_user_data=' + data.ad_user_data + '; path=/';
document.cookie = 'ad_personalization=' + data.ad_personalization + '; path=/';
document.cookie = 'functionality_storage=granted; path=/';
document.cookie = 'security_storage=granted; path=/';

// Trigger GTM dataLayer event to update the consent state
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({
    event: 'consentUpdate',
    ad_storage: data.ad_storage,
    analytics_storage: data.analytics_storage,
    ad_user_data: data.ad_user_data,
    ad_personalization: data.ad_personalization,
    functionality_storage: 'granted',
    security_storage: 'granted',
    necessary: data.necessary ?? true,
    preferences: data.preferences ?? true,
    statistics: data.statistics ?? true,
    marketing: data.marketing ?? true,
    method: "explicit",
    ver: 1,
    utc: Date.now(),
});
}

document.getElementById('confirm-cookies').addEventListener('click', function () {
var necessaryChecked = document.getElementById('necessary').checked;
var functionalChecked = document.getElementById('functional').checked;
var analyticsChecked = document.getElementById('analytics').checked;
var performanceChecked = document.getElementById('performance').checked;
var advertisingChecked = document.getElementById('advertising').checked;


var cookieConsentValue = JSON.stringify({
    'event': 'accept_cookies',
    'necessory_data': necessaryChecked ? 'granted' : 'denied',
    'ad_user_data': functionalChecked ? 'granted' : 'denied',
    'analytics_storage': analyticsChecked ? 'granted' : 'denied',
    'ad_storage': performanceChecked ? 'granted' : 'denied',
    'ad_personalization': advertisingChecked ? 'granted' : 'denied'
});
const consentData = {
    ad_storage: performanceChecked ? 'granted' : 'denied',
    analytics_storage: analyticsChecked ? 'granted' : 'denied',
    ad_user_data: functionalChecked ? 'granted' : 'denied',
    ad_personalization: advertisingChecked ? 'granted' : 'denied',
    functionality_storage: 'granted',
    necessory_data: necessaryChecked ? 'granted' : 'denied',
};
updateConsentState(consentData);
gtag("consent", "update", {
    ad_storage: performanceChecked ? 'granted' : 'denied',
    analytics_storage: analyticsChecked ? 'granted' : 'denied',
    ad_user_data: functionalChecked ? 'granted' : 'denied',
    ad_personalization: advertisingChecked ? 'granted' : 'denied',
    functionality_storage: "granted"
})
setCookie("cookie_consent", cookieConsentValue, 90);
// Hide the modal
document.getElementById('custom-modal').style.display = 'none';

// Hide the modal
$('#custom-modal').hide();
closecookie();

});
</script> --}}
<script>
    $(document).ready(function () {
        googleRecaptcha();
        $('.btn-show-pass').click(function () {
            // Find the closest input of type "password" in the wrap-input div
            var $input = $(this).closest('.wrap-input').find('input[type="password"], input[type="text"]');

            // Toggle input type and icon class
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text'); // Show password
                $(this).find('.eye-pass').removeClass('flaticon-visibility').addClass('flaticon-invisible'); // Change icon
            } else {
                $input.attr('type', 'password'); // Hide password
                $(this).find('.eye-pass').removeClass('flaticon-invisible').addClass('flaticon-visibility'); // Change icon back
            }
        });
    });
    
</script>
@if(request()->getHost() === 'trustvisory.com')
<!-- Start of LiveChat (www.livechat.com) code -->
<!-- <script>
    window.__lc = window.__lc || {};
    window.__lc.license = 18689037;
    window.__lc.integration_name = "manual_onboarding";
    window.__lc.product_name = "livechat";
    ;(function(n,t,c){function i(n){return e._h?e._h.apply(null,n):e._q.push(n)}var e={_q:[],_h:null,_v:"2.0",on:function(){i(["on",c.call(arguments)])},once:function(){i(["once",c.call(arguments)])},off:function(){i(["off",c.call(arguments)])},get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can't use getters before load.");return i(["get",c.call(arguments)])},call:function(){i(["call",c.call(arguments)])},init:function(){var n=t.createElement("script");n.async=!0,n.type="text/javascript",n.src="https://cdn.livechatinc.com/tracking.js",t.head.appendChild(n)}};!n.__lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice))
</script> -->
<!-- <noscript><a href="https://www.livechat.com/chat-with/18689037/" rel="nofollow">Chat with us</a>, powered by <a href="https://www.livechat.com/?welcome" rel="noopener nofollow" target="_blank">LiveChat</a></noscript> -->
<!-- End of LiveChat code -->
@endif
</body>
</html>
