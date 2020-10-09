 
@extends('layouts.master')
@section('content')  
<!-- Page Header-->
<header class="page-header">
    <div class="container-fluid">
        <h2 class="no-margin-bottom">Users</h2>
    </div>
</header>
<?php
$isEdit = false;
$title = "Add User";
$firstname = old('firstname');
$lastname = old('lastname');
$status = old('status');
$role_id = old('role_id');
$username = old('username');
$email = old('email');

if (isset($data['user_id']) && !empty($data['user_id'])) {
    $isEdit = true;
    $title = "Edit User";
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];
    $status = $data['status'];
    $role_id = $data['role_id'];
    $username = $data['username'];
    $email = $data['email'];
}
?>
<!-- Breadcrumb-->
<div class="breadcrumb-holder container-fluid">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
        <li class="breadcrumb-item">Users</li>
        <li class="breadcrumb-item active">{{$title}}</li>
    </ul>
</div>
<!-- Dashboard Header Section    -->
<section class="dashboard-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">  
                    <div class="card-header ">
                        <div class="card-caption">
                            <h3 class="h4">{{$title}}</h3>
                        </div>
                        <div class="card-action">
                            <a href="{{ url('list-users')}}" class="btn btn-primary">List Users</a>
                        </div>
                    </div>
                    <div class="card-body"> 
                        @if(session()->has('error'))
                        <div class="alert alert-danger mb-4">
                            {{ session()->get('error') }}
                        </div>
                        @endif
                        @if($isEdit)
                        <form class="" action="{{ url('edit-user-data') }}/{{$data['user_id']}}" method="POST">
                            @else
                            <form class="" action="{{ url('add-user-data') }}" method="POST">
                                @endif 
                                @csrf
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">First Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control text-capitalize" name="firstname"  value="{{ $firstname }}">
                                        <div style="color:red">{{($errors->first('firstname'))}}</div> 
                                    </div>
                                </div>
                                <div class="line"></div> 
<!--                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Last Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control text-capitalize" name="lastname" value="{{ $lastname }}">
                                        <div style="color:red">{{($errors->first('lastname'))}}</div> 
                                    </div>
                                </div>-->
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Status</label>
                                    <div class="col-sm-9">
                                        <select name="status" class="form-control " >
                                            <option value="1" <?= ( $status == 1) ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= ( $status == 0) ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                        <div style="color:red">{{($errors->first('status'))}}</div> 
                                    </div>
                                </div>
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Role</label>
                                    <div class="col-sm-9">
                                        <select name="role_id" class="form-control ">
                                            @foreach($roles as $val)
                                            <option value="{{$val->role_id}}" <?= ( $val->role_id == $role_id) ? 'selected' : '' ?>>{{$val->name}}</option>
                                            @endforeach
                                        </select>
                                        <div style="color:red">{{($errors->first('role_id'))}}</div> 
                                    </div>
                                </div>
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">User Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="username"  value="{{$username}}">
                                        <div style="color:red">{{($errors->first('username'))}}</div> 
                                    </div>
                                </div>
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Email</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="email"  value="{{ $email}}">
                                        <div style="color:red">{{($errors->first('email'))}}</div> 
                                    </div>
                                </div>
                                @if(!$isEdit)
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="password">
                                        <div style="color:red">{{($errors->first('password'))}}</div> 
                                    </div>
                                </div>
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Confirm Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="confirmpassword">
                                        <div style="color:red">{{($errors->first('confirmpassword'))}}</div> 
                                    </div>
                                </div>
                                @endif
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <div class="col-sm-4 offset-sm-3">
                                        @if($isEdit)
                                        <a href="{{ url('list-users')}}" class="btn btn-secondary">Cancel</a>
                                        <input type="submit" value="Edit" class="btn btn-primary">
                                        @else 
                                        <button type="reset" class="btn btn-secondary">Cancel</button>
                                        <input type="submit" value="Add" class="btn btn-primary">
                                        @endif 
                                    </div>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</section>
@stop