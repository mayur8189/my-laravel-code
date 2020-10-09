<nav class="side-navbar <?= (Auth::User()->menu_status == 1) ? '' : 'shrinked' ?> ">
    <!-- Sidebar Header-->
    <div class="sidebar-header d-flex align-items-center">
        <div class="avatar"><a href="{{url('profile')}}"> <img src="{{ URL::asset('resources/assets/img/avatar.png')}}" alt="{{Auth::User()->username}}" class="img-fluid rounded-circle"></a>
            <a class="mt-2" href="{{url('profile')}}"><h5><p>Profile</p></h5></a> 
        </div>
        <div class="title">
            <a href="{{url('profile')}}"> <h1 class="h4"> {{Auth::User()->username}}</h1></a>  
        </div>
    </div>
     <!--Sidebar Navidation Menus<span class="heading">Main</span>-->
    <ul class="list-unstyled">
        <?php
        $userroutes = array("addUser", 'listUsers');
        $roleroutes = array("addRole", 'listRoles');
        $eventroutes = array('listEvents');
        $exteventroutes = array('EventList');
        $lookuproutes = array('ticketMasterLookup');

        $user = Auth::User();
        ?> 
        <li class="{{ (request()->is('/') || request()->is('dashboard')) ? 'active' : '' }}"><a href="{{url('/')}}"> <i class="fa fa-th-large"></i>Dashboard </a></li>

        <?php
        if ($user->can('view-user')) {
            ?>
            <li class="<?= (in_array(request()->route()->getActionMethod(), $userroutes) ? 'active ' : '') ?>"><a href="{{url('list-users')}}"> <i class="fa fa-user-circle-o"></i>Users </a></li> 
        <?php } ?>
        <?php
        if ($user->can('view-role')) {
            ?>
            <li class="<?= (in_array(request()->route()->getActionMethod(), $roleroutes) ? 'active ' : '') ?>"><a href="{{url('list-roles')}}"> <i class="fa fa-user-plus"></i>Roles </a></li> 

        <?php } ?>
        <?php
        if ($user->hasRole('admin')) {
            ?>
            <li class="<?= (in_array(request()->route()->getActionMethod(), $eventroutes) ? 'active ' : '') ?>"><a href="{{url('list-events')}}"> <i class="fa fa-calendar"></i>Events<small> (TicketMaster)</small></a></li> 
        <?php } ?>
        <?php
        if ($user->can('view-event')) {
            ?>
            <li class="<?= (in_array(request()->route()->getActionMethod(), $exteventroutes) ? 'active ' : '') ?>"><a href="{{url('event-list')}}"> <i class="fa fa-calendar"></i>Events<small> (StubHub & VividSeats)</small></a></li> 
            <?php } ?>
             <!--<li class="<?= (in_array(request()->route()->getActionMethod(), $lookuproutes) ? 'active ' : '') ?>"><a href="{{url('ticket-master-code-lookup')}}"> <i class="fa fa-ticket"></i>Ticketmaster Code Lookup</a></li>--> 
    </ul> 
</nav>