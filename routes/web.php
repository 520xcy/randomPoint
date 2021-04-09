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

Route::get('/', function () {
    if(Auth::guard('random')->guest()){
        $sessionid = Session::getId();
        $user = \App\Models\User::firstOrCreate(['sessionid' => $sessionid]);
        $user_id = $user->uuid;
        Auth::guard('random')->login($user, true);
    }
    $user_id = Auth::guard('random')->id();

    

    return view('index', ['user_id' => $user_id]);
});
