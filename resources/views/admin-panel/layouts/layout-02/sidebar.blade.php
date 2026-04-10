{{--<nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
    <div class="sb-sidenav-menu" id="cds-sideMenu">
        <div class="nav">
            <div class="toggle-menu" onclick="toggleMenu()">
                <a class="nav-link cds-noSearch" onclick="event.preventDefault()">
                    <div class="sb-nav-link-icon">
                        <i class="fa-regular fa-arrow-down-to-line"></i>
                    </div>
                </a>
            </div>
            <div class="menu-search">
                <input type="text" class="form-control" onkeydown="searchMenu()" name="search_menu" id="search_menu" placeholder="Search Menu" />
            </div>
            <div class="sb-sidenav-menu-heading">Core</div>
            <div class="cds-cName">
                <div class="d-flex align-baseline">
                    <div class="flex-shrink-0">
                        <i class="fa-light fa-building fa-lg"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        {{fetchProfessionalCompanyName(auth()->user())}}
                    </div>
                </div>
            </div>
            <hr class="" />
            <a class="nav-link {{ Route::currentRouteName() == 'panel.list' ? 'active' : ''}}" href="{{ baseUrl('/') }}">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-grid-2"></i>
                </div>
                <span class="nav-link-text">Dashboard</span>
                <span class="cds-tooltip">Dashboard</span>

            </a>
            <a class="nav-link {{ Route::currentRouteName() == 'panel.myProfile' ? 'active' : ''}}" href="@if(auth()->user()->role=='professional'){{ baseUrl('profile')}}@else{{ baseUrl('edit-profile')}}@endif">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-user-pen"></i>
                </div>
                <span class="nav-link-text">My Profile</span>
                <span class="cds-tooltip">My Profile</span>
            </a>

            @if(auth()->user()->role == "professional")
            <a class="nav-link {{ Route::currentRouteName() == 'panel.action.list' || Route::currentRouteName() == 'panel.module.list' || Route::currentRouteName() == 'panel.module.role-privileges' ? 'active' : ''}} collapsed"
                href="#" onclick="showSubmenu(event, this)">
                <div class="sb-nav-link-icon"><i class="fa-regular fa-lock-keyhole"></i></div>
                <span class="nav-link-text">
                    Permission
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </span>
                <span class="cds-tooltip">Permission</span>

            </a>
            <div class=" {{ Route::currentRouteName() == 'panel.role-privileges.list'  || Route::currentRouteName() == 'panel.module.list' || Route::currentRouteName() == 'panel.module.role-privileges'? 'show' : ''}}"
                id="collapseLayoutsPermission" aria-labelledby="headingFive" style="display: none;">
                    <a class="nav-link cds-noSearch {{ Route::currentRouteName() == 'panel.role-privileges' ? 'active' : ''}} "
                        href="{{ baseUrl('role-privileges') }}">Role Previleges</a>
            </div>
            @endif
            @if(checkPrivilege([
                    'route_prefix' => 'panel.transactions/history',
                    'module' => 'professional-transactions/history',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.transaction/invoice',
                    'module' => 'professional-transaction/invoice',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.points-earn-history',
                    'module' => 'professional-points-earn-history',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.my-membership-plans',
                    'module' => 'professional-my-membership-plans',
                    'action' => 'list'
                ]))      
            <a class="nav-link {{ Route::currentRouteName() == 'panel.amount-contributed.list' || Route::currentRouteName() == 'panel.support-payments.list' ||  Route::currentRouteName() == 'panel.points-earn-history.list' ? 'active' : ''}} collapsed" href="#" onclick="showSubmenu(event, this)">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-users"></i>
                </div>
                <span class="nav-link-text">
                    Contribution
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i>
                    </div>
                </span>
                <span class="cds-tooltip">Contribution</span>
            </a>
            <div class="collapse" style="display: none;">
                @if(checkPrivilege([
                    'route_prefix' => 'panel.transactions/history',
                    'module' => 'professional-transactions/history',
                    'action' => 'list'
                ]))      
                <a class="nav-link {{ Route::currentRouteName() == 'panel.transactions/history.list' ? 'active' : ''}}"
                    href="{{ baseUrl('transactions/history') }}">
                    <div class="sb-nav-link-icon"> 
                        <i class="fa-solid fa-money-check-dollar"></i>              
                    </div>
                    <span class="nav-link-text">Trasaction History</span>
                    <span class="cds-tooltip">Trasaction History</span>
                </a>
                @endif
                 @if(checkPrivilege([
                    'route_prefix' => 'panel.transaction/invoice',
                    'module' => 'professional-transaction/invoice',
                    'action' => 'list'
                ]))      
                <a class="nav-link {{ Route::currentRouteName() == 'panel.transaction/invoice.list' ? 'active' : ''}}" href="{{ baseUrl('/transaction/invoice') }}">
                    <div class="sb-nav-link-icon">
                        <i class="fa-regular fa-credit-card"></i>
                    </div>
                    <span class="nav-link-text">Invoices</span>
                    <span class="cds-tooltip">Invoices</span>
                </a>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.points-earn-history',
                    'module' => 'professional-points-earn-history',
                    'action' => 'list'
                ]))  
                <a class="nav-link {{ Route::currentRouteName() == 'panel.points-earn-history.list' ? 'active' : ''}}" href="{{ baseUrl('/points-earn-history') }}">
                    <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-clock-rotate-left"></i>
                    </div>
                    <span class="nav-link-text">Points Earn History</span>
                    <span class="cds-tooltip">Points Earn History</span>
                </a>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.my-membership-plans',
                    'module' => 'professional-my-membership-plans',
                    'action' => 'list'
                ]))
                <a class="nav-link {{ Route::currentRouteName() == 'panel.my-membership-plans.list' ? 'active' : ''}}" href="{{ baseUrl('my-membership-plans') }}">
                    <div class="sb-nav-link-icon">
                        <i class="fa-regular fa-address-card"></i>
                    </div>
                    <span class="nav-link-text">My Membership Plans</span>
                    <span class="cds-tooltip">My Membership Plans</span>
                </a>
                @endif
                <a class="nav-link {{ Route::currentRouteName() == 'panel.professional-support' ? 'active' : ''}}" href="{{ baseUrl('/payment-methods/payment') }}">
                    <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-clock-rotate-left"></i>
                    </div>
                    <span class="nav-link-text">Payment</span>
                    <span class="cds-tooltip">Payment</span>
                </a>
            </div>
            @endif
            @if(checkPrivilege([
                'route_prefix' => 'panel.document-folders',
                'module' => 'professional-document-folders',
                'action' => 'list'
            ]))
            <a class="nav-link {{ Route::currentRouteName() == 'panel.document-folders' ? 'active' : ''}}" href="{{ baseUrl('document-folders') }}">
                <div class="sb-nav-link-icon">
                <i class="fa-regular fa-file-vector"></i>
                </div>
                <span class="nav-link-text">Document Folders</span>
                <span class="cds-tooltip">Document Folders</span>

            </a>
            @endif
            
             <a class="nav-link {{ Route::currentRouteName() == 'panel.document-folders' ? 'active' : ''}}" href="{{ baseUrl('earning-report') }}">
                <div class="sb-nav-link-icon">
                <i class="fa-regular fa-file-vector"></i>
                </div>
                <span class="nav-link-text">Earning Reports</span>
                <span class="cds-tooltip">Earning Reports</span>

            </a>
            @if(checkPrivilege([
                'route_prefix' => 'panel.invoices',
                'module' => 'professional-invoices',
                'action' => 'list'
            ]))
            <a class="nav-link {{ Route::currentRouteName() == 'panel.invoices' ? 'active' : ''}}" href="{{ baseUrl('invoices') }}">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa fa-file"></i>
                </div>
                <span class="nav-link-text">Global Invoices</span>
                <span class="cds-tooltip">Global Invoices</span>

            </a>
            @endif
            @if(checkPrivilege([
                'route_prefix' => 'panel.roles',
                'module' => 'professional-roles',
                'action' => 'list'
            ]))
            <a class="nav-link {{ Route::currentRouteName() == 'panel.roles' ? 'active' : ''}}" href="{{ baseUrl('roles') }}">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa fa-file"></i>
                </div>
                <span class="nav-link-text">Roles</span>
                <span class="cds-tooltip">Roles</span>

            </a>
            @endif
            @if(checkPrivilege([
                'route_prefix' => 'panel.staff',
                'module' => 'professional-staff',
                'action' => 'list'
            ]))
            <a class="nav-link {{ Route::currentRouteName() == 'panel.staff.list' || Route::currentRouteName() == 'panel.staff.trash-staffs' ? 'active' : ''}} collapsed" href="#" onclick="showSubmenu(event, this)">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-users"></i>
                </div>
                <span class="nav-link-text">
                    Staffs
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i>
                    </div>
                </span>
                <span class="cds-tooltip">Staffs</span>
            </a>
            <div class="collapse" style="display: none;">
                <a class="nav-link cds-noSearch cds-noSearch"
                    href="{{ baseUrl('staff') }}">
                    <div class="sb-nav-link-icon">                      
                    </div>
                    <span class="nav-link-text">Active Staff's</span>
                    <span class="cds-tooltip">Active Staff's</span>
                </a>
                <a class="nav-link cds-noSearch cds-noSearch"
                    href="{{ baseUrl('staff/trash-staff-list') }}">
                    <div class="sb-nav-link-icon">
                    </div>
                    <span class="nav-link-text">Trash Staff's</span>
                    <span class="cds-tooltip">Trash Staff's</span>
                </a>
                
            </div>
            @endif
            @if(checkPrivilege([
                    'route_prefix' => 'panel.time-duration',
                    'module' => 'professional-time-duration',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.appointment-types',
                    'module' => 'professional-appointment-types',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking-flow',
                    'module' => 'professional-appointment-booking-flow',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'add'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'view-calender'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.block-dates',
                    'module' => 'professional-block-dates',
                    'action' => 'list'
                ]))
            <a class="nav-link {{ Route::currentRouteName() == 'panel.appointment-booking.list' || Route::currentRouteName() == 'panel.appointment-booking.list' ? 'active' : ''}} collapsed" href="#" onclick="showSubmenu(event, this)">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-users"></i>
                </div>
                <span class="nav-link-text">
                Appointment System
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i>
                    </div>
                </span>
                <span class="cds-tooltip">Appointment System</span>
            </a>
            <div class="collapse" style="display: none;">
                @if(checkPrivilege([
                    'route_prefix' => 'panel.time-duration',
                    'module' => 'professional-time-duration',
                    'action' => 'list'
                ]))
                <a class="nav-link cds-noSearch cds-noSearch"
                    href="{{ baseUrl('time-duration') }}">
                    <div class="sb-nav-link-icon">                      
                    </div>
                    <span class="nav-link-text">Time Duration</span>
                    <span class="cds-tooltip">Time Duration</span>
                </a>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-types',
                    'module' => 'professional-appointment-types',
                    'action' => 'list'
                ]))
                <a class="nav-link cds-noSearch cds-noSearch"
                    href="{{ baseUrl('appointment-types') }}">
                    <div class="sb-nav-link-icon">
                    </div>
                    <span class="nav-link-text">Appointment Types</span>
                    <span class="cds-tooltip">Appointment Types</span>
                </a>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking-flow',
                    'module' => 'professional-appointment-booking-flow',
                    'action' => 'list'
                ]))
                <a class="nav-link cds-noSearch cds-noSearch"
                    href="{{ baseUrl('appointment-booking-flow') }}">
                    <div class="sb-nav-link-icon">
                    </div>
                    <span class="nav-link-text">Appointment Booking Workflow</span>
                    <span class="cds-tooltip">Appointment Booking Workflow</span>
                </a>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'list'
                ]))
                <a class="nav-link cds-noSearch cds-noSearch"
                    href="{{ baseUrl('appointment-booking') }}">
                    <div class="sb-nav-link-icon">
                    </div>
                    <span class="nav-link-text">Appointment Bookings</span>
                    <span class="cds-tooltip">Appointment Bookings</span>
                </a>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'add'
                ]))
                <a class="nav-link cds-noSearch cds-noSearch"
                    href="{{ baseUrl('appointment-booking/save-booking') }}">
                    <div class="sb-nav-link-icon">
                    </div>
                    <span class="nav-link-text">Book an Appointment </span>
                    <span class="cds-tooltip">Book an Appointment</span>
                </a>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-booking',
                    'module' => 'professional-appointment-booking',
                    'action' => 'view-calender'
                ]))
                <a class="nav-link cds-noSearch cds-noSearch"
                    href="{{ baseUrl('appointment-booking/calendar') }}">
                    <div class="sb-nav-link-icon">
                    </div>
                    <span class="nav-link-text">Appointment Calendar</span>
                    <span class="cds-tooltip">Appointment Calendar</span>
                </a>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.block-dates',
                    'module' => 'professional-block-dates',
                    'action' => 'list'
                ]))
                <a class="nav-link cds-noSearch cds-noSearch"
                    href="{{ baseUrl('block-dates') }}">
                    <div class="sb-nav-link-icon">
                    </div>
                    <span class="nav-link-text">Block Dates</span>
                    <span class="cds-tooltip">Block Dates</span>
                </a>
                @endif
            </div>
            @endif
            <a class="nav-link collapsed" href="#" onclick="showSubmenu(event, this)">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-messages"></i>
                </div>
                <span class="nav-link-text">
                    Message
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i></div>
                </span>
                <span class="cds-tooltip">Message</span>
            </a>
            <div class="collapse" style="display: none;">
              <a class="nav-link cds-noSearch {{ Route::currentRouteName() == 'panel.msg_settings' ? 'active' : ''}}" href="{{ baseUrl('/message-settings') }}">
                <div class="sb-nav-link-icon">
                </div>
                <span class="nav-link-text">Message Settings</span>
                <span class="cds-tooltip">Message Settings</span>
            </a>
            @if(checkPrivilege([
                'route_prefix' => 'panel.chat-invitations',
                'module' => 'professional-chat-invitations',
                'action' => 'list'
            ]))
                <a class="nav-link cds-noSearch {{ Route::currentRouteName() == 'panel.group' ? 'active' : ''}}" href="{{ baseUrl('chat-invitations') }}">
                    <div class="sb-nav-link-icon">
                    </div>
                    <span class="nav-link-text">Chat Invitations</span>
                    <span class="cds-tooltip">Chat Invitations</span>
                </a>
            @endif
            @if(checkPrivilege([
                'route_prefix' => 'panel.message-centre',
                'module' => 'professional-message-centre',
                'action' => 'list'
            ]))
            <a class="nav-link cds-noSearch {{ Route::currentRouteName() == 'panel.message-centre' ? 'active' : ''}}" href="{{ baseUrl('/message-centre') }}">
                <div class="sb-nav-link-icon">
                </div>
                <span class="nav-link-text">Message Centre</span>
                <span class="cds-tooltip">Message Centre</span>
            </a>
            @endif
            @if(checkPrivilege([
                'route_prefix' => 'panel.group',
                'module' => 'professionalgroup',
                'action' => 'list'
            ]))
            <a class="nav-link cds-noSearch {{ Route::currentRouteName() == 'panel.group' ? 'active' : ''}}" href="{{ baseUrl('/group/chat') }}">
                <div class="sb-nav-link-icon">
                </div>
                <span class="nav-link-text">Group Messages</span>
                <span class="cds-tooltip">Group Messages</span>
            </a>
            @endif
        </div>
            @if(checkPrivilege([
                'route_prefix' => 'panel.forms',
                'module' => 'professional-forms',
                'action' => 'list'
            ]))
            <a class="nav-link {{ Route::currentRouteName() == 'panel.forms' ? 'active' : ''}}" href="{{ baseUrl('/forms') }}">
                <div class="sb-nav-link-icon">
                <i class="fa-regular fa-note"></i>
                </div>
                <span class="nav-link-text">Forms</span>
                <span class="cds-tooltip">Forms</span>

            </a>
            @endif
            @if(checkPrivilege([
                'route_prefix' => 'panel.articles',
                'module' => 'professional-articles',
                'action' => 'list'
            ]))
            <a class="nav-link {{ Route::currentRouteName() == 'panel.articles.list' ? 'active' : ''}}" href="{{ baseUrl('articles') }}">
                <div class="sb-nav-link-icon"><i class="fa-regular fa-memo-pad" aria-hidden="true"></i></div>
                <span class="nav-link-text">Articles</span>
                <span class="cds-tooltip">Articles</span>
            </a>
            @endif
             @if(checkPrivilege([
                'route_prefix' => 'panel.discussion-threads',
                'module' => 'professional-discussion-threads',
                'action' => 'list'
            ]))
            <a class="nav-link  collapsed" href="#" onclick="showSubmenu(event, this)">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-rss"></i>
                </div>
                <span class="nav-link-text">
                    Discussion Board
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i></div>
                </span>
                <span class="cds-tooltip">Discussion Board</span>
            </a>
            <div class="collapse" style="display: none;">

           
            <a class="nav-link cds-noSearch {{ Route::currentRouteName() == 'panel.discussion-threads.list' ? 'active' : ''}}" href="{{ baseUrl('/discussion-threads/manage') }}">               
                <span class="nav-link-text">All Threads</span>
                <span class="cds-tooltip">All Threads</span>
            </a>
           
            </div>
            @endif


            @if(checkPrivilege([
                'route_prefix' => 'panel.membership-plans',
                'module' => 'professional-membership-plans',
                'action' => 'list'
            ]))
            <a class="nav-link {{ Route::currentRouteName() == 'panel.membership-plans.list' ? 'active' : ''}}" href="{{ baseUrl('membership-plans') }}">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-address-card"></i>
                </div>
                <span class="nav-link-text">Membership</span>
                <span class="cds-tooltip">Membership</span>
            </a>
            @endif
        
            @if(checkPrivilege([
                'route_prefix' => 'panel.my-services',
                'module' => 'professional-my-services',
                'action' => 'list'
            ]))
            <a class="nav-link {{ Route::currentRouteName() == 'panel.userServices' ? 'active' : ''}}" href="{{ baseUrl('/my-services') }}">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-sliders"></i>
                </div>
                <span class="nav-link-text">My Services</span>
                <span class="cds-tooltip">My Services</span>

            </a>
            @endif
             @if(checkPrivilege([
                    'route_prefix' => 'panel.cases',
                    'module' => 'professional-cases',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.case-with-professionals',
                    'module' => 'professional-case-with-professionals',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.predefined-case-stages',
                    'module' => 'professional-predefined-case-stages',
                    'action' => 'list'
                ]))
            <a class="nav-link  collapsed" href="#" onclick="showSubmenu(event, this)">
                <div class="sb-nav-link-icon">
                <i class="fa-regular fa fa-file"></i>
                </div>
                <span class="nav-link-text">
                    Cases
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i></div>
                </span>
                <span class="cds-tooltip">Cases</span>
            </a>
            <div class="collapse" style="display: none;">
                @if(checkPrivilege([
                    'route_prefix' => 'panel.cases',
                    'module' => 'professional-cases',
                    'action' => 'list'
                ]))
                <a class="nav-link cds-noSearch {{ Route::currentRouteName() == 'panel.cases.list' ? 'active' : ''}}" href="{{ baseUrl('cases') }}">
                    <div class="sb-nav-link-icon"></div>
                    <span class="nav-link-text">Post Cases</span>
                    <span class="cds-tooltip">Post Cases</span>
                </a>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.case-with-professionals',
                    'module' => 'professional-case-with-professionals',
                    'action' => 'list'
                ]))
                <a class="nav-link cds-noSearch {{ Route::currentRouteName() == 'panel.case-with-professionals.list' ? 'active' : ''}}" href="{{ baseUrl('case-with-professionals') }}">
                    <div class="sb-nav-link-icon"></div>
                    <span class="nav-link-text">My Cases</span>
                    <span class="cds-tooltip">My Cases</span>
                </a>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.predefined-case-stages',
                    'module' => 'professional-predefined-case-stages',
                    'action' => 'list'
                ]))
                <a class="nav-link cds-noSearch {{ Route::currentRouteName() == 'panel.predefined-case-stages.list' ? 'active' : ''}}" href="{{ baseUrl('predefined-case-stages') }}">
                    <div class="sb-nav-link-icon"></div>
                    <span class="nav-link-text">Predefined Case Stages</span>
                    <span class="cds-tooltip">Predefined Case Stages</span>
                </a>
                @endif
            </div>    
            @endif     
            <!-- api settings-->
         
                @if(checkPrivilege([
                    'route_prefix' => 'panel.send-invitations',
                    'module' => 'professional-send-invitations',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.review-invitations',
                    'module' => 'professional-review-invitations',
                    'action' => 'list'
                ]) || checkPrivilege([
                    'route_prefix' => 'panel.get-reviews',
                    'module' => 'professional-get-reviews',
                    'action' => 'list'
                ]))
              <a class="nav-link {{ Route::currentRouteName() == 'panel.sendInvitations' || Route::currentRouteName() == 'panel.reviewsRequests' || Route::currentRouteName() == 'panel.getReviews' ? 'active' : ''}} collapsed" href="#" onclick="showSubmenu(event, this)">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-users"></i>
                </div>
                <span class="nav-link-text">
                    Reviews
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i>
                    </div>
                </span>
                <span class="cds-tooltip">Reviews</span>
            </a>
            <div class="collapse" style="display: none;">
                
                @if(checkPrivilege([
                    'route_prefix' => 'panel.send-invitations',
                    'module' => 'professional-send-invitations',
                    'action' => 'list'
                ]))
                    <a class="nav-link {{ Route::currentRouteName() == 'panel.sendInvitations' ? 'active' : ''}}" href="{{ baseUrl('/send-invitations') }}">
                    <div class="sb-nav-link-icon">
                        <i class="fa-regular fa-user-plus"></i>
                    </div>
                    <span class="nav-link-text">Send Invitations</span>
                    <span class="cds-tooltip">Send Invitations</span>                
                </a>
                @endif

                @if(checkPrivilege([
                    'route_prefix' => 'panel.review-invitations',
                    'module' => 'professional-review-invitations',
                    'action' => 'list'
                ]))
                
                <a class="nav-link {{ Route::currentRouteName() == 'panel.reviewsRequests' ? 'active' : ''}}" href="{{ baseUrl('/review-invitations') }}">
                    <div class="sb-nav-link-icon">
                        <i class="fa-regular fa-user-magnifying-glass"></i>
                    </div>
                    <span class="nav-link-text">Review Invitations</span>
                    <span class="cds-tooltip">Review Invitation</span>
                </a>
                @endif
                 @if(checkPrivilege([
                    'route_prefix' => 'panel.get-reviews',
                    'module' => 'professional-get-reviews',
                    'action' => 'list'
                ]))
               <a class="nav-link {{ Route::currentRouteName() == 'panel.getReviews' ? 'active' : ''}}" href="{{ baseUrl('/get-reviews') }}">
                <div class="sb-nav-link-icon">
                    <i class="fa-regular fa-star"></i>
                </div>
                <span class="nav-link-text">Reviews</span>
                <span class="cds-tooltip">Reviews</span>
            </a>
            @endif
            </div>
