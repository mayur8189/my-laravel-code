<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserEventModel extends Authenticatable {

    public $table = 'tod_user_events';
    public $primaryKey = 'ue_id';
    public $timestamps = false;

    public static function saveUserEvent($data) {
        return UserEventModel::insertGetId($data);
    }
 

    public static function getUserEventByIdCount($userid,$eventid) { 
        return UserEventModel::where('event_id', $eventid)->where('user_id', $userid)->count();
    } 

    public static function getEventByid($type_id) {
        return UserEventModel::where('event_id', $type_id)->first();
    }

    public static function updateEventById($ueid, $userid,$data) {
        return UserEventModel::where('event_id', $ueid)->where('user_id', $userid)->update($data);
    }
 
}
