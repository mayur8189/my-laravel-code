 
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
        <li class="breadcrumb-item">Events</li>
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

@if($errors->any())
<div class="alert alert-danger mb-4">
    {{$errors->first()}}
</div>
@endif
<section class="dashboard-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12"> 
                <div class="card mb-0">
                    <div class="card-header ">
                        <div class="card-caption">
                            <h3 class="h4">Events</h3>
                        </div> 
                        <?php if (Auth::User()->can('add-event')) { ?>
                            <div class="card-action">
                                <a href=""  data-toggle="modal" data-target="#addEventModal" class="btn btn-primary float-right">Add Event</a>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="card-body">  
                        <div class="table-responsive">                       
                            <table class="table table-striped table-hover" id="listevents" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="display:none">Track</th> 
                                        <th>Name</th> 
                                        <th>Event Date</th>
                                        <th>City</th>
                                        <th class="nowrap">Venue</th>
                                        <th>Min</th>
                                        <th>Max</th>
                                        <th>Ticket Count</th> 
                                        <th>Source</th>
                                        <th>Actions</th>
                                        <th>Merge</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['events'] as $event)
                                    <tr>
                                        <td style="display:none"> <?php
                                            if (Auth::User()->user_id == $event['user_id']) {
                                                if ($event['is_track'] == '1') {
                                                    ?>
                                                    <div class=" mr-2"> 
                                                        <a href="javascript:void(0)" onclick="untrackevent(this)" data-id="{{$event['event_id']}}" data-val="{{$event['type_eventid']}}" class="btn btn-primary btn-sm" title="Untrack this event"><i class="fa fa-history"></i></a>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="i-checks mr-2">
                                                        <input id="checkevent_{{$event['event_id']}}" type="checkbox" value="{{$event['event_id']}}" data-val="{{$event['type_eventid']}}"  class="checkbox-template event-checkbox" >
                                                        <label for="checkevent_{{$event['event_id']}}"></label>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?></td>
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
                                        <td class="nowrap">
                                            <?php
                                            if (Auth::User()->user_id == $event['user_id']) {
                                                if ($event['is_track'] == '1') {
                                                    ?>
                                                    <a class="btn btn-primary btn-sm" href="javascript:void(0);" title="Show Track Graph" onclick="jsCode('<?= url('event-track-detail/' . $event['event_id']) ?>', '600', '750px', '<?= addslashes($event['name']) ?>');" ><i class="fa fa-bar-chart"></i></a>
                                                    <a class="btn btn-secondary btn-sm" href="javascript:void(0);" onclick="manual_check('<?= $event['event_id'] ?>','{{$event['type_eventid']}}')" title="Manual Check" ><i class="fa fa-play"></i></a>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td> <?php
                                            if (Auth::User()->user_id == $event['user_id']) {
                                                if ($isSingle) {
                                                    ?>
                                                    <div class="i-checks mr-2">
                                                        <input id="checkevent_{{$event['event_id']}}" type="checkbox" value="{{$event['type_eventid']}}" data-val="{{$event['type_eventid']}}"  class="checkbox-template checkbox_<?= $event['source'] ?>" >
                                                        <label for="checkevent_{{$event['event_id']}}"></label>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($event['eventDate']) && !empty($event['eventDate']) && date('Y-m-d', $event['eventDate']) > date('Y-m-d', strtotime('-1 day'))) {
                                                echo 'HIDE_PAST_DATA';
                                            }
                                            ?>
                                            <span style="display: none">
                                                <?= $event['ancestors'] ?>
                                                <?= $event['performers'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if (Auth::User()->can('total-events')) { ?>
                        <a class="btn btn-primary" href="javascript:void(0)" onclick="trackEvent()" style="display:none">Track</a>
                        <?php } ?>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="javascript:void(0)" onclick="mergeEvent()">Merge Selected</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- The Modal -->
<div class="modal fade " id="jsCodeModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" id="modal-dailog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"  id="jsTitle"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center holds-the-iframe" id="jsmodalBody">
                <iframe id="jsIframe"  frameborder="0"  width="100%"></iframe>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="addEventModal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"> 
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add Event</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="">
                    <div class="i-checks mr-4 pull-left">
                        <input id="checkevent_url" type="radio" name="addType" checked="" class="radio-template" onclick="$('.eventbyturl').show(); $('.eventbytartist').hide(); $('#addEventModal').find('.modal-dialog').removeClass('modal-lg')">
                        <label for="checkevent_url">By URL</label>
                    </div>
                    <?php //if (Auth::User()->can('total-search')) { ?>
                    <div class="i-checks mr-2  pull-left">
                        <input id="checkevent_name" type="radio"  name="addType" class="radio-template" onclick="$('.eventbytartist').show(); $('.eventbyturl').hide(); $('#addEventModal').find('.modal-dialog').addClass('modal-lg')">
                        <label for="checkevent_name">By Artist</label>
                    </div>
                    <?php //} ?>
                    <div class="clearfix"> </div>
                </div>
                <div class="clearfix"> </div>
                <div class="eventbyturl"> 
                    <form class="" id="addEventForm" name="addEvent"  method="POST"> 
                        @csrf 
                        <div class="form-group">
                            <label class="form-control-label">Event URL</label>
                            <input type="text" name="event" placeholder="Event URL" class="form-control" value="">
                            <div style="color:red" id="event-error"></div>  
                        </div>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary" >Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>

                <div class="eventbytartist " style="display: none"> 
                    <hr>
                    <form class="" id="searchevent">

                        @csrf  
                        <div class="form-group">
                            <label class="form-control-label pull-left  mr-4">Source</label>
                            <div class="i-checks mr-4 pull-left">
                                <input id="checkevent_stub" type="checkbox" name="source[]" value="stubhub" checked="" class="checkbox-template">
                                <label for="checkevent_stub">StubHub</label>
                            </div>
                            <div class="i-checks mr-2  pull-left">
                                <input id="checkevent_vivid" type="checkbox"  name="source[]" value="vivid" class="checkbox-template">
                                <label for="checkevent_vivid">VividSeats</label>
                            </div>
                            <div class="clearfix"> </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" name="artist" placeholder="Artist Name" class="mr-3 form-control" value="">
                                <div style="color:red" id="event-error"></div>  
                                <div class="input-group-append">
                                    <a href="javascript:void(0);"  class="btn btn-primary searchbyName">Search</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class="mt-3 mb-3">
                         
                            <div class="table-responsive" style="display: none;max-height: 500px">
                                <table class="table table-striped" id="tablesearchevent">
                                    <thead>
                                    <th>
                                        <input id="checkevent_all" type="checkbox"  class="checkbox-template">
                                    </th>
                                    <th>Event</th>
                                    <th>Venue</th>
                                    <th>Event Date</th>
                                    <th>Source</th>
                                    </thead>
                                    <tbody class="tablesearchevent">

                                    </tbody>
                                </table>

                            </div>
                            <div class="pull-right">
                                <a href="javascript:void(0);" class="btn btn-primary addbulkeventbtn" >Add Selected Event</a>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div> 
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
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

 $('#checkevent_all').on('click', function(){
      // Check/uncheck all checkboxes in the table
      var rows  = seattbl.rows({ page: 'current' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });
function mergeEvent() {
    var vividid = $(".checkbox_vividseats:checked").val()
    var shid = $(".checkbox_stubhub:checked").val()
    var totalsel = 0;
    if (vividid > 0) {
        totalsel++;
    }
    if (shid > 0) {
        totalsel++;
    }
    if (totalsel == 2) {
        $.ajax({
            url: APP_URL + "/mergeCheckedEvent",
            type: 'POST',
            dataType: 'JSON',
            data: {
                vividid: vividid,
                shid: shid
            },
            beforeSend: function(xhr) {
                $('.globalLoading').show();
            },
            success: function(data, textStatus, jqXHR) {
                if (data.status) {
                    location.reload();
                } else {
                    $('.globalLoading').hide();
                }
            },
            error: function(data, jqXHR, textStatus, errorThrown) {}
        })
    } else {
        Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'Please Select two event',
            confirmButtonColor: '#796AEE',
        })
    }
}

function manual_check(id, self_id) {
    $.ajax({
        url: APP_URL + "/manualTrackEvent",
        type: 'POST',
        dataType: 'JSON',
        data: {
            'eventid': id,
            'self_id': self_id
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
                    text: 'API Request ERROR',
                    confirmButtonColor: '#796AEE',
                })
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {

        }
    })
}

function jsCode(url, height, width, title) {
    $('#jsIframe').attr("src", url);
    if (width !== "") {
        $('#modal-dailog').css('width', width);
        $('#modal-dailog').css('max-width', '100%');
    } else {
        $('#modal-dailog').css('max-width', '100%');
    }
    $('#jsCodeModal').modal();
    $('#jsTitle').html(title);
    $('#jsIframe').css('height', (height - 20) + 'px');
}

function untrackevent(obj) {
    var event_id = $(obj).data('id');
    var self_id = $(obj).attr('data-val');
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to UNTRACK this event!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#796AEE',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes, untrack it!',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    url: APP_URL + "/untrack-event",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'event_id': event_id,
                        self_id: self_id
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
                                text: data.data,
                                confirmButtonColor: '#796AEE',
                            })
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: textStatus,
                            confirmButtonColor: '#796AEE',
                        })
                    }
                });
            });
        },
        allowOutsideClick: false
    })
}
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
        columns: [{
                data: 'track',
                name: 'track',
            },
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
            {
                data: 'action',
                name: 'action',
                orderable: false
            },
            {
                data: 'merge',
                name: 'merge',
                orderable: false
            },
            {
                data: 'hidecol',
                name: 'hidecol',
                visible: false
            },
        ],
        "order": [
            [0, 'desc']
        ]
    });
    $("div.hidepastevent").html('<input type="checkbox" class="checkbox-template" id="hidepastevent"> <label for="hidepastevent">Hide Past Events </label>');
    if (isHide == "1") {
        $('#hidepastevent').prop('checked', true);
    } else {
        $('#hidepastevent').prop('checked', false);
    }
})
$(document).on('click', '.checkbox_vividseats:checkbox', function() {
    $('.checkbox_vividseats').not(this).prop('checked', false);
});
$(document).on('click', '#hidepastevent:checkbox', function() {
    if ($('#hidepastevent').is(":checked")) {
        localStorage.setItem("isPastHideEvent", 1);
        listevent
            .columns(11)
            .search('HIDE_PAST_DATA')
            .draw();
    } else {
        localStorage.setItem("isPastHideEvent", 0);
        listevent
            .columns(11)
            .search('')
            .draw();
    }

});
$(document).on('click', '.checkbox_stubhub:checkbox', function() {
    $('.checkbox_stubhub').not(this).prop('checked', false);
});

function trackEvent() {
    var selected = [];
    $('.event-checkbox').each(function(i, v) {
        if ($(this).is(':checked')) {
            selected.push({
                id: $(this).val(),
                dataid: $(this).attr('data-val')
            });
        }
    })
    if (selected.length > 0) {
        $.ajax({
            url: APP_URL + "/trackCheckedEvent",
            type: 'POST',
            dataType: 'JSON',
            data: {
                'eventtrack': selected
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

                Swal.fire({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Your Tracking Limit Over. Please Contact Admin',
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
}
$("form[name='addEvent']").submit(function(e) {
    e.preventDefault();
    $('#event-error').html('');
    $.ajax({
        url: APP_URL + "/addEvent",
        type: 'POST',
        dataType: 'JSON',
        data: $("form[name='addEvent']").serialize(),
        beforeSend: function() {
            $('.globalLoading').show();
        },
        success: function(data) {
            if (data.status) {
                location.reload();
            } else {
                $('.globalLoading').hide();
                $('#event-error').html(data.msg);
            }
        }
    })
})
</script>
@stop