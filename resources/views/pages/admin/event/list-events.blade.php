 
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
        <li class="breadcrumb-item active">List Events</li>
    </ul>
</div>
<!-- Dashboard Header Section    -->
<section class="dashboard-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12"> 
                <div class="card"> 
                    <div class="card-header ">
                        <div class="card-caption">
                            <h3 class="h4">List Events</h3>
                        </div> 
                        <div class="card-action">
                            <input id="dateselect" type="text" class="form-control" name="dateselect" value="<?= date('m/d/Y', strtotime('-1 days')) ?>-<?= date('m/d/Y') ?>">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="card-body">
                        <div class="table-responsive">                       
                            <table class="table table-striped table-hover" id="listevents" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Added Date</th>
                                        <th>Event Type</th>
                                        <th>Name</th>
                                        <th class="nowrap">Presale Date <br><small>S : StartDate / E : EndDate</small></th>
                                        <th>Onsale Date</th>
                                        <th>Event Date</th>
                                        <th>City</th>
                                        <th class="nowrap">Venue</th>
                                        <th>Min</th>
                                        <th>Max</th>
                                        <th>URL</th>
                                        <th>Seat Map</th>
                                        <th>Source</th>
                                    </tr>
                                </thead>
                            </table>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    listuser = $('#listevents').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,
        serverMethod: "POST",
        lengthMenu: [10, 25, 50, 100],
        "ajax": {
            url: APP_URL + '/list-events-record',
            type: "POST",
            data: function (d) {
                d.dateselect = $('#dateselect').val();
            },
        },
        columns: [
            {data: 'created_date', name: 'created_date'},
            {data: 'event_type', name: 'event_type'},
            {data: 'name', name: 'name',"sWidth":'100px'},
            {data: 'presaledate', name: 'presaledate'},
            {data: 'onsaledate', name: 'onsaledate'},
            {data: 'eventdate', name: 'eventdate'},
            {data: 'city', name: 'city'},
            {data: 'venue', name: 'venue', 'sClass': 'word-break',"sWidth":'100px'},
            {data: 'min_price', name: 'min_price'},
            {data: 'max_price', name: 'max_price'},
            {data: 'url', name: 'url', "sClass": 'word-break'},
            {data: 'seatmap', name: 'seatmap'},
            {data: 'source', name: 'source', "visible": false},
        ], "order": [[0, 'desc']]
    });

    var start = moment().subtract(1, 'days');
    var end = moment();
    $('#dateselect').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'Last 1000 Days': [moment().subtract(1000, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
});
$('#dateselect').on('apply.daterangepicker', function (ev, picker) {
    listuser.ajax.reload(null, false);
});
</script>
@stop