<?php
if (isset($data['events']) && count($data['events']) > 0) {
    ?>
    @foreach($data['events'] as $event)
    <div class="project">
        <div class="row bg-white has-shadow">
            <div class="left-col col-lg-8 d-flex align-items-center justify-content-between"> 
                <div class="project-title d-flex align-items-center"> 
                    <?php
                    if ($event['track'] == '1') {
                        ?>
                    <div class=" mr-2"> 
                        <a href="javascript:void(0)" onclick="untrackevent(this)" data-id="{{$event['event_id']}}" class="btn btn-primary btn-sm" title="Untrack this event"><i class="fa fa-history"></i></a>
                    </div>
                        <?php
                    } else {
                        ?>
                        <div class="i-checks mr-2">
                            <input id="checkevent_{{$event['event_id']}}" type="checkbox" value="{{$event['event_id']}}"  class="checkbox-template event-checkbox" >
                            <label for="checkevent_{{$event['event_id']}}"></label>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="text">
                        <h3 class="h4">{{$event['name']}}</h3>
                        <small> 
                            <?php
                            $loc = '';
                            if (isset($event['venue']) && !empty($event['venue'])) {
                                $loc = $event['venue'];
                            } else {
                                $loc = 'N/A';
                            }
                            echo $loc;
                            ?>
                        </small>
                    </div>
                </div>
                <?php
                if (isset($event['eventDateLocal'])) {
                    ?>
                    <div class="project-date"><span class="hidden-sm-down"><?= date('d M D . h:i A', strtotime($event['eventDateLocal'])) ?></span></div>
                    <?php
                }
                ?>
            </div>
            <div class="right-col col-lg-4 d-flex align-items-center">

                <div class="comments"><i class="fa fa-dollar"></i>
                    <?php
                    $price = '';
                    $ticket = '';
                    if (isset($event['ticketInfo'])) {
                        $ticketInfo = json_decode($event['ticketInfo'], true);
                        if (isset($ticketInfo['minListPrice']) && isset($ticketInfo['maxListPrice'])) {
                            $price = '' . $ticketInfo['minListPrice'] . ' - ' . $ticketInfo['maxListPrice'] . ' ' . $event['currencyCode'];
                        }
                        if (isset($ticketInfo['totalTickets']) && isset($ticketInfo['totalListings'])) {
                            $ticket = ' Total Ticket - ' . $ticketInfo['totalTickets'] . '<br> &nbsp &nbsp &nbsp &nbspTotal Listing - ' . $ticketInfo['totalListings'];
                        }
                    } else {
                        $price = 'N/A';
                        $ticket = 'N/A';
                    }
                    echo $price;
                    ?>
                </div> 
                <div class="comments">
                    <i class="fa fa-ticket"></i>
                    <?= $ticket ?>
                </div>
                <div class="comments mx-auto">
                    <a href="https://www.stubhub.com/<?= $event['url'] ?>" target="_blank" class="btn btn-primary">View Event</a>
                </div>
            </div>
        </div>
    </div> 
    @endforeach
<?php } else {
    ?>
    <p class="error">No Event Found in your search term</p>
    <?php
}?>