<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PermissionsModel extends Authenticatable {

    public $table = 'tod_permissions';
    public $primaryKey = 'id';
    public $timestamps = false;

    public function roles() {
        return $this->belongsToMany(\App\Model\RoleModel::class, 'tod_roles_permissions','permission_id', 'role_id');
    }
    
    public static function getPermissions() {
        return PermissionsModel::get();
    }
}
