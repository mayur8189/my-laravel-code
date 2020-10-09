<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\RoleModel;
use App\Model\PermissionsModel;
use App\Model\RolesPermissionModel;
use DataTables;
use Validator;
use Auth;

class RoleController extends CommonController {

    public function __construct(Request $request) {
        
    }

    public function listRoles() {
        if( Auth::User()->can('view-user')){
            return view('pages.admin.role.list-roles');
        }else{
            abort(401);
        }
    }

    public function addRole($roleid = false) {
        if (Auth::User()->can('add-role')){
            $data = array();
            if ($roleid) {
                $role = RoleModel::getRole($roleid);
                if (!empty($role)) {
                    $data = $role;
                } else {
                    $msg = "Invalid Activity";
                    return redirect('list-roles')->with("error", $msg);
                }
            }
            return view('pages.admin.role.add-role', compact('data'));
        }else{
            abort(401);
        }
    }

    public function addRoleData($roleid = false, Request $request) {
        if ($roleid) {
            $rules = array(
                'name' => 'required|string|max:20|min:3|unique:tod_roles,name,' . $roleid . ',role_id'
            );
        } else {
            $rules = array(
                'name' => 'required|string|max:20|min:3|unique:tod_roles,name'
            );
        }

        // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);
        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            if ($roleid) {
                return redirect('edit-role/' . $roleid)
                                ->withErrors($validator)
                                ->withInput();
            } else {
                return redirect('add-role')
                                ->withErrors($validator)
                                ->withInput();
            }
        } else {
            if ($roleid) {
                $params = array(
                    'name' => ucwords($request->name),
                );
                $role = RoleModel::updateRole($params, $roleid);
                $msg = "Role has been successfully updated";
                return redirect('list-roles')->with("success", $msg);
            } else {
                $params = array(
                    'name' => ucwords($request->name),
                    'created_date' => date('Y-m-d H:i:s'),
                    'slug'=> strtolower(str_replace(" ", "-", $request->name))
                );
                $role = RoleModel::saveRole($params);
                // Add default permission for specific role
                $allPermission = PermissionsModel::getPermissions();  // get all default roles
                foreach( $allPermission as $permisson ){
                        $param = array(
                            'role_id' => $role,
                            'permission_id' => $permisson['id'],
                            'created_date' => date('Y-m-d H:i:s'),
                        );
                        RolesPermissionModel::saveRolePermission($param);
                }
                $msg = "New Role has been successfully added";
                if (!empty($role)) {
                    return redirect('list-roles')->with("success", $msg);
                } else {
                    $error = 'Something went wrong!';
                    return redirect('add-role')->with("error", $error);
                }
            }
        }
    }

    public function listRolesRecord(Request $request) {  
        $listrole = RoleModel::listRoles();
        return DataTables::of($listrole)
                        ->editColumn('created_date', function($data) {
                            return '<span>' . date('Y-m-d', strtotime($data->created_date)) . '</span>';
                        })
                        ->addColumn('actions', function ($data) {
                            $permission = "";
                            
                            $actions = 'No Access';
                             if (strtolower($data->name) !== "admin") {
                                    $actions = '<div class="btn-group">';
                                    $actions .='<a href="' . url('edit-permission/' . $data->role_id) . '" class="btn btn-sm btn-default mr-10 "><i class="fa fa-edit"> </i> Permission </a> ';
                                    if( Auth::User()->can('add-role')){
                                        $actions .=' <a href="' . url('edit-role/' . $data->role_id) . '" class="btn btn-sm btn-primary mr-10"><i class="fa fa-edit"> </i> Edit</a>';
                                    }
                                    if( Auth::User()->can('delete-role')){
                                        $actions .='    <a href="javascript:void(0)" onclick="deleteRole(this);" data-id="' . $data->role_id . '" class="btn btn-sm btn-danger"><i class="fa fa-trash"> </i> Delete</a>';
                                    }
                                    $actions .='</div>';
                            }	
                            return $actions;
                        })
                        ->rawColumns(['created_date', 'actions'])
                        ->make('true');
    }

    public function deleteRole(Request $request) {
        if (Auth::User()->can('delete-role')){
            $v_res = array();
            $v_res['status'] = false;
            $role_id = $request->role_id;
            $role = RoleModel::getRole($role_id);
            if (!empty($role)) {
                $role = RoleModel::deleteRole($role_id);
                if ($role) {
                    $v_res['status'] = true;
                }
            }
            echo json_encode($v_res, true);
            exit;
        }else{
            abort(401);
        }
    }
      
    public function editPermissionOfRole($roleid = false){
        
        $data = array();
        if ($roleid) {
            $allPermission = PermissionsModel::getPermissions();
           
            $rollPermission = RolesPermissionModel::getRolePermission($roleid);
//            echo "<pre>";
//             print_r($rollPermission);
//            echo "<pre>";
//            exit;
        }
        $data['role_id'] = $roleid;
        return view('pages.admin.permission.add-permission', compact('allPermission','rollPermission','data'));
        
    }
    
    public function editPermissionDataOfRole($roleid = false, Request $request){
        //delete permission total track
        RolesPermissionModel::deleteRolePermissionTotalTrack($roleid);
        //delete permission total search artist
        RolesPermissionModel::deleteRolePermissionTotalSearchArtist($roleid);
        //delete permission data
        RolesPermissionModel::deleteRolePermission($roleid);
        
        $allPermission = $request->all();
        if(isset($allPermission['rolePermission'])){
                foreach( $allPermission['rolePermission']  as $permisson ){
                    $param = array(
                        'role_id' => $roleid,
                        'permission_id' => $permisson,
                        'created_date' => date('Y-m-d H:i:s'),
                    );
                     
                    $lastid=RolesPermissionModel::saveRolePermission($param);
                    
                     if($permisson == 8)
                    {
                        $totaltrack = array(
                        
                        'role_per_id' => $lastid,
                        'total_track' => $allPermission['totalevent'],
                        'permission_id'=>$permisson,
                        'role_us_id'=>$roleid
                            
                            );
                        RolesPermissionModel::saveRoleTotalTrackPermission($totaltrack);
                    }
                    
                     if($permisson == 10)
                    {
                        $totaltrack = array(
                        
                        'role_per_id' => $lastid,
                        'total_searchartist' => $allPermission['totalsearch'],
                        'permission_id'=>$permisson,
                        'role_us_id'=>$roleid
                            
                            );
                        RolesPermissionModel::saveRoleTotalSearchArtistPermission($totaltrack);
                    }
            }

            $msg = "Permission has been updated successfully";
            return redirect('list-roles')->with("success", $msg);
        }else {
            $msg = "Please select atleast one permission";
            return redirect('list-roles');
        }
    }
  

}
