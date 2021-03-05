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

Route::get('/', function () {
    return view('welcome');
});

/****************註冊、登入*****************/

Route::get('/signin', function (){
    return view('login');
});

Route::post('/signin ','SignInController@checkLogin');
Route::post('/signup','SignInController@register');


/****************註冊、登入*****************/