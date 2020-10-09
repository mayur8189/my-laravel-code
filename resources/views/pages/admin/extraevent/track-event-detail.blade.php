<!DOCTYPE html>
<html>
    <head>
        @include('includes.head')
        <link rel="stylesheet" type="text/css" href="https://www.highcharts.com/media/com_demo/css/highslide.css" />
        @yield('styles')
    </head>
    <body> 
        <?php
        $series = array();
        $type = "";
        if ($data['eventstrack']['source'] == "stubhub") {
            $type = "SH";
        } else if ($data['eventstrack']['source'] == "vividseats") {
            $type = "VS";
        }
        $final_stat = array();
        $last_data = array();
        $series[0] = " {
                showInLegend: true, 
                name: '" . $type . " -  Total Tickets',
                data: [ ";
//        $series[1] = " {
//                showInLegend: true, 
//                name:  '" . $type . " -  Total Listings',
//                data: [ ";
        foreach ($data['eventstrack']['data'] as $key => $event) {
            $evndata = json_decode($event->ticketinfo, true);
            if (!empty($evndata)) {
                $y = date("Y", $event['created_date']);
                $m = date("m", $event['created_date']);
                $m = $m - 1;
                $d = date("d", $event['created_date']);
                $h = date("H", $event['created_date']);
                $s = 00;
                $i = date("i", $event['created_date']);

                if ($evndata['totalTickets'] != '' && $evndata['totalTickets'] !== '-') {
                    $series[0] .= ' [Date.UTC(' . $y . ',' . $m . ',' . $d . ',' . $h . ',' . $i . ',' . $s . '), ' . $evndata['totalTickets'] . '   ],';
                    $final_stat[$type][$event['created_date']] = $evndata['totalTickets'];
                    $last_data[$type]['total'] = $evndata['totalTickets'];
                    $last_data[$type]['date'] = $event['created_date'];
                }

//                if ($evndata['totalListings'] != '' && $evndata['totalListings'] !== '-')
//                    $series[1] .= ' [Date.UTC(' . $y . ',' . $m . ',' . $d . ',' . $h . ',' . $i . ',' . $s . '), ' . $evndata['totalListings'] . '   ],';
            }
        }
        $series[0] .= '  ] },';
//        $series[1] .= '  ] },';
        if (isset($data['eventstrack2']['data']) && count($data['eventstrack2']['data']) > 0) {
            $type = "";
            if ($data['eventstrack2']['source'] == "stubhub") {
                $type = "SH";
            } else if ($data['eventstrack2']['source'] == "vividseats") {
                $type = "VS";
            }
            $series[1] = " {
                showInLegend: true, 
                name: '" . $type . " -  Total Tickets',
                data: [ ";
//            $series[3] = " {
//                showInLegend: true, 
//                name:  '" . $type . " -  Total Listings',
//                data: [ ";
            foreach ($data['eventstrack2']['data'] as $key => $event) {
                $evndata = json_decode($event->ticketinfo, true);
                if (!empty($evndata)) {
                    $y = date("Y", $event['created_date']);
                    $m = date("m", $event['created_date']);
                    $m = $m - 1;
                    $d = date("d", $event['created_date']);
                    $h = date("H", $event['created_date']);
                    $s = 00;
                    $i = date("i", $event['created_date']);

                    if ($evndata['totalTickets'] != '' && $evndata['totalTickets'] !== '-') {
                        $series[1] .= ' [Date.UTC(' . $y . ',' . $m . ',' . $d . ',' . $h . ',' . $i . ',' . $s . '), ' . $evndata['totalTickets'] . '   ],';
                        $final_stat[$type][$event['created_date']] = $evndata['totalTickets'];
                        $last_data[$type]['total'] = $evndata['totalTickets'];
                        $last_data[$type]['date'] = $event['created_date'];
                    }

//                    if ($evndata['totalListings'] != '' && $evndata['totalListings'] !== '-')
//                        $series[3] .= ' [Date.UTC(' . $y . ',' . $m . ',' . $d . ',' . $h . ',' . $i . ',' . $s . '), ' . $evndata['totalListings'] . '   ],';
                }
            }
            $series[1] .= '  ] },';
//            $series[3] .= '  ] },';
        }
        $before24_arry = array();
        if (isset($last_data['SH']) && !empty($last_data['SH']['total'])) {
            $before24 = $last_data['SH']['date'] - 1 * 24 * 3600;
            if (isset($final_stat['SH'])) {
                foreach ($final_stat['SH'] as $key => $val) {

                    if ($before24 < $key && $key !== $last_data['SH']['date']) {
                        $before24_arry['SH'][$key] = $val;
                        $last_data['SH']['last'] = $val;
                        break;
                    }
                }
            }
        }
        if (isset($last_data['VS']) && !empty($last_data['VS']['total'])) {
            $before24 = $last_data['VS']['date'] - 1 * 24 * 3600;
            if (isset($final_stat['VS'])) {
                foreach ($final_stat['VS'] as $key => $val) {

                    if ($before24 < $key && $key !== $last_data['VS']['date']) {
                        $before24_arry['VS'][$key] = $val;
                        $last_data['VS']['last'] = $val;
                        break;
                    }
                }
            }
        }
        ?>
        <div class="page">

            <div class="row">
                <div class="chart col-lg-12 m-0"> 
                    <div class="bar-chart bg-white mb-0">  
                        <table class="table mb-0" style="border-top:2px solid white">
                            <tr>
                                <td class="p-0 ">
                                    <div><span><i class="fa fa-map-marker"></i> <?= $data['eventdata']->venue ?></span></div>
                                    <div><span><i class="fa fa-clock-o"></i> <?= date('M j, Y - g:i A', $data['eventdata']->eventDate) ?></span></div>
                                </td>
                                <td> <input type="checkbox" class="checkbox-template" id="showtable"> <label for="showtable">Show Only Table </label></td>
                                <?php if ($data['eventstrack']['source'] == "stubhub" || ($data['eventstrack']['source'] == "stubhub" && $data['eventstrack2']['source'] == "vividseats")) { ?>
                                    <td><a class="btn btn-primary btn-sm" href="javascript:void(0);" title="Show Sales Data" onclick="jssales('<?= url('event-track-detail/' . $event['event_id']) ?>');" >Show Sales Data</a>
                                    </td>
<?php } ?>
                            </tr>
                        </table> 
                        <hr class="m-0 mt-1">  
                        <div id="graph-contianer" style="width: 100%;display: block;"></div>    

                    </div> 
                </div>
            </div> 
        </div> 

        <div>
            <table class="table">
                <tr>
<?php
if (isset($last_data['SH']) && !empty($last_data['SH'])) {
    ?>
                        <td>Stubhub</td> 
                        <?php
                        $total = 0;
                        echo '<td>';
                        foreach ($last_data['SH'] as $key => $value) {
                            if ($key == "total") {
                                echo "Current Total : <b>" . $value . '</b>';
                                $total = $value;
                            }
                            if ($key == "last") {
                                $diff = $total - $value;
                                if ($diff > 0) {
                                    echo ' &nbsp 24 HR Change : <span class="badge badge-success">' . $diff . '</span>';
                                }
                                if ($diff < 0) {
                                    echo ' &nbsp 24 HR Change : <span class="badge badge-danger">' . $diff . '</span>';
                                }
                            }
                        }
                        echo '</td>';
                    }
                    ?>
                </tr>
                <tr>
<?php
if (isset($last_data['VS']) && !empty($last_data['VS'])) {
    ?>
                        <td>Vividseats</td> 
                        <?php
                        $total = 0;
                        echo '<td>';
                        foreach ($last_data['VS'] as $key => $value) {
                            if ($key == "total") {
                                echo "Current Total : <b>" . $value . '</b>';
                                $total = $value;
                            }
                            if ($key == "last") {
                                $diff = $total - $value;
                                if ($diff > 0) {
                                    echo ' &nbsp 24 HR Change : <span class="badge badge-success">' . $diff . '</span>';
                                }
                                if ($diff < 0) {
                                    echo ' &nbsp 24 HR Change : <span class="badge badge-danger">' . $diff . '</span>';
                                }
                            }
                        }
                        echo '</td>';
                    }
                    ?>

                </tr>



            </table>
        </div>
        <!-- The Modal -->
        <div class="modal fade " id="Sales-data" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" id="modal-dailog" role="document" style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Sales List</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-striped" id="tablesaledata">
                                <thead>
                                <th>Section</th>
                                <th>Row</th>
                                <th>Quantity</th>
                                <th>Seats</th>
                                <th>Price</th>
                                <th>Date/Time</th>
                                <th>Delivery Method</th>
                                </thead>
                                <tbody class="tablesaledata">

                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        @include('includes.foot') 
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> 
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script> 
        <script>
           
                                    function jssales() {
                                         var seattbl = '';
                                        $('#Sales-data').modal();
                                        var event_id = <?php echo $data['eventstrack']['data'][0]['type_eventid'] ?>;
                                        $.ajax({
                                            url: APP_URL + "/sales-list",
                                            type: 'POST',
                                            dataType: 'JSON',
                                            data: {eventid: event_id},
                                            beforeSend: function (xhr) {
                                                $('.globalLoading').show();
                                            },
                                            success: function (data, textStatus, jqXHR) {
                                                if (data.status) {
                                                     $('#tablesaledata').DataTable().destroy();
                                                    $('.tablesaledata').html(data.res_data);
                                                    
                                                    seattbl = $('#tablesaledata').DataTable({
                                                        
                                                        "lengthMenu": [[50,100, 250, 500, 1000, -1], [50,100, 250, 500, 1000, "All"]],
                                                        "language": {
                                                            "lengthMenu": "Show _MENU_ "
                                                        },
                                                        "destroy": true,
                                                        "order": [[5, "desc"]],
                                                        columns: [{
                                                                data: 'section',
                                                                name: 'section',
                                                            },
                                                            {
                                                                data: 'row',
                                                                name: 'row'
                                                            },
                                                            {
                                                                data: 'quantity',
                                                                name: 'quantity',
                                                            },
                                                            {
                                                                data: 'seats',
                                                                name: 'seats'
                                                            },
                                                            {
                                                                data: 'price',
                                                                name: 'price'
                                                            },
                                                            {
                                                                data: 'dateTime',
                                                                name: 'dateTime',
                                                                width: "25%"

                                                            },
                                                            {
                                                                data: 'deliveryMethod',
                                                                name: 'deliveryMethod'
                                                            },
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
                                            error: function (jqXHR, textStatus, errorThrown) {
                                                $('.globalLoading').hide();
                                                Swal.fire({
                                                    type: 'error',
                                                    title: 'Oops...',
                                                    text: 'API Request ERROR',
                                                    confirmButtonColor: '#796AEE',
                                                })
                                            }
                                        })
                                    }
        </script>
        <script>
            $(document).on('click', '#showtable:checkbox', function () {
                if ($('#showtable').is(":checked")) {
                    $('.highcharts-data-table').find('table').addClass("table mb-0");
                    $('.highcharts-table-caption').remove();
                    $('.highcharts-data-table').show();
                    $('#graph-contianer').hide();
                } else {
                    $('.highcharts-data-table').hide();
                    $('#graph-contianer').show();
                }
            });
            $(document).ready(function () {
                Highcharts.chart('graph-contianer', {
                    chart: {
                        marginTop: 40,
                        type: 'spline',
                        zoomType: 'x'
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: 'Click and drag in the plot area to zoom in',
                        x: -20
                    },
                    exporting: {
                        showTable: true,
                        csv: {
                            dateFormat: '%b %e, %Y - %H:%M %p'
                        }
                    },
                    xAxis: {
                        type: 'datetime',
                        dateTimeLabelFormats: {
                            minute: '%l:%M ',
                            hour: '%l:%M %p',
                            month: '%e. %b',
                            year: '%b',
                        },
                        labels: {
                            style: {
                                fontSize: '14px'
                            }
                        },
                        title: {
                            text: 'Date Time',
                            style: {
                                fontSize: '14px'
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Value',
                            style: {
                                fontSize: '14px'
                            }
                        },
                        labels: {
                            style: {
                                fontSize: '14px'
                            }
                        },
                        min: 0
                    },
                    credits: false,
                    tooltip: {
                        formatter: function (tooltip) {
                            const header = '<span >' + Highcharts.dateFormat(' %b %e %Y - %H:%M',
                                    new Date(this.x)) + '</span><br/>'

                            return header + (tooltip.bodyFormatter(this.points).join(''))
                        },
                        shared: true,
                        crosshairs: true
                    },
                    plotOptions: {
                        series: {
                            marker: {
                                enabled: true
                            }
                        }
                    },
                    colors: ['#796AEE', '#ff3f3f'],
                    series: [
<?php
for ($i = 0; $i < count($series); $i++)
    echo $series[$i];
?>

                    ]

                });
                $('.highcharts-data-table').hide();
                $('.highcharts-table-caption').remove();
                $('.highcharts-data-table').find('table').addClass("table mb-0");
            });
        </script>

    </body>
</html>