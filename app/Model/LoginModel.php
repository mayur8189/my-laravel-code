<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class LoginModel extends Authenticatable {

    public $table = 'tod_login';
    public $primaryKey = 'id';
    public $timestamps = false;

    public static function saveLogin($data) {
        return LoginModel::insert($data);
    }

    public static function checkLogin($userid) {
        return LoginModel::where('user_id', $userid)->count();
    }

    public static function getLogin($userid) {
        return LoginModel::where('user_id', $userid)->select('logout_time')->first();
    }

    public static function updateLogin($data, $userid) {
        return LoginModel::where('user_id', $userid)->update($data);
    }

}
