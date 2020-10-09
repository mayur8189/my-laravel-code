 
@extends('layouts.master')
@section('styles') 
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css"> 
@stop
@section('content') 
<header class="page-header">
    <div class="container-fluid">
        <h2 class="no-margin-bottom">Events</h2>
    </div>
</header>  
<div class="breadcrumb-holder container-fluid">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
        <li class="breadcrumb-item">Users</li>
        <li class="breadcrumb-item active">List Events</li>
    </ul>
</div>

<?php
if (count($data['eventsame']) !== 0) {
    foreach ($data['eventsame'] as $sameevent) {
        $self_id[] = $sameevent->self_id;
        $source[$sameevent->self_id] = $sameevent->url;
    }
//    echo '<pre>';
//    print_r($source);
//    exit;
} else {
    $self_id = array();
    $source = array();
}
?>
<!-- Dashboard Header Section    -->

<section class="dashboard-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12"> 
                <div class="card mb-0">
                    <div class="card-header ">
                        <div class="card-caption">
                            <h3 class="h4">User Events</h3>
                        </div> 

                    </div>
                    <div class="clearfix"></div>
                    <div class="card-body">  
                        <div class="table-responsive">                       
                            <table class="table table-striped table-hover" id="listevents" style="width: 100%;">
                                <thead>
                                    <tr>
                                       
                                        <th>Name</th> 
                                        <th>Event Date</th>
                                        <th>City</th>
                                        <th class="nowrap">Venue</th>
                                        <th>Min</th>
                                        <th>Max</th>
                                        <th>Ticket Count</th> 
                                        <th>Source</th>
                                
                                 
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach($data['events'] as $event)
                                    <tr>
                                    
                                        <td>{{$event['name']}}</td>
                                        <td class="nowrap">
                                            <span style="display: none"><?= $event['eventDate'] ?></span>
                                            <?php
                                            if (isset($event['eventDate']) && !empty($event['eventDate'])) {
                                                ?>
                                                <?= date('M j, Y - g:i a', $event['eventDate']) ?>
                                                <?php
                                            } else {
                                                echo "N/A";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($event['city']) && !empty($event['city'])) {
                                                ?>
                                                <?= $event['city'] ?>
                                                <?php
                                            } else {
                                                echo "N/A";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $loc = '';
                                            if (isset($event['venue']) && !empty($event['venue'])) {
                                                $loc = $event['venue'];
                                            } else {
                                                $loc = 'N/A';
                                            }
                                            echo $loc;
                                            ?>
                                        </td>
                                        <?php
                                        $minprice = 'N/A';
                                        $maxprice = 'N/A';
                                        $ticket = 'N/A';
                                        if (isset($event['ticketInfo'])) {
                                            $cuncy = $event['currencyCode'];
                                            if ($event['currencyCode'] == "USD") {
                                                $cuncy = "$";
                                            }
                                            if ($event['source'] == "vividseats") {
                                                $cuncy = "$";
                                            }
                                            $ticketInfo = json_decode($event['ticketInfo'], true);
                                            if (isset($ticketInfo['minListPrice']) && !empty($ticketInfo['minListPrice'])) {
                                                $minprice = $cuncy . ' ' . $ticketInfo['minListPrice'];
                                            }
                                            if (isset($ticketInfo['maxListPrice']) && !empty($ticketInfo['maxListPrice'])) {
                                                $maxprice = $cuncy . ' ' . $ticketInfo['maxListPrice'];
                                            }
                                            if (isset($ticketInfo['totalTickets']) && isset($ticketInfo['totalListings'])) {
                                                $ticket = ' <span class="nowrap">Total Tickets - ' . $ticketInfo['totalTickets'] . '</span><br><span class="nowrap"> Total Listings - ' . $ticketInfo['totalListings'] . '</span>';
                                            }
                                        }
                                        ?>
                                        <td class="nowrap"><?= $minprice ?></td>
                                        <td class="nowrap"><?= $maxprice ?></td>
                                        <td><?= $ticket ?></td> 
                                        <td>
                                            <?php
                                            $isSingle = true;
                                            if (isset($event['source']) && $event['source'] == "stubhub") {
                                                ?>
                                                <span style="display: none">stubhub</span>
                                                <a href="https://www.stubhub.com/<?= $event['url'] ?>" target="_blank"><img src="<?= url('resources/assets/img/stubhub.png') ?>" style="width:32px" /></a>
                                                <?php
                                                if (in_array($event['self_id'], $self_id) && isset($source[$event['type_eventid']])) {
                                                    $isSingle = false;
                                                    ?>    
                                                    <span style="display: none">vividseats</span>
                                                    <a href="<?= (isset($source[$event['type_eventid']]) ? $source[$event['type_eventid']] : '') ?>" target="_blank"> <img src="<?= url('resources/assets/img/vividseats.png') ?>" style="width:32px" /></a>
                                                <?php } ?>    
                                                <?php
                                            } else if (isset($event['source']) && $event['source'] == "vividseats") {
                                                ?>
                                                <span style="display: none">vividseats</span>
                                                <a href="<?= $event['url'] ?>" target="_blank"> <img src="<?= url('resources/assets/img/vividseats.png') ?>" style="width:32px" /></a>
                                                <?php
                                                if (in_array($event['self_id'], $self_id) && isset($source[$event['type_eventid']])) {
                                                    $isSingle = false;
                                                    ?>    
                                                    <span style="display: none">stubhub</span>
                                                    <a href="<?= (isset($source[$event['type_eventid']]) ? "https://www.stubhub.com/" . $source[$event['type_eventid']] : '') ?>" target="_blank"> <img src="<?= url('resources/assets/img/stubhub.png') ?>" style="width:32px" /></a>
                                                <?php } ?>  

                                                <?php
                                            }
                                            ?>
                                        </td>
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{url('/list-users')}}" >Back</a>
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
$('.searchbyName').on('click', function() {
    var form_data = $("#searchevent").serialize();
    $.ajax({
        url: APP_URL + "/searchEvent",
        type: 'POST',
        dataType: 'JSON',
        data: form_data,
        beforeSend: function(xhr) {
            $('.globalLoading').show();
        },
        success: function(data, textStatus, jqXHR) {
            if (data.status) { 
                $('#tablesearchevent').DataTable().destroy();
                $('.tablesearchevent').parent('table').parent('div').show();
                $('.tablesearchevent').html(data.res_data);
                seattbl = $('#tablesearchevent').DataTable({
                    destroy: true,
                    lengthMenu: [50, 100, 150, 200,250],
                    columns: [{
                        data: 'add',
                        name: 'add',
                        orderable: false
                    },{
                        data: 'event',
                        name: 'event', 
                    },{
                        data: 'venue',
                        name: 'venue', 
                    },{
                        data: 'eventdate',
                        name: 'eventdate', 
                    },{
                        data: 'source',
                        name: 'source', 
                    }], 
                    "order": [
                        [1, 'desc']
                    ],
                    pageLength: 50,
                })
               
                $('.globalLoading').hide();
            } else {
                $('.globalLoading').hide();
                Swal.fire({
                    type: 'error',
                    title: 'Oops...',
                    html: data.msg,
                    confirmButtonColor: '#796AEE',
                })
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
             $('.globalLoading').hide();
                Swal.fire({
                    type: 'error',
                    title: 'Oops...',
                    text: 'API Request ERROR',
                    confirmButtonColor: '#796AEE',
                })
        }
    })
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
                data: 'name',
                name: 'name'
            },
            {
                data: 'eventdate',
                name: 'eventdate',
                "type": "numeric",
            },
            {
                data: 'city',
                name: 'city'
            },
            {
                data: 'venue',
                name: 'venue',
                'sClass': 'word-break'
            },
            {
                data: 'min_price',
                name: 'min_price'
            },
            {
                data: 'max_price',
                name: 'max_price'
            },
            {
                data: 'ticketcount',
                name: 'ticketcount'
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
   
})

</script>
@stop