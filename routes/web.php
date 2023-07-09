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

/** Landing Page */
Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy', function () {
    return view('privacy');
});

Route::get('/terms-of-service', function () {
    return view('terms');
});

Route::get('/support', function () {
    return view('support');
});

/** End Landing Page */

Route::get( 'login', 'AuthController@login' )->name('login');
Route::post( 'login', 'AuthController@login' );
Route::get( 'logout', 'AuthController@logout' )->name('logout');
// Route::get( 'register', function(){
//     return 'Registration page still under development';
// } )->name( 'register' );
Route::get( 'docs', 'DocumentationController@index' );

Route::get( 'item', 'ItemController@show' )->name('item');



// You can define the routes below in the RouteServiceProvider.php if you want
// I prefer though it being defined here

// Cockpit is a user admin section
// You may rename 'cockpit' if you have a better term to use
if( request()->segment( 1 ) == 'cockpit' ){
    Route::group( ['prefix'=>'cockpit' , 'namespace'=>'Cockpit' , 'middleware' => ['auth'] ],function(){
        require_once( __DIR__.'/web/route_cockpit.php');
    });
    // return early as not to parse other routes
    return;
}

// Admin section for management and site owners
if( request()->segment( 1 ) == 'admin' ){
    Route::group( ['prefix'=>'admin' , 'namespace'=>'Admin' , 'middleware' => [ 'auth' ] ],function(){
        require_once( __DIR__.'/web/route_admin.php');
    });
    // return early as not to parse other routes
    return;
}

// XHR / Ajax routes
if( request()->segment( 1 ) == 'ajax' ){
    Route::group( ['prefix'=>'ajax' , 'namespace'=>'Ajax', 'middleware' => ['ajax'] ],function(){
        require_once( __DIR__.'/web/route_ajax.php');
    });
    // return early as not to parse other routes
    return;
}


Route::get( 'u/spaces', 'UtilityController@uploadToSpaces' );
Route::get( 'u/slack', 'UtilityController@notifyThroughSlack' );
Route::get( 'bug-report', function(){
        //
})->name('bug-report');

Route::get( 'u/do', 'UtilityController@DigitalOcean' );

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});


Route::get('devices', 'DevicesController@index')->name('devices');

