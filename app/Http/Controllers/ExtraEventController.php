<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\ExtraEventModel;
use App\Model\UserEventModel;
use App\Model\TrackEventData;
use App\Model\UserModel;
use App\Model\TotalTrackPermissionModel;
use App\Model\TotalSearchArtistPermissionModel;
use App\Model\StumhubSellModel;
use Validator;
use Redirect;

class ExtraEventController extends CommonController {

    public function __construct(Request $request) {
        $this->ex_event = new ExtraEventModel();
    }

    public function EventList(Request $request) {
        if (Auth::User()->can('view-event')) {
            $User_id=0;
            $final_display = array();
            $events = $this->ex_event->listEvent($request->all());

            foreach ($events as $key => $event) {
                $is_find = array_key_exists($event['self_id'], $final_display);
                if ($is_find == false) {
                    $final_display[$event['type_eventid']]['event_id'] = $event['event_id'];
                    $final_display[$event['type_eventid']]['type_eventid'] = $event['type_eventid'];
                    $final_display[$event['type_eventid']]['name'] = $event['name'];
                    $final_display[$event['type_eventid']]['eventDateLocal'] = $event['eventDateLocal'];
                    $final_display[$event['type_eventid']]['eventDateUTC'] = $event['eventDateUTC'];
                    $final_display[$event['type_eventid']]['createdDate'] = $event['createdDate'];
                    $final_display[$event['type_eventid']]['city'] = $event['city'];
                    $final_display[$event['type_eventid']]['venue'] = $event['venue'];
                    $final_display[$event['type_eventid']]['min_price'] = $event['min_price'];
                    $final_display[$event['type_eventid']]['max_price'] = $event['max_price'];
                    $final_display[$event['type_eventid']]['currencyCode'] = $event['currencyCode'];
                    $final_display[$event['type_eventid']]['ticketInfo'] = $event['ticketInfo'];
                    $final_display[$event['type_eventid']]['url'] = $event['url'].'?showGCP=0';
                    $final_display[$event['type_eventid']]['source'] = $event['source'];
                    $final_display[$event['type_eventid']]['created_date'] = $event['created_date'];
                    $final_display[$event['type_eventid']]['status'] = $event['status'];
                    $final_display[$event['type_eventid']]['eventDate'] = $event['eventDate'];
                    $final_display[$event['type_eventid']]['ue_id'] = $event['ue_id'];
                    $final_display[$event['type_eventid']]['self_id'] = $event['self_id'];
                    $final_display[$event['type_eventid']]['user_id'] = $event['user_id'];
                    $final_display[$event['type_eventid']]['is_track'] = $event['is_track'];
                    $final_display[$event['type_eventid']]['ancestors'] = $event['ancestors'];
                    $final_display[$event['type_eventid']]['performers'] = $event['performers'];
                }
            }
            $events = $final_display;
            $eventsame = $this->ex_event->listEventSame($request->all());

            $data['events'] = $events;
            $data['eventsame'] = $eventsame;
            return view('pages.admin.extraevent.list-events', compact('data'));
        } else {
            abort(401);
        }
    }
      public function UserEventList($userid = false) {
        if (Auth::User()->can('view-event')) {
            $final_display = array();
            //print_r($request->all());exit;
            $events = $this->ex_event->UserlistEvent($userid);
            foreach ($events as $key => $event) {
                $is_find = array_key_exists($event['self_id'], $final_display);
                if ($is_find == false) {
                    $final_display[$event['type_eventid']]['event_id'] = $event['event_id'];
                    $final_display[$event['type_eventid']]['type_eventid'] = $event['type_eventid'];
                    $final_display[$event['type_eventid']]['name'] = $event['name'];
                    $final_display[$event['type_eventid']]['eventDateLocal'] = $event['eventDateLocal'];
                    $final_display[$event['type_eventid']]['eventDateUTC'] = $event['eventDateUTC'];
                    $final_display[$event['type_eventid']]['createdDate'] = $event['createdDate'];
                    $final_display[$event['type_eventid']]['city'] = $event['city'];
                    $final_display[$event['type_eventid']]['venue'] = $event['venue'];
                    $final_display[$event['type_eventid']]['min_price'] = $event['min_price'];
                    $final_display[$event['type_eventid']]['max_price'] = $event['max_price'];
                    $final_display[$event['type_eventid']]['currencyCode'] = $event['currencyCode'];
                    $final_display[$event['type_eventid']]['ticketInfo'] = $event['ticketInfo'];
                    $final_display[$event['type_eventid']]['url'] = $event['url'];
                    $final_display[$event['type_eventid']]['source'] = $event['source'];
                    $final_display[$event['type_eventid']]['created_date'] = $event['created_date'];
                    $final_display[$event['type_eventid']]['status'] = $event['status'];
                    $final_display[$event['type_eventid']]['eventDate'] = $event['eventDate'];
                    $final_display[$event['type_eventid']]['ue_id'] = $event['ue_id'];
                    $final_display[$event['type_eventid']]['self_id'] = $event['self_id'];
                    $final_display[$event['type_eventid']]['user_id'] = $event['user_id'];
                    $final_display[$event['type_eventid']]['is_track'] = $event['is_track'];
                    $final_display[$event['type_eventid']]['ancestors'] = $event['ancestors'];
                    $final_display[$event['type_eventid']]['performers'] = $event['performers'];
                }
            }
            $events = $final_display;
            $eventsame = $this->ex_event->listEventSame();
            //print_r($events);exit;
            $data['events'] = $events;
            $data['eventsame'] = $eventsame;
            return view('pages.admin.extraevent.user-list-event', compact('data'));
        } else {
            abort(401);
        }
    }

