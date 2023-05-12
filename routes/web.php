<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

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
Auth::routes();

/** Default Loading to the Login Page */
Route::redirect('/', 'login', 301);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

//postman api
Route::get('v2/payments/authorizations', 'App\Http\Controllers\PaymentController@userdetails');



Route::get('payment', 'App\Http\Controllers\PaymentController@index');
Route::get('curlerror', 'App\Http\Controllers\PaymentController@curlerror');
Route::get('success_url', 'App\Http\Controllers\PaymentController@success_url');

Route::post('charge', 'App\Http\Controllers\PaymentController@charge');
Route::get('success', 'App\Http\Controllers\PaymentController@success');
Route::post('curltesting', 'App\Http\Controllers\PaymentController@curltest');
Route::get('error', 'App\Http\Controllers\PaymentController@error');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
