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

Route::get('/tour', "HomeController@index");
Route::get('/ar/{stage}/{locale}/{simulateLocation?}', "HomeController@ar");
Route::get('/home', 'HomeController@index')->name('home');



Route::group(['prefix'=>'creator', 'middleware' => ['auth']], function () {
    Route::get('/', "TourEditController@index");
    Route::resource("edit", "TourEditController")->parameters([
    'edit' => 'tour']);

    Route::post('/image/store', 'ImageController@store');
    Route::post("/edit/{tour}/stop", "TourEditController@createStop");
    Route::put("/edit/{tour}/stop/{stop}", "TourEditController@updateStop");
    Route::delete("/edit/{tour}/stop/{stop}", "TourEditController@deleteStop");

    Route::any('{all}','TourEditController@index')->where(['all' => '.*']);
    
});



// Auth stuff

Route::get("/login", "HomeController@login")->name("login");
Route::get("/logout", "HomeController@logout")->name("logout");

Route::get('socialite/{provider}', 'Auth\SocialiteController@redirectToProvider');
Route::get('socialite/{provider}/callback', 'Auth\SocialiteController@handleProviderCallback');

if (config('shibboleth.emulate_idp') ) {
    Route::name('shiblogin')->get("shiblogin", '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateLogin');
    Route::group(['middleware' => 'web'], function () {
        Route::get('emulated/idp', '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateIdp');
        Route::post('emulated/idp', '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateIdp');
        Route::get('emulated/login', '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateLogin');
        Route::get('emulated/logout', '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateLogout');
    });
} else {
    Route::name('shiblogin')->get("shiblogin", '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@login');
    Route::group(['middleware' => 'web'], function () {
        Route::name('shibboleth-login')->get('/shibboleth-login', '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@login');
        Route::name('shibboleth-authenticate')->get('/shibboleth-authenticate', '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@idpAuthenticate');
        Route::name('shibboleth-logout')->get('/shibboleth-logout', '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@destroy');
    });
}

Route::any('{all}','HomeController@index')->where(['all' => '.*']);