 
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
        <li class="breadcrumb-item">Profile</li>
        <li class="breadcrumb-item active">List Events</li>
    </ul>
</div>
<!-- Dashboard Header Section    -->
<?php
//echo "<pre>";
//print_r($setdata);
//echo "<pre>";
?>
<section class="dashboard-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12"> 
                <div class="card"> 
                    <div class="card-header ">
                        <div class="card-caption">
                            <h3 class="h4">List Events</h3>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="card-body">
                        <div class="table-responsive">                       
                            <table class="table table-striped table-hover" id="listevents" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><div class="i-checks mr-2">
                                       <input id="checkevent_all" type="checkbox"  class="checkbox-template">
                                    </div> </th>
                                        <th>Event</th>
                                        <th class="nowrap">Venue</th>
                                        <th>Event Date</th>
                                        <th>Source</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                        @foreach($setdata as $value)
                                        <tr>   
                                            <td><div class="i-checks mr-2">
                                        <input id="addevent_{{$value['id']}}" type="checkbox" value="{{$value['id']}}" data-s="{{$value['datas']}}"  class="checkbox-template addbulkevent" >
                                    </div> </td>   
                                        <td><a href="{{$value['weburl']}}" target="#"> {{$value['name']}}</a></td>
                                        <td>{{$value['venue']}}</td>
                                        <td>{{$value['eventdate']}}</td>
                                        <td>{{$value['source']}}</td>
                                        </tr>
                                        @endforeach
                                    
                                     
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                        <div class="card-footer">
                        <div class="pull-right">
                                <a href="javascript:void(0);" class="btn btn-primary addbulkeventbtn" >Add Selected Event</a>
                                <a href="{{ url('profile')}}" class="btn btn-danger">Close</a>
                            </div> 
                    </div>
                  
                </div>
            </div>
        </div>
    </div> 
</section>
@stop 
@section('scripts')   
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> 
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script> 
<script>
var seattbl = '';
$('.addbulkeventbtn').on('click', function() {
    var selected = [];
    $('.addbulkevent').each(function(i,v){
        if($(this).is(":checked")){
           selected.push({
                id: $(this).val(),
                datas: $(this).attr('data-s')
            });
        }
    })
    if (selected.length > 0) {
        $.ajax({
            url: APP_URL + "/addBulkCheckedEvent",
            type: 'POST',
            dataType: 'JSON',
            data: {
                'addBulk': selected
            },
            beforeSend: function(xhr) {
                $('.globalLoading').show();
            },
            success: function(data, textStatus, jqXHR) {
                if (data.status) {
                    location.reload();
                } else {
                    $('.globalLoading').hide();
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Please Select Atleast one event',
                        confirmButtonColor: '#796AEE',
                    })
                }
            },
            error: function(data, jqXHR, textStatus, errorThrown) { 
                 $('.globalLoading').hide();
                Swal.fire({
                    type: 'error',
                    title: 'Oops...',
                    text: 'API Request ERROR',
                    confirmButtonColor: '#796AEE',
                })
            }
        })
    } else {
        Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'Please Select Atleast one event',
            confirmButtonColor: '#796AEE',
        })
    }
})    
    
$(document).ready(function() {
    var isHide = localStorage.getItem("isPastHideEvent");
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    listevent = $('#listevents').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6' <'row'<'col-md-6 pull-left' <'pull-right hidepastevent'>> <'col-md-6 pull-right'f>>>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        processing: true,
        serverSide: false,
        pageLength: 100,
        serverMethod: "POST",
        stateSave: true,
        lengthMenu: [100, 250, 500],
        columns: [
             {
                data: 'check',
                name: 'check'
            },
            {
                data: 'Event',
                name: 'Event'
            },
            {
                data: 'venue',
                name: 'venue',
                'sClass': 'word-break'
            },
            {
                data: 'eventdate',
                name: 'eventdate',
                "type": "numeric"
            }, 
            {
                data: 'source',
                name: 'source'
            },
           
        ],
        "order": [
            [0, 'desc']
        ]
    });
   
});
$('#checkevent_all').on('click', function(){
      // Check/uncheck all checkboxes in the table
      var rows  = listevent.rows({ page: 'current' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });

</script>
@stop