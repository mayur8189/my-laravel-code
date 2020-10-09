<!DOCTYPE html>
<html>
    <head>
        @include('includes.head')
        @yield('styles')
    </head>
    <body>
        <div class="page">
            <div class="globalLoading">  
                <img src="{{asset('resources/assets/img/spinner-loading.gif')}}"  style="width:250px;">
            </div>
            <!-- Main Navbar-->
            @include('includes.header') 
            <div class="page-content d-flex align-items-stretch"> 
                <!-- Side Navbar -->
                @include('includes.sidebar')
                <div class="content-inner <?= (Auth::User()->menu_status == 1) ? '' : 'active' ?> "> 
                    @yield('content') 
                    <!-- Page Footer-->
                    @include('includes.footer')
                </div>
            </div>
        </div>
        @include('includes.foot')
        @yield('scripts')
    </body>
</html>