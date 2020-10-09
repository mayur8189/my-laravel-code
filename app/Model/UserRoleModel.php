<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserRoleModel extends Authenticatable {

    public $table = 'tod_users_roles';
    public $primaryKey = 'id';
    public $timestamps = false;

    public static function saveRole($params) {
        return UserRoleModel::insert($params);
    }

    public static function updateRole($params, $userid) {
        return UserRoleModel::where('user_id', $userid)->update($params);
    }

    public static function getRole($userid) {
        return UserRoleModel::where('user_id', $userid)->count();
    }

}
