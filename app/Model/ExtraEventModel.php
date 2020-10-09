<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use DB;

class ExtraEventModel extends Authenticatable {

    public $table = 'tod_extra_events';
    public $primaryKey = 'event_id';
    public $timestamps = false;

    public static function saveEvent($data) {
        return ExtraEventModel::insertGetId($data);
    }

 public function listEvent($data) {
       
        $userid = Auth::User()->user_id;
        $listevents = ExtraEventModel::query()->select('*');
        $listevents->join('tod_user_events', 'tod_user_events.event_id', "=", "tod_extra_events.event_id");
        if (isset($data['search']) && !empty($data['search'])) {
            $listevents->orWhere(function ($query) use ($data) {
                $query->orWhere('name', 'LIKE', '%' . $data['search'] . '%');
                $query->orWhere('ancestors', 'LIKE', '%' . $data['search'] . '%');
            });
        }
        
        if (!Auth::User()->hasRole('admin')) {
             $listevents->where('tod_user_events.user_id', $userid);
             //$listevents->where('tod_extra_events.status', 1);
         }
         
//        $listevents->where('tod_user_events.event_id', '6');
        $listevents->orderBy('tod_extra_events.source', 'ASC');
        return $listevents->get();
    }
    
      public function UserlistEvent($userid=0) {
        if(isset($userid) && !empty($userid) && $userid!=0)
        {
           $userid = $userid;
        }
        else{
            return redirect('/list-users');
        }
        
        
        $listevents = ExtraEventModel::query()->select('*');
        $listevents->join('tod_user_events', 'tod_user_events.event_id', "=", "tod_extra_events.event_id");
        if (isset($data['search']) && !empty($data['search'])) {
            $listevents->orWhere(function ($query) use ($data) {
                $query->orWhere('name', 'LIKE', '%' . $data['search'] . '%');
                $query->orWhere('ancestors', 'LIKE', '%' . $data['search'] . '%');
            });
        }
        $listevents->where('tod_user_events.user_id', $userid);
//        $listevents->where('tod_user_events.is_track', 1);
        $listevents->orderBy('tod_extra_events.source', 'ASC');
        return $listevents->get();
    }

    public function listEventSame() {
        return $listeventssame = DB::select("SELECT * FROM tod_extra_events WHERE self_id != 'NULL'");
    }

    public static function getEventByTypeidCount($type_eventid, $type = "SH") {
        if ($type == "VS") {
            $source = "vividseats";
        } else if ($type == "SH") {
            $source = "stubhub";
        }
        return ExtraEventModel::where('type_eventid', $type_eventid)->where('source', $source)->count();
    }

    public static function getEventByTypeselfid($type_eventid, $type = "SH") {
        if ($type == "VS") {
            $source = "vividseats";
        } else if ($type == "SH") {
            $source = "stubhub";
        }
        return ExtraEventModel::where('self_id', $type_eventid)->where('source', $source)->first();
    }

    public static function getEventByTypeid($type_eventid, $type = "SH") {
        if ($type == "VS") {
            $source = "vividseats";
        } else if ($type == "SH") {
            $source = "stubhub";
        }
        return ExtraEventModel::where('type_eventid', $type_eventid)->where('source', $source)->first();
    }

    public static function getEventByidCount($type_id) {
        return ExtraEventModel::where('event_id', $type_id)->count();
    }

    public static function getEventByid($type_id) {
        $userid = Auth::User()->user_id;
        return ExtraEventModel::join('tod_user_events', 'tod_user_events.event_id', "=", "tod_extra_events.event_id")->where('tod_user_events.event_id', $type_id)->where('tod_user_events.user_id', $userid)->first();
    }

    public static function updateEventById($type_eventid, $data) {
        return ExtraEventModel::where('event_id', $type_eventid)->update($data);
    }

    public static function getEventByidDate($source = "stubhub") {
        $date = time();
        $listevents = ExtraEventModel::query()->select('*');
        $listevents->join('tod_user_events', 'tod_user_events.event_id', "=", "tod_extra_events.event_id");
        $listevents->where(function ($query) {
            $query->orWhere('tod_extra_events.last_checked', '<', (time() - 3 * 3600));
            $query->orWhere('tod_extra_events.last_checked', '=', NULL);
            $query->orWhere('tod_extra_events.last_checked', '=', '0');
        });
        $listevents->where('tod_user_events.is_track', 1)->where('tod_extra_events.eventDate', '>', $date);
        $listevents->where('tod_extra_events.source', $source);
        return $listevents->get();
    }
    public static function getEventBytpyeid($type_eventid) {
         return ExtraEventModel::select('event_id', 'source','type_eventid')->where('self_id', $type_eventid)->first();
    } 
    public static function getEventByEventmid($type_eventid) {
         return ExtraEventModel::select('type_eventid')->where('type_eventid', $type_eventid)->count();
    } 
     public static function updatetypeEventById($type_eventid, $data) {
        return ExtraEventModel::where('type_eventid', $type_eventid)->update($data);
    }

    public static function HideEventForuser($data) {
        $param=array();
         if(!empty($data))
         {

             DB::update("UPDATE tod_extra_events SET status = 1");    
             $param = array(
                    "status" => 0
                );
             foreach ($data['eventids'] as $key => $value) {
               ExtraEventModel::where('event_id', $value['id'])->update($param);     
}
             
         }
         else{
             
               DB::update("UPDATE tod_extra_events SET status = 1");
         }
         
          return true;
    }
    

}
