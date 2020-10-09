<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class UserSetPermissionModel extends Authenticatable {

    public static function saveRoleSetUserPermission($data) {
        return  DB::table('tod_user_setpermission')->insert($data);   
    }
    
    public static function getRoleSerachValue($role_id) {
         $result = DB::table('tod_permission_searchart')->where('role_us_id', $role_id)->first();
         if(isset($result) && !empty($result))
         {
             return $result;
             
         }  else {
             
             return  0;    
         }
    }
    
    
     public static function getRoleTrackEvent($role_id) {
         $result = DB::table('tod_permission_totaltrack')->where('role_us_id', $role_id)->first();
         if(isset($result) && !empty($result))
         {
             return $result;
             
         }  else {
             
             return  0;    
         }
    }
    
    public static function DeleteUserSetpermission($User_id) {
        DB::table('tod_user_setpermission')->where('role_user_id', $User_id)->delete();    
    }
    
    
     
}
