 <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
 @include('admin-panel.layouts.layout-01.head')
 @yield('styles')
</head>

<body class="cdsTYFramework">
       <div id="mainContent">
            <audio id="notification-tone" preload="auto" src="{{ url('assets/message-notification.mp3') }}"></audio>

            <div class="cdsTYDashboard-main-header-container">
                <div id="cdsTYDashboardAlert-notification-header"></div>
                @include('admin-panel.layouts.layout-01.header')
            </div>
            <div class="cdsTYDashboard-main-content-container-outer">
                <div id="cdsTYDashboard-main-content-container">
                    <div id="layoutSidenav_nav">
                        @include('admin-panel.layouts.layout-01.sidebar')
                        <!-- Submenu Panel -->
                        <div class="cds-ty-dashboard-frame-submenu-panel" id="submenuPanel">
                            <div class="toggle-submenu" id="toggleSubmenuBtn" onclick="toggleSubmenuPanel()"><i class="fa-solid fa-circle-xmark"></i></div>
                            <ul id="submenuContent" class="p-0"></ul>
                        </div>
                    </div>

                    <div id="layoutSidenav_content" class="CDSDashboardProfessional-main-container">
                        <div class="CDSDashboardProfessional-main-container-header">
                            @include('admin-panel.layouts.layout-01.sub-header') @yield("page-submenu")
                        </div>

                        <div class="{{ $main_wrapper_class??'CDSDashboardProfessional-main-container-body' }}">
                            <div class="CDSDashboardContainer-wrapper">
                                @yield('content')
                            </div>
                        </div>
                        <div class="CDSDashboard-footer">
                            <p>Trustvisory.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="professional_profile_timezone" name="professional_profile_timezone" value="{{auth()->user()->timezone}}" />
        <input type="hidden" id="user_role" name="user_role" value="{{auth()->user()->role}}" />
        <div class="modal" id="popupModal" tabindex="-1" aria-labelledby="fullWidthModalLabel" aria-hidden="true"></div>
        <div class="CdsDashboardCustomPopup-overlay" id="customPopupOverlay"></div>
        <div class="chat-box-area cds-custom-chat-box active"></div>

    <!-- <div id="popupModal" class="cdsTYDashboard-modal-standard"></div>  -->
    @include("components.connected-users")


    <script src="{{url('assets/js/jquery.min.js')}}"></script>
    <!-- <script src="{{url('/assets/js/jquery-ui.min.js')}}"></script> -->
    <!-- bootstrap bundle js -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->

    <!-- <script src="{{ url('/assets/js/bootstrap.bundle.min.js') }}"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->

 
    <script src="{{url('assets/plugins/select2/select2.min.js')}}"></script>
    <script src="{{url('assets/plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    <script src="{{url('assets/js/intlTelInput.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/jquery.inputmask.bundle.min.js"></script>
    <script src="{{url('/assets/plugins/toastr/toastr.min.js')}}"></script>
    <script src="{{url('/assets/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{ url('assets/plugins/dropzone/dropzone.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ apiKeys('GOOGLE_API_KEY') }}&libraries=places">
    </script>
    <script src="{{ url('/assets/js/flatpickr.js') }}"></script>
  
    <script src="https://kit.fontawesome.com/4f41ba0c55.js" crossorigin="anonymous"></script>
    <script src="{{ url('assets/js/form-inputs.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/js/timepicker.js') }}" type="text/javascript"></script>

    <script src="{{ url('assets/plugins/chatapp/emojis/js/joypixels.min.js?v='.mt_rand()) }}" type="text/javascript">
    </script>
    <script src="{{ url('assets/plugins/chatapp/emojis/js/joypixels-custom.js?v='.mt_rand()) }}" type="text/javascript">
    </script>
     <script src="{{url('assets/js/custom-datepicker.js?v='.mt_rand())}}"></script>

   

    @if(auth()->check())
    <script src="{{ url('assets/plugins/chatapp/chatapp.js?v='.mt_rand()) }}" type="text/javascript"></script>
    <script src="{{ url('assets/plugins/chatapp/group-chatapp.js?v='.mt_rand()) }}" type="text/javascript"></script>
    
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.10.2/lottie.min.js"></script> <script src="{{url('/assets/js/scripts.js?v='.time())}}"></script>
     <script src="{{url('assets/js/custom-editor.js?v='.mt_rand())}}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('javascript')
@include('admin-panel.layouts.layout-01.components.app-script')
  
@include("components.right-slide-panel")
@stack("scripts")
</body>

</html>