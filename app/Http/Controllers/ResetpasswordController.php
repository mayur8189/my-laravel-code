<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use App\Model\UserModel;
use App\Model\ResetPasswordTokenModel;

class ResetpasswordController extends CommonController {

    use AuthenticatesUsers;
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(Request $request) {
        Validator::extend('without_spaces', function($attr, $value) {
            return preg_match('/^\S*$/u', $value);
        });
    }

    public function index($tokenval) {
         if(empty($tokenval))
        {
            return redirect('login');
        }
        $data['newtoken']=$tokenval;
        return view('pages.resetpassword',compact('data'));
    }

    public function doRestpassword(Request $request) {
       
        $rules = array(
            'newpassword' => 'required|min:6',
            'confirmpassword' => 'required|same:newpassword',
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);
        // if the validator fails, redirect back to the form

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput($request->except('newpass', 'confirmpass'));
        } else {

            // Validate the token
            $tokenData =  $gettoken = ResetPasswordTokenModel::getToken($request->newtoken);
         
            // Redirect the user back to the password reset request form if the token is invalid
            if (!$tokenData)
                     return redirect()->back()->with("error", "Something with wrong");

            
            $user = UserModel::where(['email' => $tokenData->reset_email])->first();
            // Redirect the user back if the email is invalid
            
            if ($user) {
                //update new password
                $params['password'] = $this->encrypt($request->newpassword);
                UserModel::updateUser($params, $user->user_id);
                //remove old token 
                ResetPasswordTokenModel::deleteResetToken($user->email);
                $msg = "Your Password successfully Reset.";
                return redirect('login')->with("success", $msg);
            } else {
              return redirect()->back()->with("error", "Email is invalid");
            }
        }
    }

}
