<?php

use Illuminate\Support\Facades\Route;

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



/****************註冊、登入*****************/

Route::get('/signin', function (){
    return view('login');
})->name('login');

Route::post('/signin ','SignInController@checkLogin');
Route::post('/signup','SignInController@register');
Route::get('/logout','SignInController@logout');//登出


//ECPAY接收位置
Route::post('/callback', 'ECPayController@callback');
Route::post('/ecpay/result', 'ECPayController@result');

//接收回傳結果
Route::get('/checkout_ecpay_status','PaymentsController@ecpayOrderStatus');

Route::group(['middleware'=>['auth:pay_account']], function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/check_out', function () {
        return view('pay');
    });

    Route::post('/checkout/order','PaymentsController@checkout_order');
  
  
});


/****************註冊、登入*****************/