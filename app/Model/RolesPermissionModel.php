<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class RolesPermissionModel extends Authenticatable {

    public $table = 'tod_roles_permissions';
    public $primaryKey = 'id';
    public $timestamps = false;

    public static function saveRolePermission($data) {
        return  RolesPermissionModel::insertGetId($data);   
    }
    
    public static function saveRoleTotalTrackPermission($data) {
        return DB::table('tod_permission_totaltrack')->insert($data);
    }
    
     public static function saveRoleTotalSearchArtistPermission($data) {
        return DB::table('tod_permission_searchart')->insert($data);
    }
    
    public static function getRolePermission($roleid) {
        //return RolesPermissionModel::where('role_id', $roleid)->get();
//        return  RolesPermissionModel::leftjoin('tod_permission_totaltrack', function ($join) use ($roleid) {
//            $join->on('tod_roles_permissions.id', '=', 'tod_permission_totaltrack.role_per_id');
//            
//           })->where('tod_roles_permissions.role_id',$roleid)
//        ->get();
        
        return DB::select('SELECT tod_roles_permissions.*,tod_permission_totaltrack.total_track,tod_permission_searchart.total_searchartist FROM tod_roles_permissions LEFT JOIN tod_permission_totaltrack ON tod_roles_permissions.id=tod_permission_totaltrack.role_per_id LEFT JOIN tod_permission_searchart ON tod_roles_permissions.id=tod_permission_searchart.role_per_id WHERE tod_roles_permissions.role_id='.$roleid);
    }
    
   
    public static function deleteRolePermission($roleid=0) {
        return RolesPermissionModel::where('role_id', $roleid)->delete();
    }
    
    public static function deleteRolePermissionTotalTrack($roleid=0) {
        return DB::table('tod_permission_totaltrack')->where('role_us_id', $roleid)->delete();
    }
    
    public static function deleteRolePermissionTotalSearchArtist($roleid=0) {
        return DB::table('tod_permission_searchart')->where('role_us_id', $roleid)->delete();
    }
    
    
    //get role permission for Chrome extension
    public static function getRolePermissionUser($userid,$setPer) {
        $result= DB::table('tod_users_roles')->where('user_id',$userid)->first();
        if(!empty($result))
          {
              if($result->role_id == 1)
              {
                  return 1;
              }
              else
              {
               return  $result2 = DB::table('tod_roles_permissions')->where('role_id',$result->role_id)->where('permission_id',$setPer)->count();    
                 
              }
          }
          else
          {
              return 0;
          }
        
    }
    
     //get role_id for Chrome extension
    public static function getRoleIdForUser($userid) {
        $result= DB::table('tod_users_roles')->where('user_id',$userid)->first();
        if(!empty($result))
          {
              if($result->role_id == 1)
              {
                  return 1;
              }
              else{
                  return 0;
              }
          }
          else
          {
              return 0;
          }
        
    }
    
    
}
