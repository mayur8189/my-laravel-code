<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ShkeysModel extends Authenticatable {

    public $table = 'sh_keys';
    public $primaryKey = 'id';
    public $timestamps = false;

    public static function getAllSHKeys($status, $isNULL = false) {
        $getkey = ShkeysModel::query()->select('*');
        if ($isNULL) {
            $getkey->where(function ($query) {
                $query->where('blockdate', NULL);
                $query->orWhere('blockdate', 0);
            });
        }
        $getkey->where('status', $status);
        if ($status == 1) {
            return $getkey->first();
        } else {
            return $getkey->get();
        }
    } 

    public static function updateSHKey($params, $id) {
        return ShkeysModel::where('id', $id)->update($params);
    }

}
