<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class TotalTrackPermissionModel extends Authenticatable {
 
    public static function CountTotalTrack($userid) {
         $role_id= DB::table('tod_users_roles')->where('user_id',$userid)->pluck('role_id');
         $result = DB::table('tod_permission_totaltrack')->where('role_us_id', $role_id)->pluck('total_track');
         if(isset($result))
         {
             return $result;
             
         }  else {
             
             return  0;    
         }
    }
    
    public static function UpdateCountTotalTrack($userid,$eventcounter){
        $role_id= DB::table('tod_users_roles')->where('user_id',$userid)->pluck('role_id');
        $result = DB::table('tod_permission_totaltrack')->where('role_us_id', $role_id)->first();
        $oldcounter=$result->total_track;
        $talattrack= $oldcounter - $eventcounter;
        DB::table('tod_permission_totaltrack')->where('track_total_id', $result->track_total_id)->update(['total_track' => $talattrack]);
    }
    
}
