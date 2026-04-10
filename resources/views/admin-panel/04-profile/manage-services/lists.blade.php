
<div class="CDSDashboardProfessionalServices-list-overlay" id="overlay" onclick="closeSidebar()"></div>
@if(checkPrivilege([
        'route_prefix' => 'panel.manage-services',
        'module' => 'professional-manage-services',
        'action' => 'add'
    ]))

<a href="{{baseUrl('manage-services/add-pathway')}}" class="CdsTYButton-btn-primary">Add Configuration</a>
@endif
<!-- pin service -->
    <!-- <div class="CDSDashboardProfessionalServices-list-panel" style="transform: translateY(0px);">
        <div class="CDSDashboardProfessionalServices-list-panel-header">
            <h2 class="CDSDashboardProfessionalServices-list-panel-title">
                <div class="CDSDashboardProfessionalServices-list-icon-badge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                Pinned Services
            </h2>
        </div>
        
        <div class="CDSDashboardProfessionalServices-list-panel-body CDSDashboardProfessionalServices-pin-service-body">
            
        </div>
    </div> -->
    <!-- end -->
    <div class="CDSDashboardProfessionalServices-list-container" id="container">
       
        <div class="CDSDashboardProfessionalServices-list-main-content">
            <!-- Regular Services Panel -->
            <div class="CDSDashboardProfessionalServices-list-panel">
                <div class="CDSDashboardProfessionalServices-list-panel-header">
                    <h2 class="CDSDashboardProfessionalServices-list-panel-title">
                        <div class="CDSDashboardProfessionalServices-list-icon-badge" style="background: #dbeafe;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #2563eb;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        Regular Services
                         <!-- <a href="javascript:;" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('manage-services/get-all-services') }}" class="cds-btnopen CdsTYButton-btn-primary">
                        Choose Service
                    </a> -->
                    </h2>
                   
                </div>

                <div class="CDSDashboardProfessionalServices-list-panel-body">
                    
                    @if($records->isNotEmpty())
                    @foreach($records as $key => $record)
                    <div class="CDSDashboardProfessionalServices-list-collapsible">
                        <div class="CDSDashboardProfessionalServices-list-collapsible-header" onclick="toggleCollapse(this,'{{$record->unique_id}}')">
                            <div class="CDSDashboardProfessionalServices-list-collapsible-title">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M12 2v4a2 2 0 002 2h4M12 2H5a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V8m-5-6l5 5M7 13h6m-6 3h3"/>
                                </svg>
                                 {{$record->name}}
                            </div>
                            @if(checkPrivilege([
        'route_prefix' => 'panel.manage-services',
        'module' => 'professional-manage-services',
        'action' => 'edit'
    ]))
                            <a href="{{baseUrl('manage-services/add-pathway/'.$record->unique_id)}}" class="CdsTYButton-btn-primary me-1" style="margin-left: auto;">Edit</a>
                            @endif
                            <svg class="CDSDashboardProfessionalServices-list-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 7.5L10 12.5L15 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        
                        <div class="CDSDashboardProfessionalServices-list-collapsible-content">
                            <div id="CDSDashboardProfessionalServices-list-{{$record->unique_id}}">
                               
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                      <span class="alert alert-warning">No service selected yet</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- sidebar -->
@section('javascript')
@stack('service_script')
<script>
    function toggleCollapse(header, main_service_id) {
     
        const targetDivId = '#CDSDashboardProfessionalServices-list-' + main_service_id;

        $.ajax({
            type: "GET",
            url: BASEURL + '/manage-services/add-service/' + main_service_id,
            data: {
                _token: csrf_token,
            },
            dataType: 'json',
            success: function(data) {
                $(targetDivId).html(data.contents);
            }
        });

        const content = header.nextElementSibling;
        const chevron = header.querySelector('.CDSDashboardProfessionalServices-list-chevron');

        content.classList.toggle('CDSDashboardProfessionalServices-list-expanded');
        chevron.classList.toggle('CDSDashboardProfessionalServices-list-rotate');
    }



    function openSidebar() {
        const sidebar = document.getElementById('sidebar');
        const container = document.getElementById('container');
        const overlay = document.getElementById('overlay');
        
        sidebar.classList.add('CDSDashboardProfessionalServices-list-active');
        
        if (window.innerWidth >= 1024) {
            container.classList.add('CDSDashboardProfessionalServices-list-sidebar-open');
        } else {
            overlay.classList.add('CDSDashboardProfessionalServices-list-active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const container = document.getElementById('container');
        const overlay = document.getElementById('overlay');
        
        sidebar.classList.remove('CDSDashboardProfessionalServices-list-active');
        container.classList.remove('CDSDashboardProfessionalServices-list-sidebar-open');
        overlay.classList.remove('CDSDashboardProfessionalServices-list-active');
        document.body.style.overflow = '';
    }

    function switchTab(element, tabName) {
        // Remove active class from all tabs
        const tabs = document.querySelectorAll('.CDSDashboardProfessionalServices-list-tab');
        tabs.forEach(tab => tab.classList.remove('CDSDashboardProfessionalServices-list-active'));
        
        // Add active class to clicked tab
        element.classList.add('CDSDashboardProfessionalServices-list-active');
        
        // Hide all content sections
        const contents = document.querySelectorAll('#fees-content, #additional-content');
        contents.forEach(content => content.style.display = 'none');
        
        // Show the selected content
        const selectedContent = document.getElementById(tabName + '-content');
        if (selectedContent) {
            selectedContent.style.display = 'block';
        }
    }

   
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    });

    // Touch handling for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    function handleGesture() {
        const sidebar = document.getElementById('sidebar');
        const sidebarActive = sidebar.classList.contains('CDSDashboardProfessionalServices-list-active');
        
        if (touchEndX < touchStartX - 50 && !sidebarActive) {
            openSidebar();
        }
        
        if (touchEndX > touchStartX + 50 && sidebarActive) {
            closeSidebar();
        }
    }

    document.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    });

    document.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleGesture();
    });

    // Resize handler
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            const overlay = document.getElementById('overlay');
            
            if (window.innerWidth >= 1024 && sidebar.classList.contains('CDSDashboardProfessionalServices-list-active')) {
                overlay.classList.remove('CDSDashboardProfessionalServices-list-active');
                document.body.style.overflow = '';
            } else if (window.innerWidth < 1024 && sidebar.classList.contains('CDSDashboardProfessionalServices-list-active')) {
                overlay.classList.add('CDSDashboardProfessionalServices-list-active');
                container.classList.remove('CDSDashboardProfessionalServices-list-sidebar-open');
            }
        }, 250);
    });

    // Multi-Select Dropdown Functionality
    // Initialize multi-select when visa details are expanded
    // Prevent body scroll when sidebar is open on mobile
    function preventBodyScroll(prevent) {
        if (prevent) {
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
        } else {
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
        }
    }

    // Enhanced scroll performance
    let ticking = false;
    function updateScrollAnimation() {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const panels = document.querySelectorAll('.CDSDashboardProfessionalServices-list-panel');
                
                panels.forEach((panel) => {
                    const rect = panel.getBoundingClientRect();
                    const visible = (rect.top >= 0 && rect.bottom <= window.innerHeight);
                    
                    if (visible) {
                        panel.style.transform = `translateY(${scrollTop > lastScrollTop ? -1 : 0}px)`;
                    }
                });
                
                lastScrollTop = scrollTop;
                ticking = false;
            });
            
            ticking = true;
        }
    }

    let lastScrollTop = 0;
    window.addEventListener('scroll', updateScrollAnimation, { passive: true });
</script>

<!-- subservice-type function -->
 <script>
    loadPinnedServices();
function loadPinnedServices(){
    $.ajax({
        type: "POST",
        url: BASEURL + '/manage-services/pinned-services-ajax',
        data: {
            _token: csrf_token,
        },
        dataType: 'json',
        beforeSend: function() {
            $(".CDSDashboardProfessionalServices-pin-service-body").html('<center><i class="fa fa-spin fa-spinner fa-3x"></i></center>');
        },
        success: function(data) {
            $(".CDSDashboardProfessionalServices-pin-service-body").html(data.contents);
        },
    });
}
function markAsPin(id,is_pin) {
    var type = is_pin == 1?'pin':'unpin';    
    Swal.fire({
        title: "Are you sure to mark as "+type,
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: BASEURL + '/manage-services/pin-my-service',
                data: {
                    _token: csrf_token,
                    id: id,
                    is_pin:is_pin
                },
                dataType: "json",
                beforeSend: function () {},
                success: function (response) {
                    if (response.status == true) {
                        successMessage(response.message);
                       location.reload();
                    } else {
                        errorMessage(response.message);
                    }
                },
                error: function () {
                    internalError();
                },
            });
        }
    });
}
 </script>
 <!-- end -->
@endsection