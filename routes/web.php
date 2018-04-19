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
    return ' 参数错误';
});
Route::any('wxpay/dopay', 'wxPayController@payOrder');
Route::any('wxpay/paycallback', 'wxPayController@payCallback');
Route::any('wxpay/paysuccess', 'wxPayController@paySuccess');
Route::any('api/getrec', 'RecController@getRec');
Route::any('api/getcls', 'YsyToolController@getcls');
Route::any('api/getsub', 'YsyToolController@getsub');
Route::any('api/getdai', 'YsyToolController@getdai');
Route::any('api/getpro', 'YsyToolController@getpro');

Route::any('share',function (){
   return view('ticket/share');
});



