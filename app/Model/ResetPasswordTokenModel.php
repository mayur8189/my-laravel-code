<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ResetPasswordTokenModel extends Authenticatable {

    public $table = 'tod_reset_password_token';
    public $primaryKey = 'reset_id';
    public $timestamps = false;

    public static function saveResetToken($data) {
        return ResetPasswordTokenModel::insert($data);
    }

    public static function getResetToken($emailid) {
        return ResetPasswordTokenModel::where('reset_email', $emailid)->first();
    }

    public static function getToken($token) {
        return ResetPasswordTokenModel::where('reset_token', $token)->first();
    }
    
    public static function deleteResetToken($emailid) {
        return ResetPasswordTokenModel::where('reset_email', $emailid)->delete();
    }

}