@endif
         
           
               <a class="nav-link {{ Route::currentRouteName() == 'deviceList' ? 'active' : ''}}" href="{{ baseUrl('confirm-login/'.auth()->user()->unique_id) }}">
                <div class="sb-nav-link-icon"><i class="fa-solid fa-laptop-mobile"></i></div>
                <span class="nav-link-text">Login Devices</span>
                <span class="cds-tooltip">Login Devices</span>
            </a>
        </div>
    </div>
   
</nav>

<script>
function searchMenu() {
    let input = document.getElementById("search_menu").value.toLowerCase();
    let menuItems = document.querySelectorAll("#cds-sideMenu .nav-link:not(.cds-noSearch)");
    if(input !== '') {
        // When there is input, filter the items
        menuItems.forEach(item => {
            let text = item.textContent.toLowerCase();
            item.style.display = text.includes(input) ? "flex" : "none";
        });
    } else {
        // When input is cleared, remove display: none from all items
        menuItems.forEach(item => {
            item.style.display = "";  // Reset the display property
        });
    }
}

// Attach the function to the 'input' event to trigger immediately when typing or deleting
document.getElementById("search_menu").addEventListener("input", searchMenu);
</script>

--}}

@php 

$menuItems = [
    [
        'title' => 'Dashboard',
        'icon' => 'fa-regular fa-grid-2',
        'route' => 'panel',
        'url' => baseUrl('/') . '/panel',
    ],
    [
        'title' => 'My Profile',
        'icon' => 'fa-regular fa-user-pen',
        'route' => 'panel.myProfile',
        'url' => baseUrl('/') . '/profile',
    ],
    [
        'title' => 'Permission',
        'icon' => 'fa-regular fa-lock-keyhole',
        'submenu' => [
            [
                'title' => 'Role Previleges',
                'route' => 'panel.role-privileges.list',
                'url' => baseUrl('/') . '/role-privileges',
            ],
        ],
    ],
    [
        'title' => 'Contribution',
        'icon' => 'fa-regular fa-users',
        'submenu' => [
            [
                'title' => 'Transaction History',
                'icon' => 'fa-solid fa-money-check-dollar',
                'privileges' => [
                    'route_prefix' => 'panel.transactions/history',
                    'module' => 'professional-transactions/history',
                    'action' => 'list'
                ],
                'url' => baseUrl('/') . '/transactions/history',
            ],
            [
                'title' => 'Invoices',
                'icon' => 'fa-regular fa-credit-card',
                'url' => baseUrl('/') . '/transaction/invoice',
            ],
            [
                'title' => 'Points Earn History',
                'icon' => 'fa-regular fa-clock-rotate-left',
                'url' => baseUrl('/') . '/points-earn-history',
            ],
            [
                'title' => 'My Membership Plans',
                'icon' => 'fa-regular fa-address-card',
                'url' => baseUrl('/') . '/my-membership-plans',
            ],
            [
                'title' => 'Payment',
                'icon' => 'fa-regular fa-clock-rotate-left',
                'url' => baseUrl('/') . '/payment-methods/payment',
            ],
        ],
    ],
    [
        'title' => 'Document Folders',
        'icon' => 'fa-regular fa-file-vector',
        'url' => baseUrl('/') . '/document-folders',
    ],
    [
        'title' => 'Earning Reports',
        'icon' => 'fa-regular fa-file-vector',
        'url' => baseUrl('/') . '/earning-report',
    ],
    [
        'title' => 'Global Invoices',
        'icon' => 'fa-regular fa fa-file',
        'url' => baseUrl('/') . '/invoices',
    ],
    [
        'title' => 'Roles',
        'icon' => 'fa-regular fa fa-file',
        'url' => baseUrl('/') . '/roles',
    ],
    [
        'title' => 'Staffs',
        'icon' => 'fa-regular fa-users',
        'submenu' => [
            [
                'title' => "Active Staff's",
                'url' => baseUrl('/') . '/staff',
            ],
            [
                'title' => "Trash Staff's",
                'url' => baseUrl('/') . '/staff/trash-staff-list',
            ],
        ],
    ],
    [
        'title' => 'Appointment System',
        'icon' => 'fa-regular fa-users',
        'submenu' => [
            [
                'title' => 'Time Duration',
                'url' => baseUrl('/') . '/time-duration',
            ],
            [
                'title' => 'Appointment Types',
                'url' => baseUrl('/') . '/appointment-types',
            ],
            [
                'title' => 'Appointment Booking Workflow',
                'url' => baseUrl('/') . '/appointment-booking-flow',
            ],
            [
                'title' => 'Appointment Bookings',
                'url' => baseUrl('/') . '/appointment-booking',
            ],
            [
                'title' => 'Book an Appointment',
                'url' => baseUrl('/') . '/appointment-booking/save-booking',
            ],
            [
                'title' => 'Appointment Calendar',
                'url' => baseUrl('/') . '/appointment-booking/calendar',
            ],
            [
                'title' => 'Block Dates',
                'url' => baseUrl('/') . '/block-dates',
            ],
        ],
    ],
];
@endphp
<div class="vertical-menu expanded-mode" id="sidebar">
    <div class="menu-content" id="menuContent">
        @foreach($menuItems as $menu)
        <div class="menu-item-wrapper">
            <div class="menu-item has-sub-menu">
                @if(isset($menu['submenu']) && !empty($menu['submenu']))
                    <div class="menu-left">
                        <span class="menu-icon"><i class="{{ $menu['icon'] }}"></i></span>
                        <span class="menu-label">{{ $menu['title'] }}</span>
                    </div>
                    <span class="arrow-indicator"></span>
                @else
                 @if(!isset($menu['privileges']) || checkPrivilege($menu['privileges']))
                    <div class="menu-left">
                       
                        <a href="{{ $menu['url'] }}">
                            <span class="menu-icon"><i class="{{ $menu['icon'] }}"></i></span>
                            <span class="menu-label">{{ $menu['title'] }}</span>
                        </a>
                    </div>
                @endif
                @endif
            </div>
            @if(isset($menu['submenu']) && !empty($menu['submenu']))
            <ul class="inline-sub-menu">
                @foreach($menu['submenu'] as $submenu)
                 @if(!isset($submenu['privileges']) || checkPrivilege($submenu['privileges']))
                <li><a href="{{ $submenu['url'] }}">{{ $submenu['title'] }}</a></li>
                @endif
                @endforeach
            </ul>
            @endif
        </div>
        @endforeach
    </div>
    <div class="more-indicator" id="moreIndicator" onclick="scrollToBottom()" style="display: block;"> </div>
    <button class="toggle-button" onclick="toggleSidebar()">Toggle Menu</button>
