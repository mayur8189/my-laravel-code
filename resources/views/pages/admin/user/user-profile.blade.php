@extends('layouts.master')
@section('styles') 
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css"> 
@stop
@section('content')  
<!-- Page Header-->
<header class="page-header">
    <div class="container-fluid">
        <h2 class="no-margin-bottom">Users</h2>
    </div>
</header>
<?php
$title = "User Profile";
$applicationtoken = old('applicationtoken');
$apitoken = old('apitoken');
$account = old('account');

if (isset($data['user_id']) && !empty($data['user_id'])) {
 
    $applicationtoken = $data['application_token'];
    $apitoken = $data['api_token'];
    $account = $data['account'];
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
                        @if(session()->has('success'))
                      <div class="alert alert-success mb-4 alert-dismissible">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          {{ session()->get('success') }}
                      </div>
                      @endif
                      @if(session()->has('error'))
                      <div class="alert alert-danger mb-4  alert-dismissible">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          {{ session()->get('error') }}
                      </div>
                      @endif
                <div class="card">  
                    <div class="card-header ">
                        <div class="card-caption">
                            <h3 class="h4">{{$title}}</h3>
                        </div>
                        
                    </div>
                    <div class="card-body"> 
                            <form class="" action="{{ url('edit-profile') }}" method="POST">
                               @csrf
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Application Token</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control text-capitalize" name="applicationtoken"  value="{{ $applicationtoken }}" >
                                        <div style="color:red">{{($errors->first('applicationtoken'))}}</div> 
                                    </div>
                                </div>
                              
                                <div class="line"></div> 
                          
                             
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">API Token</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="apitoken"  value="{{ $apitoken }}">
                                        <div style="color:red">{{($errors->first('apitoken'))}}</div> 
                                    </div>
                                </div>
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Account </label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="account"  value="{{ $account }}">
                                        <div style="color:red">{{($errors->first('account'))}}</div> 
                                    </div>
                                </div>
                      
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <div class="col-sm-4 offset-sm-3">
                                        <a href="{{ url('dashboard')}}" class="btn btn-secondary">Cancel</a> 
                                        <input type="submit" value="Retrieve Events"  class="btn btn-primary">
                                        
                                    </div>
                                </div>
                            </form>
                    </div>
                </div>
                            
            </div>
        </div>
    </div> 
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> 
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script> 
</section>
@stop