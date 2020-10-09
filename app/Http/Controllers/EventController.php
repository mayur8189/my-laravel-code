<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\EventModel;
use App\Model\LoginModel;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Validator;

class EventController extends CommonController {

    public function __construct(Request $request) {
        $this->event = new EventModel();
    }

    public function listEvents() {
        return view('pages.admin.event.list-events');
    }

    public function ticketMasterLookup() {
        return view('pages.admin.event.ticket-master-code-lookup');
    }

    public function validateOfferCode(Request $request) {
        $v_res = array();
        $rules = array(
            'event_id' => 'required',
            'offercode' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->messages();
            $error_data = "";
            if ($errors->any()) {
                $error_data = "<ul class='pl-0' style='list-style-type: none;'>";
                foreach ($errors->all() as $error) {
                    $error_data .= "<li>" . $error . "</li>";
                }
                $error_data .= "</ul>";
            }
            $v_res['msg'] = $error_data;
            $v_res['status'] = false;
        } else {
            $event_id = $request->event_id;
            $offercode = $request->offercode;
            $response = $this->getTicketcodeLookup($event_id, $offercode);
            $data = json_decode($response,true);
            $response = "Response:  ".$response;
            if(isset($data['error']) && $data['error']=="CODE_INCORRECT"){
                $response = "Code:  ".$offercode."\n"."Result:  CODE_INCORRECT\n".$response;
            }
            $v_res['msg'] = $response;
            $v_res['status'] = true;
        }
        echo json_encode($v_res, true);
        die;
    }

