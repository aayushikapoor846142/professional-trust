 <header class="CDSMain-header CDSDashboard-header-fixed">
      <div class="CDSMainNavigation-hamburger CDSMainNavigation-dashboard-mobile-button" onclick="toggleMenu()">
             <img src="assets/images/d-icons/list.png" class="img-fluid list-icon" />
        </div>
        <div class="CDSMainNavigation-logo"> 
            <a class="navbar-brand" href="{{url('/')}}/">
                <img src="{{url('/')}}/assets/images/logo-c.png" alt="Logo" class="img-fluid logo-img">
            </a></div>
           
          <div class="CDSMainNavigation-nav-container">
              <!-- The entire nav (main + sub + dropdown items) in one container -->
              <div id="CDSMainNavigation-navBar">
                  <div class="cds-close-btn">
                      <button class="close-btn" onclick="toggleSidebar()"><i class="fa-sharp fa-regular fa-xmark"
                              aria-hidden="true"></i></button>
                  </div>
                  <!-- Row 1: Main Items -->
                  <ul class="CDSMainNavigation-mainItems">
                      <li><a href="{{ url('/') }}">Home</a></li>


                      <li class="">
                          <a class="CDSMainNavigation-dropbtn" data-target="CDSMainNavigation-restDropdown">Manage Staff</a>
                          <ul class="CDSMainNavigation-simple-dropdown-content" id="CDSMainNavigation-restDropdown">
                              <li> <a class="reporting-flex" href="{{url('articles')}}">Articles</a></li>
                              <li> <a class="reporting-flex" href="{{url('knowledgebase')}}">Knowledge Base</a></li>
                              <li> <a class="reporting-flex" href="{{url('guides')}}">Guides</a></li>
                              <li> <a class="reporting-flex" href="{{url('advisories')}}">Advisories</a> </li>
                          </ul>
                      </li>


                      <!-- Simple Dropdown: "About" -->
                      <li
                          class="{{ (Route::currentRouteName() == 'report.individual' || Route::currentRouteName() == 'report.company' || Route::currentRouteName() == 'report.social.media' || Route::currentRouteName() == 'report.unauthorized') ? 'active' : ''}}">
                          <a href="#" class="CDSMainNavigation-dropbtn"
                              data-target="CDSMainNavigation-aboutDropdown">Reporting Forms</a>
                          <!-- The simple dropdown content -->
                          <ul class="CDSMainNavigation-simple-dropdown-content" id="CDSMainNavigation-aboutDropdown">
                              <li><a class="{{ Route::currentRouteName() == 'reporting-form-for-public' ? 'sub-menu-active' : ''}}"
                                      href="{{ url('reporting-form-for-public') }}"><i
                                          class="fa-duotone fa-regular fa-angle-right"></i><span> <span>Public</span>
                                          Reporting Form</span></a></li>
                              <li><a class="{{ Route::currentRouteName() == 'report.unauthorized' ? 'sub-menu-active' : ''}}"
                                      href="{{url('reporting-form-for-professionals')}}"><i
                                          class="fa-duotone fa-regular fa-angle-right"></i><span> <span> Professional
                                          </span>Reporting Form </span></a></li>
                              <li> <a class="{{ Route::currentRouteName() == 'reporting-form-against-misleading-social-media' ? 'sub-menu-active' : ''}}"
                                      href="{{ url('reporting-form-against-misleading-social-media') }}"><i
                                          class="fa-duotone fa-regular fa-angle-right"></i><span> <span> General </span>
                                          Reporting Social Content</span>
                                  </a> </li>

                          </ul>
                      </li>
                      <!-- Profile triggers the slide-in panel -->

                  </ul>


              </div>
             
              <!-- Hamburger (mobile) -->
              @if(auth()->check())
              <a href="javascript:;" class="CDSMainNavigation-profile-dropdown cds-mobile-profile" id="CDSMainNavigation-profileBtn">
                      <img id="imagePreview" src="{{ auth()->user()->profile_image ? userDirUrl(auth()->user()->profile_image, 't') : url('assets/images/avatar-male-blank.png') }}" alt="Profile Image">
          
              </a>
          @endif
              <div class="CDSMainNavigation-hamburger" id="CDSMainNavigation-hamburgerBtn">
                  <span></span>
                  <span></span>
                  <span></span>
              </div>
          </div> 
        @if(auth()->check())
        <!-- Profile Panel Slide-In -->
        <div id="CDSMainNavigation-profilePanel">
          <div class="d-flex justify-content-between">
            
            <h2>Welcome {{auth()->user()->first_name." ".auth()->user()->last_name}}{{fetchProfessionalCompanyName(auth()->user())}}</h2>
            <button class="CDSMainNavigation-close-profile" id="CDSMainNavigation-closeProfileBtn">
                X
            </button>
          </div>
            @if(auth()->check() && auth()->user()->role == 'supporter')
            <li><a href="{{ baseUrl('/') }}"> Dashboard</a></li>
            @endif
            <a href="@if(auth()->user()->role=='professional'){{ baseUrl('profile')}}@else{{ baseUrl('edit-profile')}}@endif">Edit Profile</a>
            <a href="{{ baseUrl('change-password/'. auth()->user()->unique_id)}}">Change Password</a>
            <a href="javascript:;" class="logout-link">Logout</a>
        </div>
        <!-- add notification -->
        <div class="cds-case-notification-wrapper ms-3">
            <div class="cds-case-notification-bell" onclick="toggleCaseNotifications()">
                🔔
                <span class="cds-case-badge" id="badgeCount">{{getChatNotification()->where('is_read',0)->count()}}</span>
            </div>
            <div class="cds-case-notification-container" id="notificationPanel">
                <div class="cds-case-notification-header d-flex align-items-center justify-content-between gap-1">
                    <label>Notifications</label>  
                    <a href="{{baseUrl('/notifications')}}" class="btn btn-sm btn-primary">View All</a>
                </div>
                @include("components.notification");
            </div>
        </div>
        <!-- end notification -->
        @endif
    </header>
  