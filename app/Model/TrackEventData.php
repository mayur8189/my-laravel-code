<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TrackEventData extends Authenticatable {

    public $table = 'tod_track_event_data';
    public $primaryKey = 'id';
    public $timestamps = false;

    public static function trackData($data) {
        return TrackEventData::insert($data);
    }

    public static function gettrackDataById($event_id) {
        return TrackEventData::where('event_id', $event_id)->select('*')->get();
    }

}