    public function listEventsRecord(Request $request) {
        $listuser = $this->event->listEvent($request->all());
        $userid = Auth::User()->user_id;
        $userdata = LoginModel::getLogin($userid);
        $logouttime = "";
        if (isset($userdata->logout_time) && !empty($userdata->logout_time)) {
            $logouttime = $userdata->logout_time;
        }
        return DataTables::of($listuser)
                        ->editColumn('presaledate', function($data) use ($logouttime) {
                            $presaledate = "N/A";
                            if (!empty($data->presaledate)) {
                                $presaledatearray = $data->presaledate;
                                $presaledatearray = json_decode($presaledatearray, true);
                                $presaledate = "";
                                foreach ($presaledatearray as $date) {
                                    $startdate = date_create(date('Y-m-d H:i:s', strtotime($date['startDateTime'])), timezone_open("US/Eastern"));
                                    $enddate = date_create(date('Y-m-d H:i:s', strtotime($date['endDateTime'])), timezone_open("US/Eastern"));
                                    $startdate = date_format($startdate, "M j, Y - g:i a");
                                    $enddate = date_format($enddate, "M j, Y - g:i a");
                                    $presaledate .= "<span class='nowrap'> <b>S</b> : " . $startdate . "<br><b>E</b> : " . $enddate . "</span><br><hr style='margin-top:0px;margin-bottom:0px'>";
                                }
                            }
                            $isnew = "";
                            if ($data->created_date > $logouttime) {
                                $isnew = "new-event";
                            }
                            return '<span class="' . $isnew . '">' . $presaledate . '</span>';
                        })
                        ->editColumn('onsaledate', function($data) use ($logouttime) {
                            $onsaledate = "N/A";
                            if (!empty($data->onsaledate)) {
                                $date = date_create(date('Y-m-d H:i:s', strtotime($data->onsaledate)), timezone_open("US/Eastern"));
//                                $onsaledate = date_format($date, "Y-m-d H:i:s");
                                $onsaledate = date_format($date, "M j, Y") . '<br>' . date_format($date, "g:i a");
//                                $onsaledate = $data->onsaledate;
                            }
                            $isnew = "";
                            if ($data->created_date > $logouttime) {
                                $isnew = "new-event";
                            }
                            return '<span class="nowrap ' . $isnew . '">' . $onsaledate . '</span>';
                        })
                        ->editColumn('created_date', function($data) use ($logouttime) {
                            $addeddate = "N/A";
                            $isnew = "";
                            if (!empty($data->created_date)) {
                                if ($data->created_date > $logouttime) {
                                    $isnew = "new-event";
                                }
                                $addeddate = date('M j, Y', $data->created_date) . '<br>' . date('g:i a', $data->created_date);
                            }
                            return '<span class="nowrap ' . $isnew . '">' . $addeddate . '</span>';
                        })
                        ->editColumn('eventdate', function($data) use ($logouttime) {
                            $eventdate = "N/A";
                            if (!empty($data->eventdate)) {
//                                $eventdate = $data->eventdate;
                                $eventdate = date('M j, Y', strtotime($data->eventdate)) . '<br>' . date('g:i a', strtotime($data->eventdate));
                            }
                            $isnew = "";
                            if ($data->created_date > $logouttime) {
                                $isnew = "new-event";
                            }
                            return '<span class="nowrap ' . $isnew . '">' . $eventdate . '</span>';
                        })
                        ->editColumn('url', function($data) use ($logouttime) {
                            if (empty($data->url)) {
                                return '<span class="text-center">N/A</span>';
                            }
                            $url = $data->url;
                            if (strlen($data->url) > 40) {
                                $url = substr($data->url, 0, 40) . '...';
                            }
                            return '<a href="' . $data->url . '"  target="_blank"/>' . $url . '</a>';
                        })
                        ->editColumn('min_price', function($data) use ($logouttime) {
                            $isnew = "";
                            if ($data->created_date > $logouttime) {
                                $isnew = "new-event";
                            }
                            if (empty($data->min_price)) {
                                return '<span class="text-center ' . $isnew . '">N/A</span>';
                            }

                            return '<span class="' . $isnew . '">' . $data->min_price . '</span>';
                        })
                        ->editColumn('max_price', function($data) use ($logouttime) {
                            $isnew = "";
                            if ($data->created_date > $logouttime) {
                                $isnew = "new-event";
                            }
                            if (empty($data->max_price)) {
                                return '<span class="text-center ' . $isnew . '">N/A</span>';
                            }

                            return '<span class="' . $isnew . '">' . $data->max_price . '</span>';
                        })
                        ->editColumn('seatmap', function($data) use ($logouttime) {
                            if (empty($data->seatmap)) {
                                return '<span class="text-center">N/A</span>';
                            }
                            return '<img src="' . $data->seatmap . '" style="width:150px" />';
                        })
                        ->editColumn('source', function($data) use ($logouttime) {
                            if (empty($data->source)) {
                                return '<span class="text-center">N/A</span>';
                            }
                            if ($data->source == "ticketmaster") {
                                $source = "tm.png";
                                $title = "Ticket Master";
                            }
                            return '<img src="' . url('resources/assets/img/' . $source) . '" style="width:32px" title="' . $title . '" />';
                        })
                        ->editColumn('event_type', function($data) use ($logouttime) {

                            $isnew = "";
                            if ($data->created_date > $logouttime) {
                                $isnew = "new-event";
                            }
                            return '<span class="' . $isnew . '">' . $data->event_type . '</span>';
                        })
                        ->editColumn('name', function($data) use ($logouttime) {

                            $isnew = "";
                            if ($data->created_date > $logouttime) {
                                $isnew = "new-event";
                            }
                            return '<span class="' . $isnew . '" style="word-break:break-word">' . $data->name . '</span>';
                        })
                        ->editColumn('city', function($data) use ($logouttime) {

                            $isnew = "";
                            if ($data->created_date > $logouttime) {
                                $isnew = "new-event";
                            }
                            return '<span class="' . $isnew . '">' . $data->city . '</span>';
                        })
                        ->editColumn('venue', function($data) use ($logouttime) {

                            $isnew = "";
                            if ($data->created_date > $logouttime) {
                                $isnew = "new-event";
                            }
                            return '<span class="' . $isnew . '">' . $data->venue . '</span>';
                        })
                        ->rawColumns(['seatmap', 'event_type', 'venue', 'city', 'name', 'presaledate', 'onsaledate', 'eventdate', 'url', 'min_price', 'max_price', 'source', 'created_date'])
                        ->make('true');
    }

