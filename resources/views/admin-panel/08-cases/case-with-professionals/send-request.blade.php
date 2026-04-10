@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')

@section('case-container')

    <div class="CdsCaseRequest-bg-animation">
        <div class="CdsCaseRequest-floating-shape CdsCaseRequest-shape-1"></div>
        <div class="CdsCaseRequest-floating-shape CdsCaseRequest-shape-2"></div>
    </div>
    <main class="CdsCaseRequest-main-container Cds-request-ajax">
        
    </main>
    <div id="case-request-skeleton-loader" style="display:none;">
        @include('components.loaders.case-request-loader')              
    </div>
    <div class="CdsCaseRequest-action-buttons">
        <!-- <a href="{{baseUrl('case-with-professionals/add-request/'.$case_id)}}" class="CdsCaseRequest-fab" title="Edit Request">
            <svg viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
        </a> -->
        <a href="{{baseUrl('case-with-professionals/add-request/'.$case_id)}}" class="CdsCaseRequest-fab" title="Add Request">
            <svg viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </a>
    </div>
@endsection

@section('javascript')
    <script>
        // Tab switching
        function cdsCaseRequestInitTabSwitching() {
            const tabs = document.querySelectorAll('.CdsCaseRequest-nav-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('CdsCaseRequest-active'));
                    tab.classList.add('CdsCaseRequest-active');
                });
            });
        }

        // Smooth scroll for breadcrumb
        function cdsCaseRequestInitBreadcrumbScroll() {
            document.querySelectorAll('.CdsCaseRequest-breadcrumb-item').forEach(item => {
                item.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });
        }

        // Ripple effect for buttons
        function cdsCaseRequestInitRippleEffect() {
            const buttons = document.querySelectorAll('.CdsCaseRequest-fab, .CdsCaseRequest-action-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.6);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: CdsCaseRequest-ripple 0.6s ease-out;
                    `;
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                });
            });
        }

        // Action button handlers
        function cdsCaseRequestInitActionButtons() {
            // View button
            const viewBtn = document.querySelector('.CdsCaseRequest-btn-view');
            if (viewBtn) {
                viewBtn.addEventListener('click', () => {
                    console.log('View case details');
                    // Add your view logic here
                });
            }

            // Edit button
            const editBtn = document.querySelector('.CdsCaseRequest-btn-edit');
            if (editBtn) {
                editBtn.addEventListener('click', () => {
                    console.log('Edit case');
                    // Add your edit logic here
                });
            }

            // Delete button
            const deleteBtn = document.querySelector('.CdsCaseRequest-btn-delete');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', () => {
                    if (confirm('Are you sure you want to delete this case?')) {
                        console.log('Delete case');
                        // Add your delete logic here
                    }
                });
            }
        }

        // Parallax effect on scroll
        function cdsCaseRequestInitParallax() {
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const shapes = document.querySelectorAll('.CdsCaseRequest-floating-shape');
                shapes.forEach((shape, index) => {
                    const speed = index === 0 ? 0.5 : 0.3;
                    shape.style.transform = `translateY(${scrolled * speed}px)`;
                });
            });
        }

        // Initialize all functions
        function cdsCaseRequestInit() {
            cdsCaseRequestInitTabSwitching();
            cdsCaseRequestInitBreadcrumbScroll();
            cdsCaseRequestInitRippleEffect();
            cdsCaseRequestInitActionButtons();
            cdsCaseRequestInitParallax();
        }

        // Run initialization when DOM is loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', cdsCaseRequestInit);
        } else {
            cdsCaseRequestInit();
        }
    </script>
<script type="text/javascript">
    
    function markAsComplete(e) {
        var url = $(e).attr("data-href");
        Swal.fire({
            title: "Are you sure to mark as complete?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            confirmButtonClass: "CdsTYButton-btn-primary",
            cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                redirect(url);
            }
        });
    }

    loadData();
    function loadData(page = 1, search = "") {
        var case_id = "{{$case_id}}";
        $.ajax({
            type: "POST",
            url: BASEURL + '/case-with-professionals/request-ajax-list/' + case_id,
            data: {
                _token: csrf_token,
            },
            dataType: 'json',
            beforeSend: function() {
                $("#case-request-skeleton-loader").show();
            },
            success: function(data) {
                $(".Cds-request-ajax").html(data.contents);
                $("#case-request-skeleton-loader").hide();
            }
        });
    }
</script>
@endsection
