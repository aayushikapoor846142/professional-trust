 <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">


 @include('admin-panel.layouts.layout-02.head')

<body class="sb-nav-fixed">
    <audio id="notification-tone" preload="auto" src="{{ url('assets/message-notification.mp3') }}"></audio>
    <!-- <div id="messagePopup" class="new-message-popup">testtest</div> -->
     <div class="loader">
        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
        <div class="text">Loading, please wait...</div>
    </div> 

    <!-- <nav class="sb-topnav navbar navbar-expand navbar-dark cds-layout-header "> -->
     @include('admin-panel.layouts.layout-02.header')
    <div id="layoutSidenav" class="cds-dashboard-main-content-bx">
        <div id="layoutSidenav_nav">
            @include('admin-panel.layouts.layout-02.sidebar')
        </div>
        <!-- Submenu Panel -->
        <div class="cds-ty-dashboard-frame-submenu-panel" id="submenuPanel">
            <div class="toggle-submenu" id="toggleSubmenuBtn" onclick="toggleSubmenuPanel()"><i
                    class="fa-solid fa-circle-xmark"></i></div>
            <ul id="submenuContent" class="p-0"></ul>
        </div>
        <div id="layoutSidenav_content" class="CDSDashboardProfessional-main-container">
		 <div  class="CDSDashboardProfessional-main-container-header">
@include('admin-panel.layouts.layout-02.sub-header')

