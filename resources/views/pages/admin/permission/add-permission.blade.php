 
@extends('layouts.master') 
@section('content')  
<!-- Page Header-->
<header class="page-header">
    <div class="container-fluid">
        <h2 class="no-margin-bottom">Permissions</h2>
    </div>
</header>
<?php
$isEdit = false;
$title = "Edit Permission";
//echo "<pre>";
//print_r($rollPermission);exit;
if (count($rollPermission) !== 0) {
    foreach($rollPermission as $permission){
        $rolePid[] = $permission->permission_id;
        $checktext[$permission->permission_id] = $permission->total_track;
        $checktextsearch[$permission->permission_id] = $permission->total_searchartist;
    }
   // print_r($checktextsearch);exit;
}else {
    $rolePid = array();
    $checktext = array();
    $checktextsearch=array();
}
?>

<!-- Breadcrumb-->
<div class="breadcrumb-holder container-fluid">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
        <li class="breadcrumb-item">Roles</li>
        <li class="breadcrumb-item">Permission</li>
        <li class="breadcrumb-item active">{{$title}}</li>
    </ul>
</div>
<!-- Dashboard Header Section    -->
<section class="dashboard-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-7 mx-auto">
                <div class="card">  
                    <div class="card-header ">
                        <div class="card-caption">
                            <h3 class="h4">{{$title}}</h3>
                        </div>
                        <div class="card-action">
                            <a href="{{ url('list-roles')}}" class="btn btn-primary">List Roles</a>
                        </div>
                    </div>
                    <div class="card-body"> 
                        @if(session()->has('error'))
                        <div class="alert alert-danger mb-4">
                            {{ session()->get('error') }}
                        </div>
                        @endif
                      <?php 
//                            echo "<pre>"; 
//                            print_r($allPermission);
//                            echo "<pre>";
//                            exit;
                      ?> 
                        <form class="" action="{{ url('edit-rolePermission-data') }}/{{$data['role_id']}}" method="POST">
                      
                            @csrf
                            
                            <div class="row">
                                @foreach($allPermission as $permission)
                                   
                                    <?php //echo $permission['id'] ?> 
                                    
                                        <div class="col-md-4 btn-group">
                                            <label><input name="rolePermission[]" class="checkbox-template event-checkbox" type="checkbox" value="<?=$permission['id']?>" <?php if( in_array($permission['id'], $rolePid) ){ echo "checked";    } ?>> </label> 
                                        <!--<input type="checkbox" name="" id="" value="" />-->
                                        
                                            <label class="ml-3"><?= $permission['name'] ?></label>
                                        
                                         <?php if($permission['name'] == "Total Events"){ ?> 
                                            <input style="height: 25px;" type="text" placeholder="Total Number of Event" class="form-control col-md-4 mb-1 ml-2" name="totalevent" value="<?php if(array_key_exists(8,$checktext) && !empty($checktext[8])){ echo $checktext[8]; } else { echo '0';}?>" > 
                                                
                                        <?php } ?>   
                                        <?php if($permission['name'] == "Total Search"){ ?> 
                                            <input style="height: 25px;" type="text" placeholder="Total Number of Artist Search" class="form-control col-md-4 mb-1 ml-2" name="totalsearch" value="<?php if(array_key_exists(10,$checktextsearch) && !empty($checktextsearch[10])){ echo $checktextsearch[10]; } else { echo '0';}?>" > 
                                                
                                        <?php }?>
                                            
                                            
                                    </div>
                                @endforeach
                                </div>
                            
                               
                              
                           
                    </div>
                      <div class="clearfix card-footer-bd ">       
                                    <a href="{{ url('list-roles')}}" class="btn btn-secondary">Cancel</a>
                                    <input type="submit" value="Update" class="btn btn-primary">
                      </div>
                     </form>
                </div>
            </div>
        </div>
    </div> 
</section>

@stop