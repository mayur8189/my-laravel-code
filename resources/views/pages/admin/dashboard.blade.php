 
@extends('layouts.master')
@section('styles')
@stop
@section('content') 
<header class="page-header">
    <div class="container-fluid">
        <h2 class="no-margin-bottom">Dashboard</h2>
    </div>
</header>  
<?php 
//$Test = "SDs";
?>
<!-- Dashboard Header Section    -->
<section class="dashboard-header">
    <div class="container-fluid">
        <div class="row"> 
            <div class="col-lg-8 mx-auto">
                <div class="card">  
                    <div class="card-header ">
                        <div class="card-caption">
                            <h3 class="h4">Change Password</h3>
                        </div>
                        
                    </div>
                    <div class="card-body"> 
                        @if(session()->has('error'))
                        <div class="alert alert-danger mb-4">
                            {{ session()->get('error') }}
                        </div>
                        @endif
                        
                          @if(session()->has('success'))
                        <div class="alert alert-success mb-4">
                            {{ session()->get('success') }}
                        </div>
                        @endif
                     
                      
                            <form class="" action="{{ url('change-password') }}" method="POST">
                               @csrf
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Current Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control text-capitalize" name="oldpass"  value="" placeholder="Enter Current Password">
                                        <div style="color:red">{{($errors->first('oldpass'))}}</div> 
                                    </div>
                                </div>
                              
                                <div class="line"></div> 
                          
                             
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">New Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="newpass"  value="" placeholder="New Password">
                                        <div style="color:red">{{($errors->first('newpass'))}}</div> 
                                    </div>
                                </div>
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Comfirm Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="confirmpass"  value="" placeholder="Comfirm Password">
                                        <div style="color:red">{{($errors->first('confirmpass'))}}</div> 
                                    </div>
                                </div>
                      
                                <div class="line"></div> 
                                <div class="form-group row">
                                    <div class="col-sm-4 offset-sm-3">
                                        <a href="{{ url('dashboard')}}" class="btn btn-secondary">Cancel</a> 
                                        <input type="submit" value="submit" class="btn btn-primary">
                                        
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