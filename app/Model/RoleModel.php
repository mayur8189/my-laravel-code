<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class RoleModel extends Authenticatable {

    public $table = 'tod_roles';
    public $primaryKey = 'role_id';
    public $timestamps = false;

    public static function saveRole($data) {
        return RoleModel::insertGetId($data);
    }

    public static function updateRole($data, $role_id) {
        return RoleModel::where('role_id', $role_id)->update($data);
    }

    public static function getRole($roleid) {
        return RoleModel::where('role_id', $roleid)->first();
    }

    public static function getRoles() {
        return RoleModel::get();
    }

    public static function roleCount() {
        return RoleModel::count();
    }

    public static function listRoles() {
        return RoleModel::select('*');
    }

    public static function deleteRole($roleid) {
        return RoleModel::where('role_id', $roleid)->delete();
    }
    public static function roleBelong() {
        return RoleModel::belongsTo(\App\Model\UserModel::class,'tod_users_roles');
    }
    
//    public function permissions() {  
//        return $this->belongsToMany(\App\Model\PermissionsModel::class, 'tod_roles_permissions');
//    }

}
