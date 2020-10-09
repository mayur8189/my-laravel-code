<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Permissions\HasPermissionsTrait;
use Laravel\Passport\HasApiTokens;

class UserModel extends Authenticatable {

    use HasPermissionsTrait,HasApiTokens;
    public $table = 'tod_users';
    public $primaryKey = 'user_id';
    public $timestamps = false;

    public static function saveuser($params) {
        return UserModel::insertGetId($params);
    }

    public static function updateUser($params, $userid) {
        return UserModel::where('user_id', $userid)->update($params);
    }

    public static function getUser($userid) {
        $user = UserModel::leftJoin('tod_users_roles', 'tod_users_roles.user_id', "=", "tod_users.user_id")->leftJoin('tod_roles', 'tod_roles.role_id', "=", "tod_users_roles.role_id")->select('tod_users.*','tod_roles.name','tod_roles.role_id')->where([
                    'tod_users.user_id' => $userid,
                ])->first();
        return $user;
    }

    public static function listUser() {
        $users = UserModel::leftJoin('tod_users_roles', 'tod_users_roles.user_id', "=", "tod_users.user_id")->leftJoin('tod_roles', 'tod_roles.role_id', "=", "tod_users_roles.role_id")->select('tod_users.*','tod_roles.name','tod_roles.role_id');
        return $users;
    }

    public static function userCount() {
        $users = UserModel::count();
        return $users;
    }

    public static function deleteUser($userid) {
        return UserModel::where('user_id', $userid)->delete();
    }

}
