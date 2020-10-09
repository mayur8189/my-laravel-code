<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class TotalSearchArtistPermissionModel extends Authenticatable {
 
    public static function CountTotalSearch($userid) {
         $role_id= DB::table('tod_users_roles')->where('user_id',$userid)->pluck('role_id');
         $result = DB::table('tod_permission_searchart')->where('role_us_id', $role_id)->pluck('total_searchartist');
         if(isset($result))
         {
             return $result;
             
         }  else {
             
             return  0;    
         }
    }
    
     public static function CountUserTotalSearch($userid) {
         $result = DB::table('tod_user_setpermission')->where('role_user_id', $userid)->where('role_permission_id', 10)->pluck('role_total');
         if(isset($result))
         {
             return $result;
             
         }  else {
             
             return  0;    
         }
    }
    
    public static function UpdateCountTotalSearch($userid,$pendcounter){
        DB::table('tod_user_setpermission')->where('role_user_id', $userid)->where('role_permission_id', 10)->update(['role_total' => $pendcounter]);
    }
    
    
}
