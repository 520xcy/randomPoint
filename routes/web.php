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

Route::get('pcloginqr', 'LoginController@pcLoginQR')->name('pcloginqr');
Route::put('pcloginCheck','LoginController@pcloginCheck')->name('pclogincheck');
Route::get('logout', 'LoginController@logout')->name('logout');
Route::group(['middleware' => ['wechat.oauth']], function () {
    Route::get('login', 'LoginController@login')->name('login');
    Route::get('pclogin', 'LoginController@pcLogin')->name('pclogin');
});
Route::group(['middleware' => ['logincheck:random']], function () {
    Route::get('/', 'CommonController@point')->name('index');
    Route::post('update', 'LoginController@update')->name('api/update');
});
