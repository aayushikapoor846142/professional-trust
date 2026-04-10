 <header>
<div class="header-content">
<nav class="navbar navbar-expand-lg navbar-light sticky-header">
    <div class="mobile-block">
        <div class="mobile-header-block">
        <a class="navbar-brand" href="{{url('/')}}">
                <img src="assets/images/logo.png" alt="Logo" class="logo-img">
            </a>
        
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse justify-content-end align-items-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'front.index' ? 'active' : ''}}" href="{{url('/')}}">Panel </a>
                </li>
               
                @if(auth()->check())
                <li class="nav-item">
                    <ul class="header-dropdown">
                        <li class="dropdown nav-item">
                            <a  class="dropdown-toggle nav-link">Welcome {{ auth()->user()->first_name}}</a>
                            <ul class="dropdown-menu">
                                <li class="p-0"><a class="nav-link blue-text p-0 logout-link" href="{{ url('logout') }}"><i class="fa-solid fa-power-off"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> 
                @endif
            </ul>
           
            
            <ul class="navbar-nav">
                <li class="nav-item">
                                    
                </li>
            </ul>
        </div>
    </div>
    </nav>
 </div>
</header>