</div>
<ul id="floatingSubmenu" class="floating-submenu" style="display: none;"></ul>

@section("javascript")
<script>
const sidebar = document.getElementById('sidebar');
const menuContent = document.getElementById('menuContent');
const moreIndicator = document.getElementById('moreIndicator');
const floatingSubmenu = document.getElementById('floatingSubmenu');
let userToggled = false;
$(document).ready(function(){
    $(document).on("click",".has-sub-menu",function(){
        $(this).parents('.menu-item-wrapper').toggleClass("active");
    });
    updateMoreIndicator();
});
menuContent.addEventListener('wheel', (e) => {
  e.preventDefault();
  menuContent.scrollTop += e.deltaY;
  updateMoreIndicator();
}, { passive: false });
window.addEventListener('resize', () => {
  updateMoreIndicator();
});

function updateMoreIndicator() {
  const items = menuContent.querySelectorAll('.menu-item-wrapper');
  let hiddenCount = 0;
  const contentRect = menuContent.getBoundingClientRect();

  items.forEach(item => {
    const rect = item.getBoundingClientRect();
    if (rect.bottom > contentRect.bottom) hiddenCount++;
  });

  if (hiddenCount > 0) {
    moreIndicator.style.display = 'block';
    moreIndicator.textContent = `+${hiddenCount} more item${hiddenCount > 1 ? 's' : ''}`;
  } else {
    moreIndicator.style.display = 'none';
  }
}

</script>
@endsection