</div><div class="{{ $main_wrapper_class??'CDSDashboardProfessional-main-container-body' }}">
                @yield('content')
                </div>
          
        </div>
       
    </div>
    <div class="cds-admin-footer">
        <p>Domain Notice - Public Awareness by Trustvisory.com</p>
    </div>

    <div class="modal" id="popupModal" tabindex="-1" aria-labelledby="fullWidthModalLabel" aria-hidden="true"> </div>
    <div class="chat-box-area cds-custom-chat-box active"></div>

    <!-- <div id="popupModal" class="cdsTYDashboard-modal-standard"></div>  -->
    @include("components.connected-users")
    <!-- <div id="rightSidebar" class="right-sidebar">
        <i class="fa-solid fa-greater-than fa-flip-horizontal"></i>
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()"><i class="fa fa-close"></i></a>
        <div class="service-area">
            <label>Search services to choose</label>
            <div class="d-flex justify-content-start h-100 search-area">
                <div class="searchbar">
                    <input class="search_input" type="text" name="" placeholder="Search For Services...">
                </div>
                <div id="autocomplete-list" class="autocomplete-items"></div>
            </div>
        </div>
    </div> -->
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
    <script src="{{url('/assets/js/scripts.js?v='.time())}}"></script>
    <script src="https://kit.fontawesome.com/4f41ba0c55.js" crossorigin="anonymous"></script>
    <script src="{{ url('assets/js/form-inputs.js') }}" type="text/javascript"></script>

    <script src="{{ url('assets/plugins/chatapp/emojis/js/joypixels.min.js?v='.mt_rand()) }}" type="text/javascript">
    </script>
    <script src="{{ url('assets/plugins/chatapp/emojis/js/joypixels-custom.js?v='.mt_rand()) }}" type="text/javascript">
    </script>
     <script src="{{url('assets/js/custom-editor.js?v='.mt_rand())}}"></script>
    @if(auth()->check())
    <script src="{{ url('assets/plugins/chatapp/chatapp.js?v='.mt_rand()) }}" type="text/javascript"></script>
    <script src="{{ url('assets/plugins/chatapp/group-chatapp.js?v='.mt_rand()) }}" type="text/javascript"></script>

    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('javascript')
       <script>
        
        /****************************************
         *  MOBILE NAV (SIDE-DRAWER) LOGIC      *
         ****************************************/
        const hamburgerBtn = document.getElementById('CDSMainNavigation-hamburgerBtn');
        const navBar = document.getElementById('CDSMainNavigation-navBar');

        hamburgerBtn.addEventListener('click', () => {
            navBar.classList.toggle('CDSMainNavigation-show');
        });

        // Close side drawer if clicking outside
        document.addEventListener('click', (e) => {
            if (
                navBar.classList.contains('CDSMainNavigation-show') &&
                !navBar.contains(e.target) &&
                !hamburgerBtn.contains(e.target)
            ) {
                navBar.classList.remove('CDSMainNavigation-show');
            }
        });

        // Close side drawer when clicking the close button
        document.querySelector('.close-btn').addEventListener('click', () => {
            navBar.classList.remove('CDSMainNavigation-show');
        });


        /****************************************
         *    SIMPLE & MEGA DROPDOWN LOGIC      *
         ****************************************/
        // We have multiple "dropbtn" elements,
        // each references a content area via data-target="ID"
        const dropButtons = document.querySelectorAll('.CDSMainNavigation-dropbtn');

        // close any open dropdown
        function closeDropdownContent(contentEl) {
            if (contentEl.classList.contains('CDSMainNavigation-show')) {
                contentEl.classList.remove('CDSMainNavigation-show');
                contentEl.addEventListener('transitionend', function endHandler() {
                    contentEl.style.left = '';
                    contentEl.style.right = '';
                    contentEl.removeEventListener('transitionend', endHandler);
                });
            }
        }

        function closeAllDropdowns() {
            // Both simple & mega are toggled by .CDSMainNavigation-show
            const openDropdowns = document.querySelectorAll(
                '.CDSMainNavigation-simple-dropdown-content.CDSMainNavigation-show, ' +
                '.CDSMainNavigation-mega-dropdown-content.CDSMainNavigation-show'
            );
            openDropdowns.forEach((dd) => closeDropdownContent(dd));
        }

        function adjustDropdownPosition(ddEl) {
            // On desktop, if a dropdown extends beyond the right edge, pin it
            const rect = ddEl.getBoundingClientRect();
            if (rect.right > window.innerWidth) {
                ddEl.style.left = 'auto';
                ddEl.style.right = '0.5rem';
            }
            if (rect.left < 0) {
                ddEl.style.left = '0.5rem';
                ddEl.style.right = 'auto';
            }
        }

        function toggleDropdownContent(contentEl) {
            const isOpen = contentEl.classList.contains('CDSMainNavigation-show');
            if (isOpen) {
                closeDropdownContent(contentEl);
            } else {
                closeAllDropdowns();
                contentEl.classList.add('CDSMainNavigation-show');
                adjustDropdownPosition(contentEl);
            }
        }

        // For each dropbtn, toggle the associated content
        dropButtons.forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const targetId = btn.getAttribute('data-target');
                if (!targetId) return;
                const contentEl = document.getElementById(targetId);
                if (contentEl) {
                    toggleDropdownContent(contentEl);
                }
            });
        });

        // Close dropdown if user clicks outside
        document.addEventListener('click', (e) => {
            // If the click isn't on a .dropbtn or inside the open dropdown content, close all
            const isInDropdown = e.target.closest('.CDSMainNavigation-dropbtn') ||
                e.target.closest(
                    '.CDSMainNavigation-simple-dropdown-content, .CDSMainNavigation-mega-dropdown-content');
            if (!isInDropdown) {
                closeAllDropdowns();
            }
        });

        /****************************************
         *   PROFILE PANEL SLIDE-IN LOGIC       *
         ****************************************/
        const profileBtn = document.getElementById('CDSMainNavigation-profileBtn');
        const profilePanel = document.getElementById('CDSMainNavigation-profilePanel');
        const closeProfileBtn = document.getElementById('CDSMainNavigation-closeProfileBtn');

        // profileBtn.addEventListener('click', (e) => {
        //     e.stopPropagation();
        //     profilePanel.classList.add('CDSMainNavigation-show');
        // });
        closeProfileBtn.addEventListener('click', () => {
            profilePanel.classList.remove('CDSMainNavigation-show');
        });

        // Close panel if clicking outside
        document.addEventListener('click', (e) => {
            if (
                profilePanel.classList.contains('CDSMainNavigation-show') &&
                !profilePanel.contains(e.target) &&
                e.target !== profileBtn
            ) {
                profilePanel.classList.remove('CDSMainNavigation-show');
            }
        });

    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('menuToggle');
    const closeNav = document.getElementById('closeNav');
    const fullscreenNav = document.getElementById('fullscreenNav');

    if (menuToggle && closeNav && fullscreenNav) {
        // Open navigation
        menuToggle.addEventListener('click', () => {
            fullscreenNav.classList.add('active');
        });

        // Close navigation
        closeNav.addEventListener('click', () => {
            fullscreenNav.classList.remove('active');
        });
    }
});

    </script>
    <script>
    @if(Session::has("error"))
    errorMessage("{{ Session::get('error') }}");
    @endif

    @if(Session::has("success"))
    successMessage("{{ Session::get('success') }}");
    @endif
    window.addEventListener('storage', function (event) {
        if (event.key === 'logout-event') {
            // Reload current tab to reflect logout
            window.location.href = "{{ route('login') }}";
        }
    });
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
        $(document).on("click", ".choose-main-service", function() {
            var id = $(this).data("id");
            $.ajax({
                url: "{{ baseUrl('choose-services') }}",
                type: "post",
                data: {
                    _token: csrf_token,
                    type: 'parent_service',
                    service_id: id
                },
                dataType: "json",
                success: function(data) {
                    $(this).removeAttr("disabled");
                    if (data.status) {
                        let query = $(".search_input").val();
                        if (query.length > 0) {
                            searchService(query);
                        } else {
                            $('#autocomplete-list').empty();
                        }
                    }

                }
            });
        });
        $(document).on("click", ".choose-sub-service", function() {
            $(this).attr("disabled", "disabled");
            var id = $(this).data("id");
            $.ajax({
                url: "{{ baseUrl('choose-services') }}",
                type: "post",
                data: {
                    _token: csrf_token,
                    type: 'sub_service',
                    service_id: id
                },
                dataType: "json",
                success: function(data) {
                    $(this).removeAttr("disabled");
                    if (data.status) {
                        let query = $(".search_input").val();
                        if (query.length > 0) {
                            searchService(query);
                        } else {
                            $('#autocomplete-list').empty();
                        }
                    }

                }
            });
        });
        $('.search_input').on('keyup', function() {
            let query = $(this).val();
            var core = 0;
            var service_id = 0;
            var parent_service_id = 0;
            if (query.length > 0) {
                searchService(query);
            } else {
                $('#autocomplete-list').empty();
            }
        });
        getMininmizedMessaging =
        JSON.parse(localStorage.getItem("minimizedMessagingBox")) || [];
        let isMinimized = localStorage.getItem("minimizedMessagingBox") === "false";

        const msgbotContainer = document.querySelector(".message-show-block");
                if (isMinimized) {
                    msgbotContainer.classList.remove('active');
                  //  toggleBots($("#chatbot-" + bot[1]), true);
                }else{
                    msgbotContainer.classList.add('active');
                }
    });

    function searchService(query) {
        $.ajax({
            url: "{{ baseUrl('search-services') }}",
            type: "GET",
            data: {
                query: query
            },
            dataType: "json",
            success: function(data) {
                let list = $('#autocomplete-list');
                list.empty();
                list.html(data.contents);
            }
        });
    }

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

        // notificaiton function
        function toggleCaseNotifications() {
            const panel = document.getElementById("notificationPanel");
            panel.classList.toggle("show");
        }

        function removeCaseNotification(button) {
            const notification = button.parentElement;
            notification.classList.add("fade-out");
            setTimeout(() => {
                notification.remove();
                updateCaseBadge();
            }, 500);
        }

        function updateCaseBadge() {
            let notifications = document.querySelectorAll(".cds-case-notification-item").length;
            const badge = document.getElementById("badgeCount");

            if (notifications === 0) {
                badge.style.display = "none";
            } else {
                badge.textContent = notifications;
            }
        }


    // end notification function
    </script>
    <!-- toggle sidebar -->
    <script>
    function toggleMenu() {
        const sideMenu = document.getElementById("layoutSidenav_nav");
        const mainContent = document.getElementById("layoutSidenav")
        sideMenu.classList.toggle("collapsed");
        mainContent.classList.toggle("collapsed-menu")
    }



    function togglePanel() {
        const rightMenu = document.getElementById("rightSidebar");
        rightMenu.classList.toggle("collapsed");
    }
    // Check screen size
    function initialiseScreenSize() {
        // const rightMenu = document.getElementById("rightSidebar");
        const sideMenu = document.getElementById("layoutSidenav_nav");
        const mainContent = document.getElementById("layoutSidenav");

        if (window.innerWidth < 1366) {
            console.log("less");
            // rightMenu.classList.add("collapsed");
            sideMenu.classList.add("collapsed");
            mainContent.classList.add("collapsed-menu");
        } else {
            console.log("greater");
            // rightMenu.classList.remove("collapsed");
            sideMenu.classList.remove("collapsed");
            mainContent.classList.remove("collapsed-menu");
        }
    }

    // Run on page load
    initialiseScreenSize();

    // Update dynamically on window resize
    window.addEventListener("resize", initialiseScreenSize);



    function toggleSubmenuPanel() {
        const submenuPanel = document.getElementById("submenuPanel");
        const toggleSubmenuBtn = document.getElementById("toggleSubmenuBtn");

        submenuPanel.classList.toggle("active");
        toggleSubmenuBtn.style.display = submenuPanel.classList.contains("active") ? "block" : "none";
    }
    </script>
    <!-- toggle submenu -->
    <script>
    function showSubmenu(event, menuItem) {
        event.preventDefault(); // Prevent navigation

        const submenuPanel = document.getElementById("submenuPanel");
        const submenuContent = document.getElementById("submenuContent");
        const toggleSubmenuBtn = document.getElementById("toggleSubmenuBtn");
        const rightPanel = document.getElementById("rightSidebar"); // Get the right panel element

        submenuContent.innerHTML = ''; // Clear previous submenu items

        const submenuItems = menuItem.nextElementSibling.cloneNode(true); // Clone the submenu items
        submenuItems.style.display = "block";
        submenuContent.appendChild(submenuItems); // Append cloned items to the submenu panel

        submenuPanel.classList.add("active"); // Show the submenu panel
        toggleSubmenuBtn.style.display = "block"; // Show the toggle button

    }
    </script>
    <script>
    // Select all nav-link elements
    document.querySelectorAll('.nav-link').forEach((item) => {
        const tooltip = item.querySelector('.cds-tooltip');

        // Show tooltip on hover
        if (tooltip) {
            item.addEventListener('mouseenter', () => {
                const rect = item.getBoundingClientRect(); // Get position relative to the viewport
                tooltip.style.left = `${rect.right + 10}px`; // Position tooltip to the right
                tooltip.style.top = `${rect.top + rect.height / 2}px`; // Center tooltip vertically
                tooltip.classList.add('visible'); // Show tooltip
            });

            // Hide tooltip on mouse leave
            item.addEventListener('mouseleave', () => {
                tooltip.classList.remove('visible'); // Hide tooltip
            });
        }
    });
    </script>
    <script>
    $(document).ready(function() {
        googleRecaptcha();
        $('.btn-show-pass').click(function() {
            // Find the closest input of type "password" in the wrap-input div
            var $input = $(this).closest('.wrap-input').find(
                'input[type="password"], input[type="text"]');

            // Toggle input type and icon class
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text'); // Show password
                $(this).find('.eye-pass').removeClass('flaticon-visibility').addClass(
                    'flaticon-invisible'); // Change icon
            } else {
                $input.attr('type', 'password'); // Hide password
                $(this).find('.eye-pass').removeClass('flaticon-invisible').addClass(
                    'flaticon-visibility'); // Change icon back
            }
        });
    });
    </script>
   <script>
        function openSidebar() {
            const sidebar = document.getElementById('layoutSidenav_nav');
            const submenuPanel = document.getElementById("submenuPanel");
            const toggleSubmenuBtn = document.getElementById("toggleSubmenuBtn");

            // Remove "collapsed" class
            sidebar.classList.remove("collapsed");

            // Check if "openMobile" class is present
            if (sidebar.classList.contains("openMobile")) {
                sidebar.classList.remove("openMobile");
                sidebar.classList.add("closeMobile");
                if (submenuPanel.classList.contains("active")) {
                    submenuPanel.classList.remove("active");
                }

            } else {
                sidebar.classList.remove("closeMobile");
                sidebar.classList.add("openMobile");
            }
        }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Check if the viewport width is 991px or less
        if (window.matchMedia("(max-width: 991px)").matches) {
            const sidebar = document.getElementById('layoutSidenav_nav');
            const submenuPanel = document.getElementById("submenuPanel");
            const toggleSubmenuBtn = document.getElementById("toggleSubmenuBtn");

            // Mobile: Sidebar collapsed by default
            sidebar.classList.add("collapsed");

            sidebar.style.visibility = "visible"; // Make it visible once script runs

            function openSidebar() {
                sidebar.classList.remove("collapsed");

                if (sidebar.classList.contains("openMobile")) {
                    sidebar.classList.remove("openMobile");
                    sidebar.classList.add("closeMobile");
                    if (submenuPanel.classList.contains("active")) {
                        submenuPanel.classList.remove("active");
                    }
                } else {
                    sidebar.classList.remove("closeMobile");
                    sidebar.classList.add("openMobile");
                }
            }
        }
    });
</script>

    @stack("scripts")
</body>

</html>