@extends('admin-panel.layouts.app')
@section('page-submenu')
    {!! pageSubMenu('my-membership-plans') !!}
@endsection

@section('styles')
<link href="{{ url('assets/css/24-CDS-plan-page.css') }}" rel="stylesheet" />
@endsection
@section('content')



<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 {{ Route::currentRouteName() }}
 <h1 class="CDSSiteplans-Main-h1">Choose Your Perfect Plan</h1>
            <p class="CDSSiteplans-Main-subtitle">Join thousands of satisfied customers and unlock premium features</p>
            
            <div class="CDSSiteplans-Main-trust-badges">
                <div class="CDSSiteplans-Main-trust-badge">
                    <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <span>4.9/5 Rating</span>
                </div>
                <div class="CDSSiteplans-Main-trust-badge">
                    <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                    <span>SSL Secured</span>
                </div>
                <div class="CDSSiteplans-Main-trust-badge">
                    <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    <span>50K+ Users</span>
                </div>
                <div class="CDSSiteplans-Main-trust-badge">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>Money-Back Guarantee</span>
                </div>
            </div>
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="CDSSiteplans-Main-toggle-section">
            <div class="CDSSiteplans-Main-toggle-wrapper">
                <div class="CDSSiteplans-Main-toggle">
                    <div class="CDSSiteplans-Main-toggle-slider" id="toggleSlider"></div>
                    <div class="CDSSiteplans-Main-toggle-option CDSSiteplans-Main-active"  onclick="togglePlan('subscription')" id="basic-toggle">Monthly</div>
                
                    <div class="CDSSiteplans-Main-toggle-option" onclick="togglePlan('onetime')" id="basic-toggle">Lifetime</div>
                </div>
            </div>
            <div class="CDSSiteplans-Main-save-badge" id="saveBadge" style="display: none;">Save up to 70%!</div>
        </div>

        <div class="CDSSiteplans-Main-plans-container" id="plansContainer">
            <!-- Plans will be populated by JavaScript -->
        </div>
        <div id="common-skeleton-loader" style="display:none;">
            @include('components.loaders.membership-plans-loader')              
        </div>
			</div>
	
	</div>
  </div>
</div>
<div class="CDSSiteplans-Main-bg-animation"></div>


@endsection

@section('javascript')
<script type="text/javascript">

    let currentMode = '{{$type}}';
    
    // Initialize with default plan type
    loadData("{{$type}}");
    
    function togglePlan(mode) {
        currentMode = mode;
        const options = document.querySelectorAll('.CDSSiteplans-Main-toggle-option');
        const slider = document.getElementById('toggleSlider');
        const saveBadge = document.getElementById('saveBadge');
        
        options.forEach(option => option.classList.remove('CDSSiteplans-Main-active'));
        
        let activeIndex = 0;
        if (mode === 'subscription') {
            activeIndex = 0;
            saveBadge.style.display = 'none';
        } else {
            activeIndex = 1;
            saveBadge.style.display = 'block';
            saveBadge.textContent = 'Save up to 70%!';
        }
        
        options[activeIndex].classList.add('CDSSiteplans-Main-active');
        
        // Calculate slider position
        let leftPosition = 8;
        for (let i = 0; i < activeIndex; i++) {
            leftPosition += options[i].offsetWidth + 8;
        }
        
        slider.style.left = leftPosition + 'px';
        slider.style.width = options[activeIndex].offsetWidth + 'px';
        
        // Load data for the selected plan type
        loadData(mode);
    }

    function loadData(type) {
        $("#membership-loader").show();
        $.ajax({
            type: "POST",
            url: BASEURL + '/membership-plans/ajax-list',
            data: {
                _token: csrf_token,
                type: type,
            },
            dataType: 'json',
            beforeSend: function() {
                $("#common-skeleton-loader").show();
            },
            success: function(data) {
              
                $("#plansContainer").html(data.contents);
                $("#common-skeleton-loader").hide();
                // Update save badge based on plan type
                const saveBadge = document.getElementById('saveBadge');
                if (type === 'onetime') {
                    saveBadge.style.display = 'block';
                    saveBadge.textContent = 'Save up to 70%!';
                } else {
                    saveBadge.style.display = 'none';
                }
            },
            complete: function() {
                $("#membership-loader").hide(); 
            }
        });
    }
    
    // Initialize the toggle slider position
    $(document).ready(function() {
        // Set initial slider position based on current mode
        setTimeout(() => {
            const options = document.querySelectorAll('.CDSSiteplans-Main-toggle-option');
            const slider = document.getElementById('toggleSlider');
            let activeIndex = currentMode === 'onetime' ? 1 : 0;
            
            let leftPosition = 8;
            for (let i = 0; i < activeIndex; i++) {
                leftPosition += options[i].offsetWidth + 8;
            }
            
            slider.style.left = leftPosition + 'px';
            slider.style.width = options[activeIndex].offsetWidth + 'px';
            
            // Set initial active class
            options.forEach(option => option.classList.remove('CDSSiteplans-Main-active'));
            options[activeIndex].classList.add('CDSSiteplans-Main-active');
        }, 100);
    });
</script>
@endsection