    public function trackCheckedEvent(Request $request) {
        if (Auth::User()->can('total-events')) {
            $userid = Auth::User()->user_id;
            if (Auth::User()->hasRole('admin')) {
                $eventcounter = 1;
                $setcounter = 2;
                $setUsercounter=2;
            } else {
                $setpermission = TotalTrackPermissionModel::CountTotalTrack($userid);
                $setperusermission = TotalTrackPermissionModel::CountUserTotalTrack($userid);
                $setUsercounter=$setperusermission[0];
                $eventcounter = count($request->eventtrack);
                $setcounter = $setpermission[0];
            }

            if ($setcounter >= $setUsercounter && $setUsercounter>=$eventcounter ) {
                $v_res = array();
                $rules = array(
                    'eventtrack' => 'required',
                );

                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    $v_res['status'] = false;
                } else {
                    $userid = Auth::User()->user_id;
                    $param = array(
                        'is_track' => 1
                    );
                    foreach ($request->eventtrack as $event) {
                        UserEventModel::updateEventById($event['id'], $userid, $param);
                        $response = ExtraEventModel::getEventBytpyeid($event['dataid']);
                        if (isset($response['event_id']) && !empty($response['event_id'])) {
                            UserEventModel::updateEventById($response['event_id'], $userid, $param);
                        }
                    }
                    $v_res['status'] = true;
                    if (!Auth::User()->hasRole('admin')) {
                        $pendingevent=$setUsercounter-$eventcounter;
                        $updatecounter = TotalTrackPermissionModel::UpdateCountTotalTrack($userid, $eventcounter);
                    }
                }

                echo json_encode($v_res, true);
                exit;
            } else {
                return Redirect::back()->withErrors(['You dont have a permission for this role. Please contact admin', "msg"]);
            }
        } else {
            abort(401);
        }
    }

    public function getStubhubdataByID($eventid) {
        $SHendpoints = "/sellers/search/events/v3?id=" . $eventid;
        $SHURL = config('config.SH_URL');
        $apiurl = $SHURL . $SHendpoints;
        $data = $this->curl_post($apiurl, 'SH', true);
        $data = json_decode($data, true);
        return $data;
    }

    public function getVividdataByID($eventid) { 
        $apiurl = 'https://www.vividseats.com/rest/v2/web/listings/' . $eventid; 
        $data = $this->curl_post($apiurl, 'VS', false);
        $data = json_decode($data, true);
        return $data;
    }

    public function untrackEvent(Request $request) {
        $v_res = array();
        $userid = Auth::User()->user_id;
        $v_res['status'] = false;
        $event_id = $request->event_id;
        $self_id = $request->self_id;
        $event = UserEventModel::getUserEventByIdCount($userid, $event_id);
        if (!empty($event)) {
            $param = array(
                "is_track" => 0
            );
            $event = UserEventModel::updateEventById($event_id, $userid, $param);
            $response = ExtraEventModel::getEventBytpyeid($self_id);
            if (isset($response['event_id']) && !empty($response['event_id'])) {
                UserEventModel::updateEventById($response['event_id'], $userid, $param);
            }
            if ($event) {
                $v_res['status'] = true;
            }
        }
        echo json_encode($v_res, true);
        exit;
    }

    public function stubhubtrackbyid($eventid, $dataevent) {
        $response = $this->getStubhubdataByID($eventid);
        if (!isset($response['code']) && isset($response['numFound']) && $response['numFound'] > 0) {
            foreach ($response['events'] as $event) {
                $param = array(
                    "ticketInfo" => (isset($event['ticketInfo']) ? json_encode($event['ticketInfo'], true) : ''),
                    "last_checked" => time()
                );
                ExtraEventModel::updateEventById($dataevent, $param);
                $param = array(
                    "event_id" => $dataevent,
                    "type_eventid" => $eventid,
                    "created_date" => time(),
                    "ticketInfo" => (isset($event['ticketInfo']) ? json_encode($event['ticketInfo'], true) : ''),
                );
                TrackEventData::trackData($param);
            }
            return true;
        }
    }

    public function vividtrackbyid($eventid, $dataevent) {
        $response = $this->getVividdataByID($eventid);

        if (isset($response['global']) && count($response['global']) == 1) {
            foreach ($response['global'] as $event) {
                $tickettotal = 0;
                foreach ($response['tickets'] as $ticket) {
                    $tickettotal = $tickettotal + $ticket['q'];
                }
                $event['totalticket'] = $tickettotal;

                $max_price = 0;
                $min_price = 0;
                if (isset($event['lp']) && !empty($event['lp'])) {
                    $min_price = $event['lp'];
                }
                if (isset($event['hp']) && !empty($event['hp'])) {
                    $max_price = $event['hp'];
                }
                $ticketinfo = array(
                    "minListPrice" => $min_price,
                    "maxListPrice" => $max_price,
                    "totalTickets" => $event['totalticket'],
                    "totalListings" => $event['listingCount'],
                );
                $param = array(
                    "ticketInfo" => json_encode($ticketinfo, true),
                    "last_checked" => time()
                );
                ExtraEventModel::updateEventById($dataevent, $param);
                $param = array(
                    "event_id" => $dataevent,
                    "type_eventid" => $eventid,
                    "created_date" => time(),
                    "ticketInfo" => json_encode($ticketinfo, true),
                );
                TrackEventData::trackData($param);
            }
            return true;
        }
    }

    public function manualTrackEvent(Request $request) {
        if (Auth::User()->can('total-events')) {
            $v_res = array();
            $v_res['status'] = false;
            $event_id = $request->eventid;
            $self_id = $request->self_id;
            $data = ExtraEventModel::getEventByid($event_id);
            if (!empty($data['is_track']) && $data['is_track'] == "1") {
                if ($data['source'] == "stubhub") {
                    $eventid = $data['type_eventid'];
                    $response = $this->stubhubtrackbyid($eventid, $data['event_id']);
                    $v_res['status'] = $response;
                    $response = ExtraEventModel::getEventBytpyeid($self_id);

                    if (isset($response['type_eventid']) && !empty($response['type_eventid'])) {
                        $response = $this->vividtrackbyid($response['type_eventid'], $response['event_id']);
                        $v_res['status'] = $response;
                    }
                } else if ($data['source'] == "vividseats") {
                    $eventid = $data['type_eventid'];
                    $response = $this->vividtrackbyid($eventid, $data['event_id']);
                    $v_res['status'] = $response;
                }
            }
            echo json_encode($v_res, true);
            exit;
        }
    }

    public function addEvent(Request $request) {
        if (Auth::User()->can('add-event')) {
            $v_res = array();
            $userid = Auth::User()->user_id;
            $rules = array(
                'event' => 'required|string',
            );
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->messages();
                $error_data = "";
                if ($errors->any()) {
                    $error_data = "<ul>";
                    foreach ($errors->all() as $error) {
                        $error_data .= "<li>" . $error . "</li>";
                    }
                    $error_data .= "</ul>";
                }
                $v_res['msg'] = $error_data;
                $v_res['status'] = false;
            } else {
                $v_res['msg'] = 'Event not found';
                $v_res['status'] = false;
                if (strpos($request->event, "stubhub.com") > -1) {
                    $urlExplode = explode("/", $request->event);
                    $eventid = 0;
                    if (isset($urlExplode[5]) && !empty($urlExplode[5])) {
                        $eventid = $urlExplode[5];
                        $response = $this->getStubhubdataByID($eventid);

                        if (!isset($response['code']) && isset($response['numFound']) && $response['numFound'] > 0) {
                            foreach ($response['events'] as $event) {
                                $isExistcnt = ExtraEventModel::getEventByTypeidCount($event['id'], "SH");
                                if ($isExistcnt == 0) {
                                    $res = $this->insertStubhub($event);
                                    $v_res['status'] = true;
                                    echo json_encode($v_res, true);
                                    exit;
                                } else {
                                    $evntddata = ExtraEventModel::getEventByTypeid($event['id'], "SH");
                                    $isExistuE = UserEventModel::getUserEventByIdCount($userid, $evntddata['event_id']);
                                    if ($isExistuE == 0) {
                                        $param = array(
                                            "user_id" => $userid,
                                            "event_id" => $evntddata['event_id'],
                                            "is_track" => 1,
                                            "created_date" => time()
                                        );
                                        UserEventModel::saveUserEvent($param);
                                        $v_res['status'] = true;
                                        echo json_encode($v_res, true);
                                        exit;
                                    } else {
                                        $v_res['msg'] = 'Already exists';
                                        $v_res['status'] = false;
                                    }
                                    echo json_encode($v_res, true);
                                    exit;
                                }
                            }
                        } else {
                          
                            $v_res['msg'] = 'Event not found';
                            $v_res['status'] = false;
                        }
                    } else if(isset($urlExplode[4]) && !empty($urlExplode[4])){
                            
                              $eventid = $urlExplode[4];
                        $response = $this->getStubhubdataByID($eventid);
                        
                        

                        if (!isset($response['code']) && isset($response['numFound']) && $response['numFound'] > 0) {
                            foreach ($response['events'] as $event) {
                                $isExistcnt = ExtraEventModel::getEventByTypeidCount($event['id'], "SH");
                                if ($isExistcnt == 0) {
                                    $res = $this->insertStubhub($event);
                                    $v_res['status'] = true;
                                    echo json_encode($v_res, true);
                                    exit;
                    } else {
                                    $evntddata = ExtraEventModel::getEventByTypeid($event['id'], "SH");
                                    $isExistuE = UserEventModel::getUserEventByIdCount($userid, $evntddata['event_id']);
                                    if ($isExistuE == 0) {
                                        $param = array(
                                            "user_id" => $userid,
                                            "event_id" => $evntddata['event_id'],
                                            "is_track" => 1,
                                            "created_date" => time()
                                        );
                                        UserEventModel::saveUserEvent($param);
                                        $v_res['status'] = true;
                                        echo json_encode($v_res, true);
                                        exit;
                                    } else {
                                        $v_res['msg'] = 'Already exists';
                                        $v_res['status'] = false;
                                    }
                                    echo json_encode($v_res, true);
                                    exit;
                                }
                            }
                        } else {
                          
                        $v_res['msg'] = 'Event not found';
                        $v_res['status'] = false;
                    }
                    }
                    else{
                        $v_res['msg'] = 'Event not found';
                        $v_res['status'] = false;
                    }
                } else if (strpos($request->event, "https://www.vividseats.com") > -1) {
                    $parts = parse_url($request->event);
                    if (!isset($parts['query'])) {
                        $v_res['msg'] = 'Event not found';
                        $v_res['status'] = false;
                        echo json_encode($v_res, true);
                        exit;
                    }
                    parse_str($parts['query'], $query);

                    $eventid = 0;

                    if (isset($query['productionId']) && !empty($query['productionId'])) {
                        $eventid = $query['productionId'];
                        $response = $this->getVividdataByID($eventid);

                        if (isset($response['global']) && count($response['global']) == 1) {
                            foreach ($response['global'] as $event) {
                                $tickettotal = 0;
                                foreach ($response['tickets'] as $ticket) {
                                    $tickettotal = $tickettotal + $ticket['q'];
                                }
                                $event['totalticket'] = $tickettotal;
                                $event['weburl'] = $request->event;

                                $isExistcnt = ExtraEventModel::getEventByTypeidCount($event['productionId'], 'VS');
                                if ($isExistcnt == 0) {
                                    $res = $this->insertVivid($event);
                                    $v_res['status'] = true;
                                    echo json_encode($v_res, true);
                                    exit;
                                } else {
                                    $evntddata = ExtraEventModel::getEventByTypeid($event['productionId'], "VS");
                                    $isExistuE = UserEventModel::getUserEventByIdCount($userid, $evntddata['event_id']);
                                    if ($isExistuE == 0) {
                                        $param = array(
                                            "user_id" => $userid,
                                            "event_id" => $evntddata['event_id'],
                                            "is_track" => 1,
                                            "created_date" => time()
                                        );
                                        UserEventModel::saveUserEvent($param);
                                        $v_res['status'] = true;
                                        echo json_encode($v_res, true);
                                        exit;
                                    } else {
                                        $v_res['msg'] = 'Already exists';
                                        $v_res['status'] = false;
                                    }
                                    echo json_encode($v_res, true);
                                    exit;
                                }
                            }
                        } else {
                            $v_res['msg'] = 'Event not found';
                            $v_res['status'] = false;
                        }
                    } else {
                        $v_res['msg'] = 'Event not found';
                        $v_res['status'] = false;
                    }
                }

                echo json_encode($v_res, true);
                exit;
            }
        } else {
            abort(401);
        }
    }

    public function insertStubhub($event) {
        $userid = Auth::User()->user_id;
        $city = (isset($event['venue']['city']) && !empty($event['venue']['city']) ? $event['venue']['city'] : '');
        $state = (isset($event['venue']['state']) && !empty($event['venue']['state']) ? $event['venue']['state'] : '');
        $vname = (isset($event['venue']['name']) && !empty($event['venue']['name']) ? $event['venue']['name'] : '');
        $max_price = 0;
        $min_price = 0;
        if (isset($event['ticketInfo']) && !empty($event['ticketInfo'])) {
            if (isset($event['ticketInfo']['maxListPrice'])) {
                $max_price = $event['ticketInfo']['maxListPrice'];
            }
            if (isset($event['ticketInfo']['minListPrice'])) {
                $min_price = $event['ticketInfo']['minListPrice'];
            }
        }
        $eventDateLocal = "";
        $eventDateUTC = "";
        $eventDate = "";
        $createdDate = "";
        $venue_array = "";
        if (isset($event['eventDateLocal']) && !empty($event['eventDateLocal'])) {
            $eventDateLocal = $event['eventDateLocal'];
        }
        if (isset($event['eventDateUTC']) && !empty($event['eventDateUTC'])) {
            $eventDateUTC = $event['eventDateUTC'];
            $eventDate = strtotime($event['eventDateUTC']);
        }
        if (isset($event['createdDate']) && !empty($event['createdDate'])) {
            $createdDate = $event['createdDate'];
        }

        if (isset($event['venue']) && !empty($event['venue'])) {
            $venue_array = json_encode($event['venue'], true);
        }

        $isExistcnt = ExtraEventModel::getEventByTypeidCount($event['id'], 'SH');
        $checkstubself = ExtraEventModel::getEventByTypeselfid($event['id'], 'VS');

        $self_id = NULL;
        if (isset($checkstubself['type_eventid']) && !empty($checkstubself['type_eventid'])) {
            $self_id = $checkstubself['type_eventid'];
        }
        if ($isExistcnt == 0) {
            $params = array(
                "type_eventid" => (isset($event['id']) && !empty($event['id']) ? $event['id'] : ''),
                "name" => (isset($event['name']) && !empty($event['name']) ? $event['name'] : ''),
                "description" => (isset($event['description']) && !empty($event['description']) ? $event['description'] : ''),
                "timezone" => (isset($event['timezone']) && !empty($event['timezone']) ? $event['timezone'] : ''),
                "currencyCode" => (isset($event['currencyCode']) && !empty($event['currencyCode']) ? $event['currencyCode'] : ''),
                "ancestors" => (isset($event['ancestors']) && !empty($event['ancestors']) ? json_encode($event['ancestors'], true) : ''),
                "performers" => (isset($event['performers']) && !empty($event['performers']) ? json_encode($event['performers'], true) : ''),
                "eventDateLocal" => $eventDateLocal,
                "eventDateUTC" => $eventDateUTC,
                "createdDate" => $createdDate,
                "city" => $city,
                "venue" => $vname . ', ' . $city . ', ' . $state,
                "venue_json" => $venue_array,
                "min_price" => $min_price,
                "max_price" => $max_price,
                "ticketInfo" => (isset($event['ticketInfo']) ? json_encode($event['ticketInfo'], true) : ''),
                "url" => (isset($event['webURI']) && !empty($event['webURI']) ? $event['webURI'] : ''),
                "source" => 'stubhub',
                "eventDate" => $eventDate,
                "self_id" => $self_id,
                "created_date" => time(),
                "modified_date" => time(),
            );
            $ins_id = ExtraEventModel::saveEvent($params);
            if (!empty($ins_id)) {
                $param = array(
                    "event_id" => $ins_id,
                    "type_eventid" => (isset($event['id']) && !empty($event['id']) ? $event['id'] : ''),
                    "created_date" => time(),
                    "ticketInfo" => (isset($event['ticketInfo']) ? json_encode($event['ticketInfo'], true) : ''),
                );
                TrackEventData::trackData($param);
            }

            $isExistuE = UserEventModel::getUserEventByIdCount($userid, $ins_id);
            if ($isExistuE == 0) {
                $param = array(
                    "user_id" => $userid,
                    "event_id" => $ins_id,
                    "is_track" => 1,
                    "created_date" => time()
                );
                UserEventModel::saveUserEvent($param);
            }
        }
        return true;
    }

    public function insertVivid($event) {
        $userid = Auth::User()->user_id;
        $state = (isset($event['venueState']) && !empty($event['venueState']) ? $event['venueState'] : '');
        $vname = (isset($event['mapTitle']) && !empty($event['mapTitle']) ? $event['mapTitle'] : '');
        $max_price = 0;
        $min_price = 0;
        if (isset($event['lp']) && !empty($event['lp'])) {
            $min_price = $event['lp'];
        }
        if (isset($event['hp']) && !empty($event['hp'])) {
            $max_price = $event['hp'];
        }
        $venue_array = "";
        $ticketinfo = array(
            "minListPrice" => $min_price,
            "maxListPrice" => $max_price,
            "totalTickets" => $event['totalticket'],
            "totalListings" => $event['listingCount'],
        );
        $isExistcnt = ExtraEventModel::getEventByTypeidCount($event['productionId'], 'VS');
        $checkstubself = ExtraEventModel::getEventByTypeselfid($event['productionId'], 'SH');
        $self_id = NULL;
        if (isset($checkstubself['type_eventid']) && !empty($checkstubself['type_eventid'])) {
            $self_id = $checkstubself['type_eventid'];
        }
        $resdata = $this->getStubIdFromVivid($event['productionId']);
        $eventdate = 0;
        $venue_array = array();
        $performer = array();
        if (!empty($resdata)) {
            if (isset($resdata->rows['0']->stubhubEventId) && !empty($resdata->rows['0']->stubhubEventId)) {
                $self_id = $resdata->rows['0']->stubhubEventId;
            }
            if (isset($resdata->rows['0']->date) && !empty($resdata->rows['0']->date)) {
                $eventdate = strtotime($resdata->rows['0']->date);
            }
            if (isset($resdata->rows['0']->venue) && !empty($resdata->rows['0']->venue)) {
                $venue_array = json_encode($resdata->rows['0']->venue, true);
            }
            if (isset($resdata->rows['0']->performer) && !empty($resdata->rows['0']->performer)) {
                $performer = json_encode($resdata->rows['0']->performer, true);
            }

            $isExistcnt1 = ExtraEventModel::getEventByTypeid($self_id, 'SH');

            if (isset($isExistcnt1['type_eventid']) && !empty($isExistcnt1['type_eventid'])) {
                $isEcntid = $isExistcnt1['event_id'];

                $param = array(
                    "self_id" => $event['productionId']
                );
                ExtraEventModel::updateEventById($isEcntid, $param);
            }
        }
        if ($isExistcnt == 0) {
            $params = array(
                "type_eventid" => (isset($event['productionId']) && !empty($event['productionId']) ? $event['productionId'] : ''),
                "name" => (isset($event['productionName']) && !empty($event['productionName']) ? $event['productionName'] : ''),
                "venue" => $vname . ', ' . $state,
                "min_price" => $min_price,
                "max_price" => $max_price,
                "ticketInfo" => json_encode($ticketinfo, true),
                "url" => $event['weburl'],
                "source" => 'vividseats',
                "self_id" => $self_id,
                "eventDate" => $eventdate,
                "venue_json" => $venue_array,
                "performers" => $performer,
                "created_date" => time(),
                "modified_date" => time(),
            );
            $ins_id = ExtraEventModel::saveEvent($params);
            if (!empty($ins_id)) {
                $param = array(
                    "event_id" => $ins_id,
                    "type_eventid" => (isset($event['productionId']) && !empty($event['productionId']) ? $event['productionId'] : ''),
                    "created_date" => time(),
                    "ticketInfo" => json_encode($ticketinfo, true),
                );
                TrackEventData::trackData($param);
            }

            $isExistuE = UserEventModel::getUserEventByIdCount($userid, $ins_id);
            if ($isExistuE == 0) {
                $param = array(
                    "user_id" => $userid,
                    "event_id" => $ins_id,
                    "is_track" => 1,
                    "created_date" => time()
                );
                UserEventModel::saveUserEvent($param);
            }
        } else {
            
        }
        return true;
    }

    public function getStubIdFromVivid($eventid) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://skybox.vividseats.com/services/events?eventId=' . $eventid);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Accept: application/json';
        //        $headers[] = 'X-Application-Token: 2a0209a1-7f3b-47f2-86fa-139dce3b8d6e';
        $headers[] = 'X-Application-Token: 96-e356-46ce-a0d7-03ad88f64750';
        $headers[] = 'X-Api-Token: e4895b-1469-43eb-81b5-4565bfc6a941';
        $headers[] = 'X-Account: 30';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return array();
        }
