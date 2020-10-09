<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Model\UserModel;
use App\Model\StumhubEvents;
use App\Model\StumhubSellModel;
use App\Model\RolesPermissionModel;
use App\Model\TotalSearchArtistPermissionModel;
use App\Model\ExtraEventModel;
use App\Model\TotalTrackPermissionModel;
use App\Model\UserEventModel;
use Validator;

class ApiController extends CommonController {

    public function login(Request $request) {
        $v_res = array();
        $rules = array(
            'username' => 'required', // make sure the email is an actual email
            'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
        );
        // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            $v_res['status'] = false;
            $v_res['msg'] = 'Required';
            return response()->json($v_res, 401);
        } else {
            $user = UserModel::where([
                        'username' => $request->input('username'),
                        'password' => $this->encrypt($request->input('password')),
                    ])->first();

            if ($user) {
                if ($user->status == 1) {
                    Auth::login($user, false);
                    $v_res['status'] = true;
                    $v_res['token'] = $user->createToken('TicketExtension')->accessToken;
                    $v_res['user_id']=$user->user_id;
                    
                    return response()->json($v_res, 200);
                } else {
                    $v_res['status'] = false;
                    $v_res['msg'] = 'Unauthorised';
                    return response()->json($v_res, 200);
                }
            } else {
                $v_res['status'] = false;
                $v_res['msg'] = 'Unauthorised';
                return response()->json($v_res, 200);
            }
        }
    }

    public function searchEvent(Request $request) { 
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

                    $SHendpoints = "/sellers/search/events/v3?performerName=" . $artist . "&rows=500&date=" . $date;
                    $SHURL = config('config.SH_URL');
                    $apiurl = $SHURL . $SHendpoints;
                    $data = $this->curl_post($apiurl, 'SH', true);
                    $data = json_decode($data, true);
                    if (isset($data['events']) && !empty($data['events'])) {
                        if (count($data['events']) > 0) {
                            $listeventhtml .= ' <thead>
                                    <th>Event</th>
                                    <th>Venue</th>
                                    <th>Event Date</th>
                                    <th></th>
                                    </thead>
                                    <tbody>';
                            foreach ($data['events'] as $event) {
                                $city = (isset($event['venue']['city']) && !empty($event['venue']['city']) ? $event['venue']['city'] : '');
                                $state = (isset($event['venue']['state']) && !empty($event['venue']['state']) ? $event['venue']['state'] : '');
                                $vname = (isset($event['venue']['name']) && !empty($event['venue']['name']) ? $event['venue']['name'] : '');
                                $weburl = (isset($event['webURI']) && !empty($event['webURI']) ? 'https://www.stubhub.com/' . $event['webURI'] : '');
                                $listeventhtml .= '<tr> 
                                <td>
                                    <a href="' . $weburl . '" target="_blank">' . $event['name'] . '</a>
                                </td> 
                                <td>
                                    ' . $vname . ', ' . $city . ', ' . $state . '
                                </td>
                                <td>
                                    ' . date('M j, Y - g:i a', strtotime($event['eventDateUTC'])) . '
                                </td>
                                <td>
                                  
                                </td> 
                            </tr>';
                            }

                            $listeventhtml .= ' </tbody>';
                        }
                    }
                }
            }
            $v_res['status'] = true;
            $v_res['res_data'] = $listeventhtml;
        }
        echo json_encode($v_res, true);
        exit;
    }

    public function storeStubSells(Request $request) {

        $v_res = array();
        $v_res['status'] = false;
        $event_id = $request->event_id;
        $get_event = StumhubEvents::where('event_id', $event_id)->first();
        if (empty($get_event['event_id'])) {
            $inserdata = StumhubEvents::saveEvent(array('event_id' => $event_id, 'created_date' => time(), "modified_date" => time()));

            if (!empty($inserdata)) {
                $stmb_sells = json_decode($request->stub_sells);
                foreach ($stmb_sells as $stubhub_sells) {
                    $params = array(
                        "tod_stubhub_events_event_id" => $event_id,
                        "section" => $stubhub_sells[0],
                        "rows" => $stubhub_sells[1],
                        "quantity" => $stubhub_sells[2],
                        "seats" => $stubhub_sells[3],
                        "price" => $stubhub_sells[4],
                        "dateTime" => $stubhub_sells[5],
                        "deliveryMethod" => $stubhub_sells[6],
                    );
                    StumhubSellModel::saveEvent($params);
                }
                $v_res['status'] = true;
            }
        }
        echo json_encode($v_res, true);
        exit;
    }
    
    
    public function searchPermission(Request $request,$setPer=10) {
        $v_per = array();
            if(isset($request->userid))
            {
                  $setvale=RolesPermissionModel::getRolePermissionUser($request->userid,$setPer);
                  if($setvale==0)
                  {
                      $v_per['permission_search']=false;
                  }
                  else
                  {
                      $v_per['permission_search']=true;
                  }
            }
            else
            {
                $v_per['permission_search']=false;
            }
        echo json_encode($v_per, true);
        exit;
    }
    
    public function SerachIncrease(Request $request) {
        
            if(isset($request->userid))
            {
                  $setvale=RolesPermissionModel::getRoleIdForUser($request->userid);
                  if($setvale!=1)
                  { 
                  $setusercounter=TotalSearchArtistPermissionModel::CountUserTotalSearch($request->userid);
                  $eventcounter=$setusercounter[0];
                  $pencpunter=$eventcounter-1;
                  $updatecounter = TotalSearchArtistPermissionModel::UpdateCountTotalSearch($request->userid, $pencpunter);
                  }
             }
           echo true;  
    }
    
    public function getSetSerach(Request $request) {
          $v_search=array();
            if(isset($request->userid))
            {
                $setvale=RolesPermissionModel::getRoleIdForUser($request->userid);
                  if($setvale==1)
                  { 

                    $v_search['usercounter']=1;
                    $v_search['setcounter']=1;
                  }else{
                        $setpermission = TotalSearchArtistPermissionModel::CountTotalSearch($request->userid);
                        $setusercounter=TotalSearchArtistPermissionModel::CountUserTotalSearch($request->userid);
                        $eventcounter=$setusercounter[0];
                        $setcounter = $setpermission[0];
                         $v_search['usercounter']=$eventcounter;
                         $v_search['setcounter']=$setcounter;
                  }
                
                   
             }
             else
             {
                  $v_search['usercounter']=0;
                    $v_search['setcounter']=1;
             }
             
          echo json_encode($v_search, true); 
    }
    
    //track event check already track or not
      public function trackEventCheck(Request $request) {
        $v_per = array();
            if(!empty($request->userid) && !empty($request->eventId))
            { 
                  $evntddata = ExtraEventModel::getEventByTypeid($request->eventId, "SH");
                  $isExistuE = UserEventModel::getUserEventByIdCount($request->userid, $evntddata['event_id']);
                  if($isExistuE==0)
                  {
                        $v_per['check_track']=true;
                  }
                  else
                  {
                      $v_per['check_track']=false;
                  }
            }
            else
            {
                $v_per['check_track']=false;
            }
        echo json_encode($v_per, true);
        exit;
    }
    
    
    //add event and track event function
     public function trackCheckedEvent(Request $request) {
         $v_res=array();
          if(!empty($request->userid) && !empty($request->EventId))
          {
             $userid = $request->userid;
             $EventId = $request->EventId;
             
             $setvale=RolesPermissionModel::getRolePermissionUser($userid,4);
                  if($setvale==0){
                      $v_res['status'] = "notper";
                  }
                  else
                  {
                            $evntddata = ExtraEventModel::getEventByTypeid($EventId, "SH");
                            $isExistuE = UserEventModel::getUserEventByIdCount($userid, $evntddata['event_id']);
                            if($isExistuE==0)
                            {
                                
                                  $response = app('App\Http\Controllers\ExtraEventController')->getStubhubdataByID($EventId);
                                    if (!isset($response['code']) && isset($response['numFound']) && $response['numFound'] > 0) {
                                        foreach ($response['events'] as $event) {
                                            $isExistcnt = ExtraEventModel::getEventByTypeidCount($event['id'], "SH");
                                            if ($isExistcnt == 0) {
                                                $res = app('App\Http\Controllers\ExtraEventController')->insertStubhub($event);
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
                               
                                $v_res['status']="true";
                            }
                            else
                            {
                                $v_res['status']="alreadyin";
                            }
                            
                  }
          }
          else
          {
              $v_res['status'] = "false";
          }
        echo json_encode($v_res, true);
    }
    

}
