<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class EventModel extends Authenticatable {

    public $table = 'tod_events';
    public $primaryKey = 'event_id';
    public $timestamps = false;

    public static function saveEvent($data) {
        return EventModel::insert($data);
    }

    public function listEvent($data) {
        $listevents = EventModel::select('*');
        if (isset($data['dateselect']) && !empty($data['dateselect'])) { 
            $listevents->orWhere(function ($query) use ($data){  
                $dateexplod = explode("-", $data['dateselect']);
                $query->whereBetween('onsaledate', [date('Y-m-d', strtotime($dateexplod[0])), date('Y-m-d', strtotime($dateexplod[1] . " +1 days"))]);
                $query->orWhereBetween('eventdate', [date('Y-m-d', strtotime($dateexplod[0])), date('Y-m-d', strtotime($dateexplod[1] . " +1 days"))]);
            });
        } 
        return $listevents;
    }

    public static function getEventByTypeidCount($type_eventid) {
        return EventModel::where('type_eventid', $type_eventid)->count();
    }

}