    public function cronTickermasterUS() {
        ini_set('max_execution_time', 0);
        $myfile = fopen("cronlog.txt", "a") or die("Unable to open file!");
        $txt = date('Y-m-d H:i') . ' US Cron ';
        fwrite($myfile, $txt . "\n");
        fclose($myfile);
        $page = 0;
        $response = $this->getTicketmasterdata($page, 200, 'KZFznnSyZfZ7v7nE,KZiwnSyZfZ7v7na,KZFznSyZfZ7v7nJ', 'US');
        if (isset($response['_embedded']['events'])) {
            foreach ($response['_embedded']['events'] as $key => $event) {
                $this->insertTicketMaster($event);
            }
        }
        sleep(10);
        if (isset($response['page']['totalPages']) && $response['page']['totalPages'] > 0) {
            for ($ipage = 1; $ipage < $response['page']['totalPages']; $ipage++) {
                $response = $this->getTicketmasterdata($ipage, 200, 'KZFzniwnSyZfZ7vE,,KZFzniwnSyfZ7v7nJ', 'US');
                if (isset($response['_embedded']['events'])) {
                    foreach ($response['_embedded']['events'] as $key => $event) {
                        $this->insertTicketMaster($event);
                    }
                }
                sleep(10);
            }
        }
        echo '<pre>';
        echo "Done";
        echo '</pre>';
        die;
    }

    public function cronTickermasterCA() {
        ini_set('max_execution_time', 0);
        $myfile = fopen("cronlog.txt", "a") or die("Unable to open file!");
        $txt = date('Y-m-d H:i') . ' CA Cron ';
        fwrite($myfile, $txt . "\n");
        fclose($myfile);
        $page = 0;
        $response = $this->getTicketmasterdata($page, 200, 'KZFzniwnSyZfv7nE,KZFzniSyZfZ7v7na,KZFzniwnSyZfZ7v7nJ', "CA");
        if (isset($response['_embedded']['events'])) {
            foreach ($response['_embedded']['events'] as $key => $event) {
                $this->insertTicketMaster($event);
            }
        }
        sleep(10);
        if (isset($response['page']['totalPages']) && $response['page']['totalPages'] > 0) {
            for ($ipage = 1; $ipage < $response['page']['totalPages']; $ipage++) {
                $response = $this->getTicketmasterdata($ipage, 200, 'KFzniwnSyZfZ7v7nE,KZFzniwnZfZ7v7na,KZFzniwnSyZfZ7v7n', "CA");
                if (isset($response['_embedded']['events'])) {
                    foreach ($response['_embedded']['events'] as $key => $event) {
                        $this->insertTicketMaster($event);
                    }
                }
                sleep(10);
            }
        }
        echo '<pre>';
        echo "Done";
        echo '</pre>';
        die;
    }

