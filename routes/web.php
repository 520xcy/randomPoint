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
    $sessionid = Session::getId();
    $user = \App\Models\User::firstOrCreate(['sessionid' => $sessionid]);

    return view('index', ['user_id' => $user->uuid]);
});
