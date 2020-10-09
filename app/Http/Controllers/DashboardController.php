<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\UserModel;


class DashboardController extends CommonController {
 
    public function index(Request $request) { 
        return view('pages.admin.dashboard');
    }

    public function menuChanged(Request $request) {
        $userid = Auth::User()->user_id;
        $param = array(
            'menu_status' => $request->menuactive
        );
        UserModel::updateUser($param, $userid);
    }

}