    public function insertTicketMaster($event) {
        $city = (isset($event['_embedded']['venues'][0]['city']['name']) && !empty($event['_embedded']['venues'][0]['city']['name']) ? $event['_embedded']['venues'][0]['city']['name'] : '');
        $state = (isset($event['_embedded']['venues'][0]['state']['name']) && !empty($event['_embedded']['venues'][0]['state']['name']) ? $event['_embedded']['venues'][0]['state']['name'] : '');
        $vname = $event['_embedded']['venues'][0]['name'];
        $eventtype = (isset($event['classifications'][0]['segment']['name']) && !empty($event['classifications'][0]['segment']['name']) ? $event['classifications'][0]['segment']['name'] : '');
        $max_price = 0;
        $min_price = 0;
        if (isset($event['priceRanges']) && !empty($event['priceRanges'])) {
            if (isset($event['priceRanges'][0]['max'])) {
                $max_price = $event['priceRanges'][0]['max'];
            }
            if (isset($event['priceRanges'][0]['min'])) {
                $min_price = $event['priceRanges'][0]['min'];
            }
        }
        $presaledate = "";
        $onsaledate = "";
        $eventdate = "";
        $images = "";
        $venue_array = "";
        $isPresalematch = 0;

        $date = date('Y-m-d');
        $nexday = date('Y-m-d', strtotime('+1 days'));
        if (isset($event['sales']['presales']) && !empty($event['sales']['presales'])) {
            $presaledate = json_encode($event['sales']['presales'], true);
            foreach ($event['sales']['presales'] as $datepre) {
                if (substr($datepre['startDateTime'], 0, 10) == $date) {
                    $isPresalematch++;
                }
            }
        }
        if (isset($event['sales']['public']['startDateTime']) && !empty($event['sales']['public']['startDateTime'])) {
            $onsaledate = $event['sales']['public']['startDateTime'];
        }
        if (isset($event['dates']['start']['localDate']) && !empty($event['dates']['start']['localDate'])) {
            $eventdate = $event['dates']['start']['localDate'];
        }
        if (isset($event['dates']['start']['localTime']) && !empty($event['dates']['start']['localTime'])) {
            $eventdate .= ' ' . $event['dates']['start']['localTime'];
        } else {
            $eventdate .= ' 00:00:00';
        }

        if (isset($event['images']) && !empty($event['images'])) {
            $images = json_encode($event['images'], true);
        }
        if (isset($event['_embedded']['venues']) && !empty($event['_embedded']['venues'])) {
            $venue_array = json_encode($event['_embedded']['venues'], true);
        }
        $isExistcnt = EventModel::getEventByTypeidCount($event['id']);
        $isDateOk = FALSE;
        if (substr($onsaledate, 0, 10) == $date || $isPresalematch > 0) {
            $isDateOk = TRUE;
        }
        if ($isDateOk && $isExistcnt == 0) {
            $params = array(
                "type_eventid" => (isset($event['id']) && !empty($event['id']) ? $event['id'] : ''),
                "event_type" => $eventtype,
                "name" => (isset($event['name']) && !empty($event['name']) ? $event['name'] : ''),
                "description" => (isset($event['description']) && !empty($event['description']) ? $event['description'] : ''),
                "images" => $images,
                "presaledate" => $presaledate,
                "onsaledate" => $onsaledate,
                "eventdate" => $eventdate,
                "city" => $city,
                "venue" => $vname . ', ' . $city . ', ' . $state,
                "venue_json" => $venue_array,
                "min_price" => $min_price,
                "max_price" => $max_price,
                "price_range" => (isset($event['priceRanges']) ? json_encode($event['priceRanges'], true) : ''),
                "url" => (isset($event['url']) && !empty($event['url']) ? $event['url'] : ''),
                "seatmap" => (isset($event['seatmap']['staticUrl']) && !empty($event['seatmap']['staticUrl']) ? $event['seatmap']['staticUrl'] : ''),
                "source" => 'ticketmaster',
                "created_date" => time(),
                "modified_date" => time(),
            );
            EventModel::saveEvent($params);
        }
        return true;
    }

    public function getTicketmasterdata($page, $size = 200, $segmentId = '', $countryCode = "US") {
        $TMURL = config('config.TM_URL');
        $method = 'events';
        $TMKEY = config('config.TM_KEY');
        $segment = '';
        if (!empty($segmentId)) {
            $segment = '&segmentId=' . $segmentId;
        }
        $date = date('Y-m-d');
//        $nexday = date('Y-m-d', strtotime('+1 days')) . 'T00:00:00';
//        $date1 = date('Y-m-d') . 'T00:00:00'; 
        $apiurl = $TMURL . $method . '?apikey=' . $TMKEY . '&locale=*&onsaleOnStartDate=' . $date . '&size=' . $size . '&sort=date,asc&countryCode=' . $countryCode . '&page=' . $page . $segment;
        $tmdata = $this->curl_post($apiurl, 'TM');
        $tmdata = json_decode($tmdata, true);
        return $tmdata;
    }

    public function getStubhubdata($page, $size = 200) {
        $SHendpoints = "/sellers/search/events/v3";
        $SHURL = config('config.SH_URL');
        $apiurl = $SHURL . $SHendpoints;
        $test = $this->curl_post($apiurl, 'SH', true);
        $test = json_decode($test);
        $tmdata = json_decode($tmdata, true);
        return $tmdata;
    }

