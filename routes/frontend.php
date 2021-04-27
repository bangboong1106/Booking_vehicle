<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "frontend" middleware group. Now create something great!
|
*/
//------------------------------------------------------------------------------------

//Route::post('auth/login', 'Auth\LoginController@login');
//Route::group(['middleware' => 'fjwt.auth'], function () {
//    Route::get('auth/user', 'LoginController@user');
//
//    Route::get('event', 'OrderApiController@event');
//    Route::get('order', 'OrderApiController@order');
//    Route::get('detail', 'OrderApiController@detail');
//});
//Route::group(['middleware' => 'fjwt.refresh'], function () {
//    Route::get('auth/refresh', 'LoginController@refresh');
//});


Route::any('/', [
    'as' => 'home',
    'uses' => 'HomeController@main'
]);
Route::get('/main', [
    'as' => 'home.main',
    'uses' => 'HomeController@index'
]);
Route::any('/about', [
    'as' => 'about',
    'uses' => 'HomeController@about'
]);
Route::any('/support', [
    'as' => 'support',
    'uses' => 'HomeController@support'
]);
Route::any('/covid', [
    'as' => 'covid',
    'uses' => 'HomeController@covid'
]);
Route::any('/policy', [
    'as' => 'policy',
    'uses' => 'HomeController@policy'
]);
Route::get('/event', [
    'as' => 'home.event',
    'uses' => 'HomeController@event'
]);
Route::get('/order', [
    'as' => 'home.order',
    'uses' => 'HomeController@order'
]);
Route::get('/detail', [
    'as' => 'home.detail',
    'uses' => 'HomeController@detail'
]);
Route::get('/customer', [
    'as' => 'customer.index',
    'uses' => 'CustomerController@index'
]);
Route::get('/logs', '\App\Http\Supports\LogViewerController@index')->middleware('log.viewer')->name('frontend.log.viewer');
Route::get('/switch-lang/{lang}', [
    'as' => 'frontend.switch_lang',
    'uses' => 'HomeController@switchLang'
]);
authRoutes('frontend');