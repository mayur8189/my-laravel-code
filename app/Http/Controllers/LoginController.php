<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use App\Model\UserModel;
use App\Model\LoginModel;

class LoginController extends CommonController {

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
    public function index() {
        return view('pages.login');
    }

    public function doLogin(Request $request) { 
        $rules = array(
            'username' => 'required', // make sure the email is an actual email
            'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
        );
        // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return redirect('login')
                            ->withErrors($validator) // send back all errors to the login form
                            ->withInput($request->except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {
            $user = UserModel::where([
                        'username' => $request->input('username'),
                        'password' => $this->encrypt($request->input('password')),
                    ])->first();

            if ($user) {
                if ($user->status == 1) {
//                    if ($user->role_id == 1) {
                        Auth::login($user, false);
                        return redirect()->intended('dashboard');
//                    } else {
//                        $error = "Only Admin can login";
//                        return redirect('login')->with("error", $error);
//                    }
                } else {
                    $error = "Your Account is inactive.";
                    return redirect('login')->with("error", $error);
                }
            } else {
                $error = Lang::get('auth.failed');
                return redirect('login')->with("error", $error);
            }
        }
    }

    public function logout() {
        $userid = Auth::User()->user_id;
        $isLogin = LoginModel::checkLogin($userid);
        if ($isLogin > 0) {
            $params = array(
                "logout_time" => time()
            );
            $isLogin = LoginModel::updateLogin($params, $userid);
        } else {
            $params = array(
                "user_id" => $userid,
                "logout_time" => time()
            );
            $isLogin = LoginModel::saveLogin($params);
        }
        Auth::logout();
        return redirect('login');
    }

}
