<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use App\Model\UserModel;

class RegistrationController extends CommonController {

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
    public function index() {
        return view('pages.register');
    }
    
    
    public function doRegister(Request $request) {
            $rules = array(
                'firstname' => 'required|string|max:20|min:3',
                // 'lastname' => 'required|string|max:20|min:3',
                'username' => 'required|without_spaces|alphanum|max:20|min:3|unique:tod_users,username',
                'email' => 'required|email|max:50|min:3|unique:tod_users,email',
                'password' => 'required|min:6',
                'confirmpassword' => 'required|same:password',
            );

        // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);
        // if the validator fails, redirect back to the form
        
        if ($validator->fails()) {
           
                return redirect('register')
                                ->withErrors($validator)
                                ->withInput($request->except('password', 'confirmpassword'));
        } else {
            $params = array(
                "firstname" => $request->firstname,
               // "lastname" => $request->lastname,
                "status" => 0,
                "username" => $request->username,
                "email" => $request->email,
                "modified_date" => time(),
            );
            
                $params['password'] = $this->encrypt($request->password);
                $params['created_date'] = time();
               
                $userid = UserModel::saveUser($params);
                $msg = "Welcome to Ticket Broker Tools!";
          
            return redirect('login')->with("success", $msg);
        }
    }

    

}
