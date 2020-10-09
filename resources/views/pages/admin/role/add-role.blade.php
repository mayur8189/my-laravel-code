 
@extends('layouts.master') 
@section('content')  
<!-- Page Header-->
<header class="page-header">
    <div class="container-fluid">
        <h2 class="no-margin-bottom">Roles</h2>
    </div>
</header>
<?php
$isEdit = false;
$title = "Add Role";
if (isset($data['role_id']) && !empty($data['role_id'])) {
    $isEdit = true;
    $title = "Edit Role";
}
?>
<!-- Breadcrumb-->
<div class="breadcrumb-holder container-fluid">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
        <li class="breadcrumb-item">Roles</li>
        <li class="breadcrumb-item active">{{$title}}</li>
    </ul>
</div>
<!-- Dashboard Header Section    -->
<section class="dashboard-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 mx-auto">
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
                        @if($isEdit)
                        <form class="" action="{{ url('edit-role-data') }}/{{$data['role_id']}}" method="POST">
                            @else
                            <form class="" action="{{ url('add-role-data') }}" method="POST">
                                @endif
                                @csrf
                                <div class="form-group">
                                    <label class="form-control-label">Role Name</label>
                                    <input type="text" name="name" placeholder="Role Name" class="form-control text-capitalize" value="<?= (isset($data['name']) && !empty($data['name']) ? $data['name'] : '') ?>">
                                    <div style="color:red">{{($errors->first('name'))}}</div>  
                                </div>
                                <div class="form-group">       
                                    @if($isEdit)
                                    <a href="{{ url('list-roles')}}" class="btn btn-secondary">Cancel</a>
                                    <input type="submit" value="Edit" class="btn btn-primary">
                                    @else 
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                    <input type="submit" value="Add" class="btn btn-primary">
                                    @endif
                                </div> 
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</section>
@stop