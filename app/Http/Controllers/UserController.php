<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\UserModel;
use App\Model\RoleModel;
use App\Model\UserRoleModel;
use App\Model\UserSetPermissionModel;
use DataTables;
use Validator;
use Auth;

class UserController extends CommonController {

    public function __construct(Request $request) {
        Validator::extend('without_spaces', function($attr, $value) {
            return preg_match('/^\S*$/u', $value);
        });
        Validator::extend('password_validation', function($attr, $value) {
            return preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/u', $value);
        });
    }

    public function listUsers() {
        if (Auth::User()->can('view-user')) {
            return view('pages.admin.user.list-users');
        } else {
            abort(401);
        }
    }

    public function addUser($userid = false) {
        if (Auth::User()->can('add-user')) {
            $roles = RoleModel::getRoles();

            $data = array();
            if ($userid) {
                $user = UserModel::getUser($userid);
                if (!empty($user)) {
                    $data = $user;
                } else {
                    $msg = "Invalid Activity";
                    return redirect('list-users')->with("error", $msg);
                }
            }
            return view('pages/admin/user/add-user', compact('data', 'roles'));
        } else {
            abort(401);
        }
    }

    public function addUserData($userid = false, Request $request) {
        if ($userid) {
            $rules = array(
                'firstname' => 'required|string|max:20|min:3',
//                'lastname' => 'required|string|max:20|min:3',
                'status' => 'required|numeric',
                'role_id' => 'required|numeric',
                'username' => 'required|without_spaces|alphanum|max:20|min:3|unique:tod_users,username,' . $userid . ',user_id',
                'email' => 'required|email|max:50|min:3|unique:tod_users,email,' . $userid . ',user_id',
            );
        } else {
            $rules = array(
                'firstname' => 'required|string|max:20|min:3',
//                'lastname' => 'required|string|max:20|min:3',
                'status' => 'required|numeric',
                'role_id' => 'required|numeric',
                'username' => 'required|without_spaces|alphanum|max:20|min:3|unique:tod_users,username',
                'email' => 'required|email|max:50|min:3|unique:tod_users,email',
                'password' => 'required|password_validation',
                'confirmpassword' => 'required|same:password',
            );
        }

        // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);
        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            if ($userid) {
                return redirect('edit-user/' . $userid)
                                ->withErrors($validator)
                                ->withInput();
            } else {
                return redirect('add-user')
                                ->withErrors($validator)
                                ->withInput($request->except('password', 'confirmpassword'));
            }
        } else {
            $params = array(
                "firstname" => $request->firstname,
//                "lastname" => $request->lastname,
                "status" => $request->status,
                "username" => $request->username,
                "email" => $request->email,
                "modified_date" => time(),
            );
            if ($userid) {
                $user = UserModel::updateUser($params, $userid);
                $msg = "User has been successfully updated";
            } else {
                $params['password'] = $this->encrypt($request->password);
                $params['created_date'] = time();
                $userid = UserModel::saveUser($params);
                $msg = "New User has been successfully added";
                
                
            }
            $isdata = UserRoleModel::getRole($userid);
            if ($isdata > 0) {
                $params = array(
                    "role_id" => $request->role_id
                );
                UserRoleModel::updateRole($params, $userid);
                UserSetPermissionModel::DeleteUserSetpermission($userid);
                $Searchval=UserSetPermissionModel::getRoleSerachValue($request->role_id);
                if(!empty($Searchval))
                {
                $dataSearch=array(
                    
                    "role_set_id" => $request->role_id,
                    "role_user_id" => $userid,
                    "role_total" =>$Searchval->total_searchartist,
                    "role_permission_id"=>$Searchval->permission_id
                );
                UserSetPermissionModel::saveRoleSetUserPermission($dataSearch);
                }
                
                $trackval=UserSetPermissionModel::getRoleTrackEvent($request->role_id);
                if(!empty($trackval)){
                $datatrack=array(
                    
                    "role_set_id" => $request->role_id,
                    "role_user_id" => $userid,
                    "role_total" =>$trackval->total_track,
                    "role_permission_id"=>$trackval->permission_id
                );
               UserSetPermissionModel::saveRoleSetUserPermission($datatrack);
                }
                
                
            } 
            else {
                $params = array(
                    "user_id" => $userid,
                    "role_id" => $request->role_id
                );
                
                UserRoleModel::saveRole($params);
                //add track event and serach artist
                $Searchval=UserSetPermissionModel::getRoleSerachValue($request->role_id);
                if(!empty($Searchval))
                {
                $dataSearch=array(
                    
                    "role_set_id" => $request->role_id,
                    "role_user_id" => $userid,
                    "role_total" =>$Searchval->total_searchartist,
                    "role_permission_id"=>$Searchval->permission_id
                );
                UserSetPermissionModel::saveRoleSetUserPermission($dataSearch);
                }
                
                $trackval=UserSetPermissionModel::getRoleTrackEvent($request->role_id);
                if(!empty($trackval)){
                $datatrack=array(
                    
                    "role_set_id" => $request->role_id,
                    "role_user_id" => $userid,
                    "role_total" =>$trackval->total_track,
                    "role_permission_id"=>$trackval->permission_id
                );
               UserSetPermissionModel::saveRoleSetUserPermission($datatrack);
                } 
            }
            return redirect('list-users')->with("success", $msg);
        }
    }

    public function listUsersRecord(Request $request) {
        if (Auth::User()->can('view-user')) {
            $listuser = UserModel::listuser();
            return DataTables::of($listuser)
                            ->editColumn('status', function($data) {
                                $staushtml = "<select class='form-control changestatus' data-id='" . $this->encrypt($data->user_id) . "'>
                                            <option value='1' " . (($data->status == 1) ? 'selected' : '') . ">Active</option>
                                            <option value='0' " . (($data->status == 0) ? 'selected' : '') . ">Inactive</option> 
                                     </select>";
                                return '<span>' . $staushtml . '</span>';
                            })
                            ->editColumn('name', function($data) {
                                if (empty($data->name)) {
                                    return '<span>N/A</span>';
                                }
                                return '<span>' . $data->name . '</span>';
                            })
                            ->editColumn('created_date', function($data) {

                                return '<span>' . date('Y-m-d', $data->created_date) . '</span>';
                            })
                            ->addColumn('actions', function ($data) {
                                $actions = '<div class="btn-group">';
                                if (Auth::User()->can('add-user')) {
                                    $actions .= '<a href="' . url('edit-user/' . $data->user_id) . '" class="btn btn-sm btn-primary mr-10"><i class="fa fa-edit"> </i> Edit</a>';
                                }
                                if ($data->role_id != '1') {
                                    if (Auth::User()->can('delete-user')) {
                                        $actions .= '<a href="javascript:void(0)" onclick="deleteUser(this);" data-id="' . $data->user_id . '" class="btn btn-sm btn-danger mr-10"><i class="fa fa-trash"> </i> Delete</a></div>';
                                    }
                                }
                                
                                  if ($data->role_id != '1') {
                                    if (Auth::User()->hasRole('admin')) {
                                       
                                      $actions .= '<a href="' . url('event-list-user/' . $data->user_id) . '"  class="btn btn-sm btn-info mr-10"><i class="fa fa-list"> </i> Events</a></div>';   
                                    }
                                 }
                                $actions .= '</div>';
                                return $actions;
                            })
                            ->rawColumns(['status', 'name', 'created_date', 'actions'])
                            ->make('true');
        } else {
            abort(401);
        }
    }

    public function deleteUser(Request $request) {
        if (Auth::User()->can('delete-user')) {
            $v_res = array();
            $v_res['status'] = false;
            $user_id = $request->user_id;
            $role = UserModel::getUser($user_id);
            if (!empty($role)) {
                $role = UserModel::deleteUser($user_id);
                if ($role) {
                    $v_res['status'] = true;
                }
            }
            echo json_encode($v_res, true);
            exit;
        } else {
            abort(401);
        }
    }

    public function changeStatus(Request $request) {
        $v_res = array();
        $data = $request->all();
        $rules = array(
            'value' => 'required',
            'user_id' => 'required',
        );
        $userid = $this->decrypt($request->user_id);
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $errors = $validator->messages();
            $error_data = "";
            if ($errors->any()) {
                $error_data = "<ul>";
                foreach ($errors->all() as $error) {
                    $error_data .= "<li>" . $error . "</li>";
                }
                $error_data .= "</ul>";
            }
            $v_res['data'] = $error_data;
            $v_res['status'] = false;
        } else {
            $status = $request->input('value');
            $params = array(
                "status" => $status,
            );
            UserModel::updateUser($params, $userid);
            $v_res['status'] = true;
        }
        echo json_encode($v_res, true);
        die;
    }

    public function user_profile() {
        $userid = Auth::User()->user_id;
        $data = array();
        if ($userid) {
            $user = UserModel::getUser($userid);
            if (!empty($user)) {
                $data = $user;
            }
        }
        return view('pages.admin.user.user-profile', compact('data'));
    }

    public function edit_profile(Request $request) {
        //print_r($request->all());
        $userid = Auth::User()->user_id;
        $rules = array(
            'applicationtoken' => 'required',
            'apitoken' => 'required',
            'account' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {

            return redirect('profile')
                            ->withErrors($validator);
        } else {
            $params = array(
                "application_token" => $request->applicationtoken,
                "api_token" => $request->apitoken,
                "account" => $request->account,
            );
            UserModel::updateUser($params, $userid);
        }
        return redirect('/profile');
    }
    
    public function changePassword(Request $request)
    {
        $userid = Auth::User()->user_id;
        $rules = array(
            'oldpass' => 'required',
            'newpass' => 'required|min:6',
            'confirmpass' => 'required|same:newpass',
        );
        
         // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);
        // if the validator fails, redirect back to the form
        
        if ($validator->fails()) {
           
                return redirect('dashboard')
                                ->withErrors($validator)
                                ->withInput($request->except('newpass', 'confirmpass'));
        } else {
                 $user = UserModel::where([
                        'user_id' => $userid,
                        'password' => $this->encrypt($request->oldpass)])->first();
               if($user){
                   $params['password'] = $this->encrypt($request->newpass);
                   UserModel::updateUser($params,$userid);
                   $msg = "Your Password successfully Change.";
                   return redirect('dashboard')->with("success", $msg);
               }
               else{
                   $msg = "Your Current Password Doest Match.";
                   return redirect('dashboard')->with("error", $msg);
               }
                
          
            
        }
    }

}
