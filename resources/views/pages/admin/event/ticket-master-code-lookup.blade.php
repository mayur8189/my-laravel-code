 
@extends('layouts.master')
@section('styles') 
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@stop
@section('content')  
<!-- Page Header-->
<header class="page-header">
    <div class="container-fluid">
        <h2 class="no-margin-bottom">Events</h2>
    </div>
</header>
<!-- Breadcrumb-->
<div class="breadcrumb-holder container-fluid">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
        <li class="breadcrumb-item">Events</li>
        <li class="breadcrumb-item active">TicketMaster Code Lookup</li>
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
                            <h3 class="h4">TicketMaster Code Lookup</h3>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form class="form-inline" name="validateOffercode"  method="POST">
                                    @csrf
                                    <div class="col-md-12 mt-3 pl-0">
                                        <div style="color:red" id="event-error"></div>  
                                    </div>
                                    <div class="form-group">
                                        <label for="tmeventid" >Ticketmaster Event ID: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                        <input id="tmeventid" type="text" name="event_id" placeholder="Enter Event ID" class="mr-3 form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="tmcode"  >Code(s): &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                        <input id="tmcode" type="text" name="offercode" placeholder="Enter Code" class="mr-3 form-control">
                                    </div> 
                                    <div class="col-md-12 mt-3">
                                        <div class="form-group float-right">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div> 
                                    </div>
                                </form> 
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="form-group">
                                    <label >Result: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                    <textarea class="form-control" id="textarearesult" rows="10"></textarea> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            </section>
            @stop 
            @section('scripts')    
            <script>
                $('document').ready(function () {
                    $("form[name='validateOffercode']").submit(function (e) {
                        e.preventDefault();
                        $('#event-error').html('');
                        $.ajax({
                            url: APP_URL + "/validateOfferCode",
                            type: 'POST',
                            dataType: 'JSON',
                            data: $("form[name='validateOffercode']").serialize(),
                            beforeSend: function () {
                                $('.globalLoading').show();
                            },
                            success: function (data) {
                                $('.globalLoading').hide();
                                if (data.status) {
                                    $('#textarearesult').html(data.msg);
                                } else {
                                    $('#event-error').html(data.msg);
                                }
                            }
                        })
                    })
                })
            </script>
            @stop