<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

Route::group(['middleware' => ['auth', 'prevent-back-history']], function () {
    /* Dashboard Controller */
    Route::get('/', 'DashboardController@index');
    Route::get('/dashboard', 'DashboardController@index');
    Route::post('/menuChanged', 'DashboardController@menuChanged')->name('logout');

    Route::get('/logout', 'LoginController@logout')->name('logout');

    /* User Controller */
    Route::get('/list-users', 'UserController@listUsers');
    Route::post('/list-users-record', 'UserController@listUsersRecord');
    Route::get('/add-user', 'UserController@addUser');
    Route::post('/add-user-data', 'UserController@addUserData');
    Route::get('/edit-user/{userid}', 'UserController@addUser');
    Route::post('/edit-user-data/{userid}', 'UserController@addUserData');
    Route::post('/delete-user', 'UserController@deleteUser');
    Route::post('/change-status', 'UserController@changeStatus');
    Route::post('/change-password', 'UserController@changePassword');

    /* Role Controller */
    Route::get('/list-roles', 'RoleController@listRoles');
    Route::post('/list-roles-record', 'RoleController@listRolesRecord');
    Route::get('/add-role', 'RoleController@addRole');
    Route::get('/edit-role/{roleid}', 'RoleController@addRole');
    Route::post('/add-role-data', 'RoleController@addRoleData');
    Route::post('/edit-role-data/{roleid}', 'RoleController@addRoleData');
    Route::post('/delete-role', 'RoleController@deleteRole');
    Route::get('/edit-permission/{role_id}', 'RoleController@editPermissionOfRole');
    Route::post('/edit-rolePermission-data/{role_id}', 'RoleController@editPermissionDataOfRole');

    /* Event Controller */

    Route::get('/list-events', 'EventController@listEvents');
    Route::post('/list-events-record', 'EventController@listEventsRecord');
    Route::get('/ticket-master-code-lookup', 'EventController@ticketMasterLookup');
    Route::post('/validateOfferCode', 'EventController@validateOfferCode');

    /* Extra Event */
    
    Route::get('/event-list', 'ExtraEventController@EventList');
    Route::get('/event-list-user/{roleid}', 'ExtraEventController@UserEventList');
    Route::post('/addEvent', 'ExtraEventController@addEvent');
    Route::post('/trackCheckedEvent', 'ExtraEventController@trackCheckedEvent');
    Route::post('/untrack-event', 'ExtraEventController@untrackEvent');
    Route::post('/manualTrackEvent', 'ExtraEventController@manualTrackEvent');
    Route::get('/event-track-detail/{eventid}', 'ExtraEventController@eventTrackDetail');
    Route::post('/mergeCheckedEvent', 'ExtraEventController@mergeCheckedEvent');
    Route::get('/test', 'ExtraEventController@getStubIdFromVivid');
    Route::get('/profile', 'UserController@user_profile');
    Route::post('/edit-profile', 'UserController@edit_profile');
    Route::get('/event-list-api', 'ExtraEventController@get_event_by_api');
    Route::post('/searchEvent', 'ExtraEventController@searchEvent');
    Route::post('/addBulkCheckedEvent', 'ExtraEventController@addBulkCheckedEvent');
    Route::post('/hideevent', 'ExtraEventController@HideEventCont');
     Route::post('/sales-list', 'ExtraEventController@getSalesData');
});
Route::get('/cron-ticketmaster-us', 'EventController@cronTickermasterUS');
Route::get('/cron-ticketmaster-ca', 'EventController@cronTickermasterCA');
Route::get('/cron-stubhub-track', 'ExtraEventController@cronStubhubTrack');
Route::get('/cron-vivid-track', 'ExtraEventController@cronVividTrack');
Route::get('/stubhubDataGet', 'ExtraEventController@stubhubDataGet'); 
Route::get('/testurl', 'EventController@testurl');  

Route::get('login', array('as' => 'login',
    'uses' => 'LoginController@index'))->middleware('guest');

Route::post('dologin', ['as' => 'dologin', 'uses' => 'LoginController@doLogin']);


Route::get('/register', array('as' => 'register',
    'uses' => 'RegistrationController@index'))->middleware('guest');  
Route::post('doregister', ['as' => 'doregister', 'uses' => 'RegistrationController@doRegister']);

Route::get('/forgot', array('as' => 'forgot',
    'uses' => 'ForgotController@index'))->middleware('guest');  
Route::post('doforget', ['as' => 'doforgot', 'uses' => 'ForgotController@doForgot']);


Route::get('/resetpassword/{tokenval}', array('as' => 'resetpassword/{tokenval}',
    'uses' => 'ResetpasswordController@index'))->middleware('guest');  
Route::post('doreset', ['as' => 'doreset', 'uses' => 'ResetpasswordController@doRestpassword']);

