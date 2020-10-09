<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use App\Model\ResetPasswordTokenModel;
use App\Model\UserModel;
use Mail;

class ForgotController extends CommonController {

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
        return view('pages.forgot');
    }

    public function doForgot(Request $request) {
        $emailid = $request->email;
        $rules = array(
            'email' => 'required|email|max:50|min:3'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);
        // if the validator fails, redirect back to the form

        if ($validator->fails()) {

            return redirect('forgot')
                            ->withErrors($validator)
                            ->withInput($request->except('email'));
        } else {
            $user = UserModel::where(['email' => $emailid])->first();
            if ($user) {
                // Create Password Reset Token
                $param = array(
                    'reset_email' => $emailid,
                    'reset_token' => str_random(60),
                    'reset_created_at' => time()
                );
                //remove old token 
                ResetPasswordTokenModel::deleteResetToken($emailid);

                //add new token
                ResetPasswordTokenModel::saveResetToken($param);

                //get token for send user
                $gettoken = ResetPasswordTokenModel::getResetToken($emailid);


                if ($this->sendResetEmail($emailid, $gettoken->reset_token)) {
                    $msg = "A reset link has been sent to your email address.";
                    $seterror="success";
                } else {
                    $msg = "A Network Error occurred. Please try again.";
                    $seterror="error";
                    
                }
                return redirect('forgot')->with($seterror, $msg);
            } else {
                $msg = "Your EmailId Doest Exist.";
                return redirect('forgot')->with($seterror, $msg);
            }
        }
    }

    private function sendResetEmail($email, $token) {
        //Retrieve the user from the database
        $user = UserModel::where(['email' => $email])->first();
        //Generate, the password reset link. The token generated is embedded in the link
        $link = url('/') . '/resetpassword/' . $token . '?email=' . urlencode($user->email);
        try {
            //send mail of user for reset password link
                    $data = array('link'=>$link);
                    $infouser=array('emailid'=>$user->email,'name'=>$user->firstname);
                 Mail::send('pages.mail', $data, function($message) use ($infouser){
                $message->to('mayur.8189@gmail.com', 'Ticket Broker Tools')->subject
                   ('Reset Password For Ticket Broker Tools');
                $message->from($infouser['emailid'],$infouser['name']);
             });
         
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}
