 






<script> 




/****************************************
         * CDS HEADER 02  LOGIC      *
         ****************************************/
  const burgerMenu = document.getElementById('burgerMenu');
  const mainNav = document.getElementById('mainNav');

  burgerMenu.addEventListener('click', () => {
    const isOpen = mainNav.classList.toggle('open');
    burgerMenu.setAttribute('aria-expanded', isOpen);
  });

  const dropdownButtons = document.querySelectorAll('.CDSCTNav-menu-toggle');
  dropdownButtons.forEach(button => {
    const target = document.getElementById(button.dataset.target);

    button.addEventListener('click', e => {
      const isOpen = target.classList.contains('open');
      document.querySelectorAll('.CDSCTNav-dropdown').forEach(d => d.classList.remove('open'));
      dropdownButtons.forEach(b => b.setAttribute('aria-expanded', 'false'));

      if (!isOpen) {
        target.classList.add('open');
        button.setAttribute('aria-expanded', 'true');
      }
    });

    button.addEventListener('keydown', e => {
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        const firstLink = target.querySelector('a');
        if (firstLink) firstLink.focus();
      }
    });
  });

  document.addEventListener('click', e => {
    if (!e.target.closest('.CDSCTNav-menu-item')) {
      document.querySelectorAll('.CDSCTNav-dropdown').forEach(d => d.classList.remove('open'));
      dropdownButtons.forEach(b => b.setAttribute('aria-expanded', 'false'));
    }
  });

  // Floating Panel
  const notificationTrigger = document.getElementById('notificationTrigger');
  const profileTrigger = document.getElementById('profileTrigger');
  const panel = document.getElementById('floatingPanel');
  const backdrop = document.getElementById('backdropOverlay');
  const closeBtn = document.getElementById('panelCloseBtn');
  const badge = document.querySelector('.notificationBadge');
  const tabNotifications = document.getElementById('tabNotifications');
  const tabSettings = document.getElementById('tabSettings');
  const panelNotifications = document.getElementById('panelNotifications');
  const panelSettings = document.getElementById('panelSettings');

  function openPanel(tab = 'notifications') {
    panel.classList.add('open');
    backdrop.classList.add('active');
   
    setActiveTab(tab);
  }

  function closePanel() {
    panel.classList.remove('open');
    backdrop.classList.remove('active');
  }

 function setActiveTab(tab) {
  // Deactivate all
  tabNotifications.classList.remove('active');
  tabSettings.classList.remove('active');
  panelNotifications.classList.remove('active');
  panelSettings.classList.remove('active');

  // Activate selected tab
  if (tab === 'notifications') {
    tabNotifications.classList.add('active');
    panelNotifications.classList.add('active');
  } else {
    tabSettings.classList.add('active');
    panelSettings.classList.add('active');
  }
}


  notificationTrigger.addEventListener('click', e => {
    e.stopPropagation();
    openPanel('notifications');
  });

  profileTrigger.addEventListener('click', e => {
    e.stopPropagation();
    openPanel('settings');
  });

  tabNotifications.addEventListener('click', () => setActiveTab('notifications'));
  tabSettings.addEventListener('click', () => setActiveTab('settings'));
  closeBtn.addEventListener('click', closePanel);
  backdrop.addEventListener('click', closePanel);

  var touchStartX01 = 0, touchEndX = 0;
  panel.addEventListener('touchstart', e => {
    if (window.innerWidth <= 768) touchStartX01 = e.changedTouches[0].screenX;
  });
  panel.addEventListener('touchend', e => {
    if (window.innerWidth <= 768) {
      touchEndX = e.changedTouches[0].screenX;
      if (touchStartX01 - touchEndX > 50) closePanel();
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
      var profTimezoneh = Intl.DateTimeFormat().resolvedOptions().timeZone;
            var getprofileUrl="{{baseUrl('/profile/personal-detail')}}";
            var editProfile = "{{baseUrl('edit-profile')}}";
            if(profTimezoneh=="Asia/Calcutta"){
                profTimezoneh="Asia/Kolkata";
            }
            var userRole = $('#user_role').val();
            var redirectUrl = (userRole === "professional") ? getprofileUrl : editProfile;

           "{{ url('/profile/edit') }}";

 
        if (sessionStorage.getItem('timezone_alert_dismissed') !== '1' && profTimezoneh != $('#professional_profile_timezone').val()) {
                $('#header_notif').html(
        '<div class="cdsTYDashboardAlert-danger-notification cdsAlertmsg" id="timezone-alert" style="position: relative;">' +
            '<span>Your current timezone and profile timezone are different</span> ' +
            '<a href="' + redirectUrl + '" target="_blank">update your timezone</a>' +
            '<span onclick="dismissTimezoneAlert()" ' +
                'style="position:absolute; right:10px; top:5px; cursor:pointer; font-size:18px;">&times;</span>' +
        '</div>'
    );
                $("#cdsalaertdiv").addClass("cdsshowbox");
            }
        console.log(profTimezoneh);
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

    function dismissTimezoneAlert() {
   $('#timezone-alert').remove();
            sessionStorage.setItem('timezone_alert_dismissed', '1');
             location.reload();
            console.log('Timezone alert dismissed.');
}
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
<script>
    const burger = document.getElementById('burgerMenu');
    const nav = document.getElementById('mainNav');

    burger.addEventListener('click', function () {
        const isExpanded = burger.getAttribute('aria-expanded') === 'true';
        burger.setAttribute('aria-expanded', !isExpanded);
        nav.classList.toggle('active');
    });
</script>