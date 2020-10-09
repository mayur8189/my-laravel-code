<?php

namespace App\Permissions;

use App\Model\PermissionsModel;
use App\Model\RoleModel;

trait HasPermissionsTrait {

    public function givePermissionsTo(... $permissions) {
        $permissions = $this->getAllPermissions($permissions);
        if ($permissions === null) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }
    public function givePermissionsToRole(... $permissions) {
        $permissions = $this->getAllPermissionsRole($permissions); 
        if ($permissions === null) {
            return $this;
        }
        $this->roles()->saveMany($permissions);
        return $this;
    }

    public function withdrawPermissionsTo(... $permissions) {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }

    public function refreshPermissions(... $permissions) {
        $this->permissions()->detach();
        return $this->givePermissionsTo($permissions);
    }

    public function hasPermissionTo($permission) { 
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }

    public function hasPermissionThroughRole($permission) {
        foreach ($permission->roles as $role) { 
            if ($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    public function hasRole(... $roles) {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    public function roles() {
        return $this->belongsToMany(RoleModel::class, 'tod_users_roles','user_id', 'role_id');
    }

    public function permissions() {
        return $this->belongsToMany(PermissionsModel::class, 'tod_users_permissions', 'permission_id', 'user_id');
    }

    protected function hasPermission($permission) {
        return (bool) $this->permissions->where('slug', $permission->slug)->count();
    }

    protected function getAllPermissions(array $permissions) {
        return PermissionsModel::whereIn('slug', $permissions)->get();
    }
    protected function getAllPermissionsRole(array $permissions) {
        return RoleModel::whereIn('slug', $permissions)->get();
    }

}