//        echo '<pre>';
//        print_r(json_decode($result));
//        exit;
        return json_decode($result);
    }

     public function getStubIdVividData($token,$api,$account) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://skybox.vividseats.com/services/inventory?limit=9999999&ticketStatus=AVAILABLE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Accept: application/json';
        //        $headers[] = 'X-Application-Token: 2a0209a1-7f3b-47f2-86fa-139dce3b8d6e';
        $headers[] = 'X-Application-Token: '.$token;
        $headers[] = 'X-Api-Token: '.$api;
        $headers[] = 'X-Account: '.$account;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if($result=="API request rate limit reached, please try again later.")
        {
        return $result;    
        }
        if (curl_errno($ch)) {
            return array();
        }
        return json_decode($result);
    }
    

    public function eventTrackDetail($eventid) {
        $data['eventstrack']['data'] = TrackEventData::gettrackDataById($eventid);
        $data['eventdata'] = ExtraEventModel::getEventByid($eventid);
        $data['eventstrack']['source'] = $data['eventdata']->source;
        $response = ExtraEventModel::getEventBytpyeid($data['eventdata']['type_eventid']);
        if (isset($response['event_id']) && !empty($response['event_id'])) {
            $data['eventstrack2']['data'] = TrackEventData::gettrackDataById($response['event_id']);
            $data['eventstrack2']['source'] = $response['source'];
        }
        return view('pages/admin/extraevent/track-event-detail', compact('data'));
    }

    public function mergeCheckedEvent(Request $request) {
        $v_res = array();
        $v_res['status'] = false;
        $vividid = $request->vividid;
        $shid = $request->shid;
        $newarray = array(
            $vividid => $shid,
            $shid => $vividid
        );
        foreach ($newarray as $key => $val) {
            $iscnt = ExtraEventModel::getEventByEventmid($key);
            if ($iscnt > 0) {
                $param = array(
                    "self_id" => $val
                );
                ExtraEventModel::updatetypeEventById($key, $param);
            }
        }
        $v_res['status'] = true;
        echo json_encode($v_res, true);
        exit;
    }

    public function cronStubhubTrack() {
        $data = ExtraEventModel::getEventByidDate();
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        foreach ($data as $devent) {
            if (isset($devent['event_id']) && !empty($devent['event_id'])) {
                $eventid = $devent['type_eventid'];
                $response = $this->getStubhubdataByID($eventid);
                if (!isset($response['code']) && isset($response['numFound']) && $response['numFound'] > 0) {
                    foreach ($response['events'] as $event) {
                        $param = array(
                            "ticketInfo" => (isset($event['ticketInfo']) ? json_encode($event['ticketInfo'], true) : ''),
                            "last_checked" => time()
                        );
                        ExtraEventModel::updateEventById($devent['event_id'], $param);
                        $param = array(
                            "event_id" => $devent['event_id'],
                            "type_eventid" => $eventid,
                            "created_date" => time(),
                            "ticketInfo" => (isset($event['ticketInfo']) ? json_encode($event['ticketInfo'], true) : ''),
                        );
                        TrackEventData::trackData($param);
                    }
                }
            }
        }
    }

    public function cronVividTrack() {
        $data = ExtraEventModel::getEventByidDate('vividseats');
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        foreach ($data as $devent) {
            if (isset($devent['event_id']) && !empty($devent['event_id'])) {
                $eventid = $devent['type_eventid'];

                $response = $this->getVividdataByID($eventid);
                if (isset($response['global']) && count($response['global']) == 1) {
                    foreach ($response['global'] as $event) {
                        $tickettotal = 0;
                        foreach ($response['tickets'] as $ticket) {
                            $tickettotal = $tickettotal + $ticket['q'];
                        }
                        $event['totalticket'] = $tickettotal;

                        $max_price = 0;
                        $min_price = 0;
                        if (isset($event['lp']) && !empty($event['lp'])) {
                            $min_price = $event['lp'];
                        }
                        if (isset($event['hp']) && !empty($event['hp'])) {
                            $max_price = $event['hp'];
                        }
                        $ticketinfo = array(
                            "minListPrice" => $min_price,
                            "maxListPrice" => $max_price,
                            "totalTickets" => $event['totalticket'],
                            "totalListings" => $event['listingCount'],
                        );
                        $param = array(
                            "ticketInfo" => json_encode($ticketinfo, true),
                            "last_checked" => time()
                        );
                        ExtraEventModel::updateEventById($devent['event_id'], $param);
                        $param = array(
                            "event_id" => $devent['event_id'],
                            "type_eventid" => $eventid,
                            "created_date" => time(),
                            "ticketInfo" => json_encode($ticketinfo, true),
                        );
                        TrackEventData::trackData($param);
                    }
                }
            }
        }
    }

    public function stubhubDataGet() {
        $response = $this->getStubhubdataByID('104177732');
        echo '<pre>';
        print_r(json_decode($response));
        echo '</pre>';
        die;
    }

    public function searchEvent(Request $request) {
       
     if (Auth::User()->can('total-search')) {
          $userid = Auth::User()->user_id;
            if (Auth::User()->hasRole('admin')) {
                $eventcounter = 1;
                $setcounter = 2;
            } else {
                $setpermission = TotalSearchArtistPermissionModel::CountTotalSearch($userid);
                $setusercounter=TotalSearchArtistPermissionModel::CountUserTotalSearch($userid);
                $eventcounter=$setusercounter[0];
                $setcounter = $setpermission[0];
            }
      if ($setcounter >= $eventcounter) {
        $v_res = array();
        $v_res['status'] = false;
        $eventlist = array();
        $listeventhtml = '';
        $rules = array(
            'artist' => 'required',
            'source' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->messages();
            $error_data = "";
            if ($errors->any()) {
                $error_data = "<ul>";
                foreach ($errors->all() as $error) {
                    $error_data .= "<li>" . $error . "</li>";
                }
                $error_data .= "</ul>";
            }
            $v_res['msg'] = $error_data;
            $v_res['status'] = false;
        } else {
            $artist = urlencode($request->artist);
               $date = date('Y-m-d');
            foreach ($request->source as $source) {
                if ($source == "stubhub") {
                 
                    $SHendpoints = "/sellers/search/events/v3?performerName=" . $artist . "&rows=500&date=".$date;
                    $SHURL = config('config.SH_URL');
                    $apiurl = $SHURL . $SHendpoints;
                    $data = $this->curl_post($apiurl, 'SH', true);
                    $data = json_decode($data, true);
                    if (isset($data['events']) && !empty($data['events'])) {
                        foreach ($data['events'] as $event) {
                            $city = (isset($event['venue']['city']) && !empty($event['venue']['city']) ? $event['venue']['city'] : '');
                            $state = (isset($event['venue']['state']) && !empty($event['venue']['state']) ? $event['venue']['state'] : '');
                            $vname = (isset($event['venue']['name']) && !empty($event['venue']['name']) ? $event['venue']['name'] : '');
                            $weburl = (isset($event['webURI']) && !empty($event['webURI']) ? 'https://www.stubhub.com/'.$event['webURI'].'?showGCP=0': '');
                            $listeventhtml .= '<tr>
                                <td> 
                                    <div class="i-checks mr-2">
                                        <input id="addevent_' . $event['id'] . '" type="checkbox" value="' . $event['id'] . '" data-s="s"  class="checkbox-template addbulkevent" >
                                    </div> 
                                </td> 
                                <td>
                                    <a href="'.$weburl.'" target="_blank">' . $event['name'] . '</a>
                                </td> 
                                <td>
                                    ' . $vname . ', ' . $city . ', ' . $state . '
                                </td>
                                <td>
                                    ' . date('M j, Y - g:i a', strtotime($event['eventDateUTC'])) . '
                                </td>
                                <td>
                                   StubHub
                                </td> 
                            </tr>';
                        }
                    }
                }
                if ($source == "vivid") {
                    $results = $this->vividAPIRequest('https://skybox.vividseats.com/services/events?keywords=' . $artist.'&eventDateFrom='.$date);
                    
                    foreach ($results->rows as $event) { 
                         $city = (isset($event->venue->city) && !empty($event->venue->city) ? $event->venue->city : '');
                            $state = (isset($event->venue->state) && !empty($event->venue->state) ? $event->venue->state : '');
                            $vname = (isset($event->venue->name) && !empty($event->venue->name) ? $event->venue->name : '');
                            $weburl = (isset($event->vividSeatsEventUrl) && !empty($event->vividSeatsEventUrl) ? $event->vividSeatsEventUrl : '');
                            
                        $listeventhtml .= '<tr>
                                <td> 
                                    <div class="i-checks mr-2">
                                        <input id="addevent_' . $event->id . '" type="checkbox" value="' . $event->id . '" data-s="v"  class="checkbox-template addbulkevent" >
                                    </div> 
                                </td> 
                                <td>
                                    <a href="'.$weburl.'" target="_blank">' . $event->name . '</a>
                                </td> 
                                <td>
                                    ' . $vname . ', ' . $city . ', ' . $state . '
                                </td>
                                <td>
                                    ' .  date('M j, Y - g:i a', strtotime($event->date)) . '
                                </td>
                                <td>
                                   VividSeats
                                </td> 
                            </tr>';
                    }
                }
            }
            $v_res['status'] = true;
            $v_res['res_data'] = $listeventhtml;
             if (!Auth::User()->hasRole('admin')) {
                        $pencpunter=$eventcounter-1;
                        $updatecounter = TotalSearchArtistPermissionModel::UpdateCountTotalSearch($userid, $pencpunter);
                    }
        }
        echo json_encode($v_res, true);
        exit;
         } else {
                return Redirect::back()->withErrors(['You dont have a permission for this role. Please contact admin', "msg"]);
            }
    }
   }

    public function addBulkCheckedEvent(Request $request) {
         $userid = Auth::User()->user_id;
        $v_res = array();
        $v_res['status'] = false;
        $rules = array(
            'addBulk' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->messages();
            $error_data = "";
            if ($errors->any()) {
                $error_data = "<ul>";
                foreach ($errors->all() as $error) {
                    $error_data .= "<li>" . $error . "</li>";
                }
                $error_data .= "</ul>";
            }
            $v_res['msg'] = $error_data;
            $v_res['status'] = false;
        } else {
            $addBulk = $request->addBulk;
            foreach ($addBulk as $key => $value) {
                if ($value['datas'] == "s") {
                    $eventid = $value['id'];
                    $response = $this->getStubhubdataByID($eventid);
                    if (!isset($response['code']) && isset($response['numFound']) && $response['numFound'] > 0) {
                        foreach ($response['events'] as $event) {
                            $isExistcnt = ExtraEventModel::getEventByTypeidCount($event['id'], "SH");
                            if ($isExistcnt == 0) {
                                $res = $this->insertStubhub($event);
                            } else {
                                $evntddata = ExtraEventModel::getEventByTypeid($event['id'], "SH");
                                $isExistuE = UserEventModel::getUserEventByIdCount($userid, $evntddata['event_id']);
                                if ($isExistuE == 0) {
                                    $param = array(
                                        "user_id" => $userid,
                                        "event_id" => $evntddata['event_id'],
                                        "is_track" => 1,
                                        "created_date" => time()
                                    );
                                    UserEventModel::saveUserEvent($param);
                                }
                            }
                        }
                    }
                } else if ($value['datas'] == "v") {
                    $eventid = $value['id']; 
                    $response = $this->getVividdataByID($eventid); 
                    if (isset($response['global']) && count($response['global']) == 1) {
                        foreach ($response['global'] as $event) {
                            $tickettotal = 0;
                            foreach ($response['tickets'] as $ticket) {
                                $tickettotal = $tickettotal + $ticket['q'];
                            }
                            $event['totalticket'] = $tickettotal;
                            $event['weburl'] = 'https://www.vividseats.com/buy/Production.action?productionId='.$event['productionId'];

                            $isExistcnt = ExtraEventModel::getEventByTypeidCount($event['productionId'], 'VS');
                            if ($isExistcnt == 0) {
                                $res = $this->insertVivid($event);
                            } else {
                                $evntddata = ExtraEventModel::getEventByTypeid($event['productionId'], "VS");
                                $isExistuE = UserEventModel::getUserEventByIdCount($userid, $evntddata['event_id']);
                                if ($isExistuE == 0) {
                                    $param = array(
                                        "user_id" => $userid,
                                        "event_id" => $evntddata['event_id'],
                                        "is_track" => 1,
                                        "created_date" => time()
                                    );
                                    UserEventModel::saveUserEvent($param);
                                }
                            }
                        }
                    }
                }
            }
            $v_res['status'] = true;
        }
        echo json_encode($v_res, true);
        exit;
    }
    
    
    public function getSalesData(Request $request) {
       $event_id = $request->eventid;
        $v_res = array();
        $v_res['status'] = false;
        $listeventhtml = ''; 
                     $get_sales_event = StumhubSellModel::where('tod_stubhub_events_event_id', $event_id)->get();
                     $count = StumhubSellModel::where('tod_stubhub_events_event_id', $event_id)->count();
                     
                    if ($count!=0) {
                        foreach ($get_sales_event as $event) {
                            
                            $listeventhtml .= '<tr>
                                <td> 
                                   ' . $event['section'] . '
                                </td> 
                                <td>
                                   ' . $event['rows'] . '
                                </td> 
                                <td>
                                    ' . $event['quantity'] . '
                                </td>
                                <td>
                                    ' . $event['seats'] . '
                                </td>
                                <td>
                                    ' . $event['price'] . '
                                </td>
                                <td>
                                    ' .date('M j, Y - g:i a', strtotime($event['dateTime']))  . '
                                </td>
                               <td>
                                    ' . $event['deliveryMethod'] . '
                                </td>
                            </tr>';
                        }
                    }
                    else{
                        $listeventhtml .= '<tr><td colspan="7">Sale data not available</td></tr>';
                    }
         
            $v_res['status'] = true;
            $v_res['res_data'] = $listeventhtml;
        echo json_encode($v_res, true);
        exit;
         
   }
    public function HideEventCont(Request $request) {
         $v_res['status'] = false;
         if(ExtraEventModel::HideEventForuser($request->all()))
         {
             $v_res['status']=true;
         }
         echo json_encode($v_res, true);
         exit;
    }
    
     public function get_event_by_api()
    {
            $userid = Auth::User()->user_id;
            $data = UserModel::getUser($userid);
            $token=$data['application_token'];
            $api=$data['api_token'];
            $account=$data['account'];
            $setvalue = $this->getStubIdVividData($token,$api,$account);
            if(isset($setvalue->message))
            {
                 $setmsg=$setvalue->message;
                    return redirect('profile')->with("error", $setmsg);
            }
            
            
            
            if($setvalue=="API request rate limit reached, please try again later.")
            {
                 $setmsg=$setvalue;
                return redirect('profile')->with("error", $setmsg);
            }
            
            if (isset($setvalue) && !empty($setvalue)) {
               
                            $setmsg="Data Available";
                             $setdata="";
                             $stud=array();
                             $vivid=array();
                               foreach ($setvalue->rows as $key => $value) {
                              $city = (isset($value->event->venue->city) && !empty($value->event->venue->city) ? $value->event->venue->city : '');
                              $state = (isset($value->event->venue->state) && !empty($value->event->venue->state) ? $value->event->venue->state : '');
                              $vname = (isset($value->event->venue->name) && !empty($value->event->venue->name) ? $value->event->venue->name : '');
                              $stuburl = (isset($value->event->stubhubEventUrl) && !empty($value->event->stubhubEventUrl) ? $value->event->stubhubEventUrl : '');
                              $vividurl = (isset($value->event->vividSeatsEventUrl) && !empty($value->event->vividSeatsEventUrl) ? $value->event->vividSeatsEventUrl : '');
                              $date=$value->event->date;
                              if(strpos($stuburl, "stubhub.com") > -1)
                              {
                                  $source="stubhub";
                           
                                $stud[]=array(
                                    'id'=>$value->event->stubhubEventId,
                                    'name'=> $value->event->name,
                                    'venue'=> $vname . ', ' . $city . ', ' . $state ,
                                    'eventdate'=>date('M j, Y - g:i a', strtotime($date)),
                                    'source'=>$source,
                                    'weburl'=>$stuburl,
                                    'datas'=>'s'    
                                );
                             }
                             
                             if(strpos($vividurl, "vividseats.com") > -1)
                              {
                                  $source="vividSeats";
                           
                                $vivid[]=array(
                                    'id'=>$value->event->id,
                                    'name'=> $value->event->name,
                                    'venue'=> $vname . ', ' . $city . ', ' . $state ,
                                    'eventdate'=>date('M j, Y - g:i a', strtotime($date)),
                                    'source'=>$source,
                                    'weburl'=>$vividurl,
                                    'datas'=>'v'
                                );
                             }
                             
                        }
                        
                        $setdata =  array_merge($vivid, $stud); 
                        return view('pages.admin.extraevent.event-list-check' , compact('setdata'));
                       
                 } else {

                    $setmsg="Data Not-Available";
                    return redirect('profile')->with("error", $setmsg);
                }
    }

}