    public function getTicketcodeLookup($event_id, $offercode) {
      $url = 'https://www1.ticketmaster.com/ipa/v2/offercode/validate?eventId=' . $event_id . '&code='.$offercode;
        $ch = curl_init();
        $headers = array();
        $headers[] = 'Cookie: '
                . '_ga=GA1.2.1140041319.1570854324; '
                . '_gid=GA1.2.280129427.1570854324; '
                . 'SSESSba67f03972f55553598b0a8abebb2c0d=5655-VvinsmQ; '
                . 'tk-u=NzZhZGQ0MmEtNWM1ZS00NDcyLTg3OTktN65tTBmMzgxZWQwMmY1; tk-api-email=bWF5dX4OUBnbWFpbC5jb20; tk-api-key=WyJnMVJNUw3S05iN0FCbVEzZkIxelBKbWZvY2xOMCJd; tk-api-apps=W3sibmFtZSI6Im1heXVycy1BcHAiLCJjcmVhdGVkIjoiMjAxOS0wNy0zMSAwNDo1NiBBTSIsImtleSI6ImcxUnVjMk41TDdLTmI3UTNmQjF6UEptZm9jbE4wIn1d; tk-user-roles=WyJhd0aWNhdGVkIHVzZXIiXQ; LANGUAGE=en-us; SID=0G-7PpeZJ7YvgWhoSH_ixZnySvVDx-h4gQbTneB3A4gEtH2jj8pr-eykoOMcHGbO4tEue75tS8i47Vli; GEO_OMN=unk; AB=B; D_ZID=F5131953-99BB-3590-AE41-8E331DD7501C; D_ZUID=047E5F96-2BE8-3828-9686-E44E272D6CAB; D_HID=D6D2BFF4-4EF1-3506-9729-832128E06022; D_SID=43.240.8.99:N+oqOIGlJ4Iufwk+fWBmGoBPPvF03nbaCTIT3FNx3Es; BID=viLnyj3y2PXF-KaBFeNnRc3aOxtMnPr_5FUDZ0kfhezJwc0DMOPbL8mFM-jxHVf_5Vl16WnHszVjst-7; _gcl_au=1.1.93257551.1570880016; TMFS=false; D_IID=978FC5E6-3FB6-3516-AEB8-1AD982734C77; D_UID=EB020928-7728-3368-8C90-A98D334B14F0; w2a_branch={"web_referrer":"http://localhost/ticketbroker/list-events"}; _fbp=fb.1.1570880017850.422209497; _dvs=0:k1nhejn9:2FPOOBvdoRsVo19S3vflaXLkZoE_OgwK; _dvp=0:k1nhejn9:4zoD3A4vOO9po05hzBqeiHFqp6yUMkTi; __qca=P0-2133590069-1570880019144; mt.pc=2.1; mt.g.2f013145=2.1446834816.1570880028586; __gads=ID=67c39e471efe84bb:T=1570880031:S=ALNI_MaHeTEfL0XBhjulVwcoKck94dUMKw; IDCID2=cbad3f82-4b53-4715-b0e9-c9f5dd06cacd; PFTPI=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzUxMiJ9.eyJyZWRpcmVjdFVyaSI6Imh0dHBzOi8vYXV0aC50aWNrZXRtYXN0ZXIuY29tL3NlY3VyZS1hY2NvdW50cy93aWRnZXQvY29tcGxldGUifQ.WkzzRhKngzQDheyBWG_yXbFtGnSoSEuyH0x8eiN5rd54r-D3wTquIHy_yTS71JQOHUnlK-AydPkcGbMk1574ZcaerLhu63HbVGyNq40ChrK6NgUNQ7fz28ClsWv4R3yI3AIRfKfOkXI90cNDyjqFL2QMlzIQZHTbFlyp4NZBpYWBo-attC6A8yYZqYl0Vomcpq8iC76avpssn0tEpnuDraRsYQ8pDTB-2DNgyafmDwlJE7pkC53FXvhx0oJEQ8urDcyFy8s1Tb1F-sI9wrcfYxaYwUXagxBSDYpdYDL6EpPF1ZB_NW-WSkjm5jsbSrxQ_Qiaf2wY6jWVfbeDjr6bpA; _pxvid=325f5d41-ece4-11e9-bb50-c56e230142f1; _px2=eyJ1IjoiMzM5OGM3YTAtZWNlNC0xMWU5LTgyZDktNTE1MDVjNGVmZDU4IiwidiI6IjMyNWY1ZDQxLWVjZTQtMTFlOS1iYjUwLWM1NmUyMzAxNDJmMSIsInQiOjE1NzA4ODAzNDg3NTQsImgiOiIxMzRkNDVjM2I3OWQ3NmMxNzQ1MjZjYmJjM2Q4OTE2MDU5NzBjNWVhZmQ2YzM3ZTUzMzYyYTJiNjE1MjRjMmI5In0=; CAMEFROM=CFC_BUYAT_73030; MC_ID=aff_BUYAT_73030; TMUO=west_jnUljVrN9uTwaXtkNpcbjb0JNUdQecY93Ssv8ESwOSk%3D%0A; MARKET_NAME=; MARKET_ID=32; csrf_token=d335ea6ca0cc4986633249a04f62be4e; TMSID=0G-7PpeZJ7YvgWhoSH_ixZnySvVDx-h4gQbTneB3A4gEtH2jj8pr-eykoOMcHGbO4tEue75tS8i47Vli; mt.v=2.1446834816.1570880028586; dcImpactRadius2=true; _ga_otc=aff_BUYAT_73030; _ga_cfc=CFC_BUYAT_73030; TM_PIXEL={"_dvs":"0:k1nhejn9:2FPOOBvdoRsVo19S3vflaXLkZoE_OgwK","_dvp":"0:k1nhejn9:4zoD3A4vOO9po05hzBqeiHFqp6yUMkTi","cfc":"cfc_buyat_73030"}; IR_gbd=ticketmaster.com; IR_4272=1570880106752%7C-1%7C1570880090907%7Cxrd2IkXivxyJW5zwUx0Mo3QzUkn2Cy3ELS0U3Q0%7C; IR_PI=1570880090907.xjedxkxdz78%7C1570966506752; s_pers=%20s_vnum%3D1572546600477%2526vn%253D1%7C1572546600477%3B%20s_fid%3D3146FE304D07896B-25B0D04289146577%7C1634038536346%3B%20gpv1%3DTM_US%253A%2520Microsite%253A%2520Ticket%2520Deals%2520Microsite%253A%2520Home%7C1570881936351%3B%20s_vs%3D1%7C1570881936355%3B%20s_invisit%3Dtrue%7C1570881936448%3B%20s_visit%3D1%7C1570881936461%3B%20gpv%3DTM_US%253A%2520Microsite%253A%2520Ticket%2520Deals%2520Microsite%253A%2520Home%7C1570881936486%3B; s_sess=%20s_ria%3Dflash%2520not%2520detected%257C%3B%20cpcbrate%3D0%3B%20s_campaign%3Daff_BUYAT_73030%3B%20s_eVar30%3DCFC_BUYAT_73030%3B%20s_ppvl%3D%3B%20s_cc%3Dtrue%3B%20s_sq%3D%3B%20s_ppv%3DTM_US%25253A%252520Microsite%25253A%252520Ticket%252520Deals%252520Microsite%25253A%252520Home%252C69%252C69%252C900%252C1920%252C900%252C1920%252C1080%252C1%252CL%3B';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        return $response;
    }
   public function testurl() {
      $url = 'https://www.ticketmaster.com/';
        $ch = curl_init();
        $headers = array();
        $headers[] = 'cookie: _ga=GA1.2.1140041319.1570854324; SSESSba67f03972f55553598b0a8abebb2c0dg=AgvKijtRzeJ4g6FVqgK94LKFNKh156frNu7-VvinsmQ; tk-u=NzZhZGQ0MmEtNWM1ZS00NDcyLTg3OTktNTBmMzgxZWQwMmY1; tk-api-email=bWF5dXIuODE4OUBnbWFpbC5jb20; tk-api-key=WyJnMVJ1YzJONUw3S05iN0FCbVEzZkIxelBKbWZvY2xOMCJd; tk-api-apps=W3sibmFtZSI6Im1heXVycy1BcHAiLCJjcmVhdGVkIjoiMjAxOS0wNy0zMSAwNDo1NiBBTSIsImtleSI6ImcxUnVjMk41TDdLTmI3QUJtUTNmQjF6UEptZm9jbE4wIn1d; tk-user-roles=WyJhdXRoZW50aWNhdGVkIHVzZXIiXQ; _gcl_au=1.1.93257551.1570880016; TMFS=false; _fbp=fb.1.1570880017850.422209497; __qca=P0-2133590069-1570880019144; tmff=lineup-images%3Dfalse; disco_location=%7B%22geoHash%22%3A%22tegb%22%2C%22latLong%22%3A%2221.233%2C72.864%22%7D; D_ZID=285D1816-2831-3A7E-BD3A-02CA9CAE456E; D_ZUID=1730F137-46E0-3BAC-91D9-B75B25BE85D1; D_HID=5BEA4CF8-FFD1-3A1F-9AF4-9B766C29AFB4; D_SID=43.240.8.99:N+oqOIGlJ4Iufwk+fWBmGoBPPvF03nbaCTIT3FNx3Es; _dvp=0:k1nhejn9:4zoD3A4vOO9po05hzBqeiHFqp6yUMkTi; __gads=ID=67c39e471efe84bb:T=1570880031:S=ALNI_MaHeTEfL0XBhjulVwcoKck94dUMKw; seerid=u_153508619375762430; IDCID2=cbad3f82-4b53-4715-b0e9-c9f5dd06cacd; _pxvid=325f5d41-ece4-11e9-bb50-c56e230142f1; MARKET_NAME=; MARKET_ID=32; mt.v=2.1446834816.1570880028586; dcImpactRadius2=true; ku1-vid=1e454eac-922b-ab26-63de-daf6393b9fef; cto_lwid=32cacc49-df94-4884-8ad6-199cdabd8475; _ga_otc=aff_BUYAT_73030; _ga_cfc=CFC_BUYAT_73030; CAMEFROM=CFC_BUYAT_73030; IR_PI=1570880090907.xjedxkxdz78%7C1570973735317; BID=ZHkv3NTXB8mosOv6lYgrOXsrXnvEzg4NMlVw152RwJGj630iXJeqVb1yo1O8xoHacuNFgZmRPh3CaQrt; s_pers=%20s_vnum%3D1572546600477%2526vn%253D2%7C1572546600477%3B%20s_fid%3D3146FE304D07896B-25B0D04289146577%7C1634045757351%3B%20gpv1%3DTM_US%253A%2520Microsite%253A%2520Ticket%2520Deals%2520Microsite%253A%2520Home%7C1570889157355%3B%20s_vs%3D1%7C1570889157360%3B%20s_invisit%3Dtrue%7C1570889157400%3B%20s_visit%3D1%7C1570889157407%3B%20gpv%3DTM_US%253A%2520Microsite%253A%2520Ticket%2520Deals%2520Microsite%253A%2520Home%7C1570889157423%3B; TMNUO=dwest_sOK9hnI8tqIvDkFszF0AM4ASHIOEFS89Vxx6U1pl23A=; TMUO=east_CWhh42GTZbq4zQTPExjvUbx+bFe/spTUkxhyAJu8B8M=; LANGUAGE=en-us; mt.pc=2.1; mt.g.2f013145=2.1446834816.1570880028586; w2a_branch={"web_camefrom":"CFC_BUYAT_73030","web_referrer":"https://www.google.com/"}; _gid=GA1.2.603005582.1571029112; seerses=e; TM_PIXEL={"_dvs":"0:k1py64zb:_9POahUe4ibgtsKwmbfgSs1GpBxXFFdy","_dvp":"0:k1nhejn9:4zoD3A4vOO9po05hzBqeiHFqp6yUMkTi"}; _dvs=0:k1py64zb:_9POahUe4ibgtsKwmbfgSs1GpBxXFFdy; tmsid=c62bad0d-1c1e-4d1c-9f07-3100b4aa7f3e; azk=ue1-722a3ea22b664092bba037ff4ec41702; _dc_gtm_UA-60025178-2=1; _gat_UA-60025178-2=1; _dc_gtm_UA-60025178-1=1; _gat_UA-60025178-1=1; D_IID=597076A4-3E6D-3012-B16C-6430D040435D; D_UID=C6E6BEAA-D88E-3CA3-9187-2EF4DE30AECF; TMSO=seed=7251ddad2f70&exp=1571117368&kid=key1&sig=0x9066aa84a9af701e397a59ae4ccf33d5fa5692043615bc6038210e9cd9c45dba1a33df5ad7ea935a6b471dde468b1c989e2ae4bb9968c72850069a572a9068f3';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.google.com/');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1); 
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
         $info = curl_getinfo($ch);
        echo '<pre>';
        print_r($response);
        echo '</pre>';
        die;
        return $response;
    }

}
