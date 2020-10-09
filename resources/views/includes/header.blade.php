<header class="header">
    <nav class="navbar"> 
        <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
                <!-- Navbar Header-->
                <div class="navbar-header">
                    <!-- Navbar Brand --><a href="index.html" class="navbar-brand d-none d-sm-inline-block">
                        <div class="brand-text d-none d-lg-inline-block"><span>Ticket Broker Tools</span></div>
                        <div class="brand-text d-none d-sm-inline-block d-lg-none"><strong>TOD</strong></div></a>
                    <!-- Toggle Button-->
                    <a id="toggle-btn" href="#" class="menu-btn <?= (Auth::User()->menu_status == 1) ? 'active' : ''?>"><span></span><span></span><span></span></a>
                </div>
                <!-- Navbar Menu -->
                <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">  
                    <!-- Logout    -->
                    <li class="nav-item"><a href="{{ url('logout')}}" class="nav-link logout"> <span class="d-none d-sm-inline">Logout</span><i class="fa fa-sign-out"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>