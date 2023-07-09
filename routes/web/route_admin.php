<?php


Route::get( 'users', 'AdminUsersController@index' );
Route::get( 'user/{user_id}' , 'AdminUsersController@user' );

Route::group( ['prefix'=>'categories' ],function(){
    Route::get( '', 'AdminCategoriesController@index' );
    Route::get( 'create' , 'AdminCategoriesController@addCategory' );
    Route::get( '{category_id}' , 'AdminCategoriesController@category' );
});

Route::group( ['prefix'=>'countries' ],function(){
    Route::get( '', 'AdminCountriesController@index' );
    Route::get( 'create' , 'AdminCountriesController@addCountry' );
    Route::get( '{country_id}' , 'AdminCountriesController@country' );
});

Route::group( ['prefix'=>'cities' ],function(){
    Route::get( '', 'AdminCitiesController@index' );
    Route::get( 'create' , 'AdminCitiesController@addCity' );
    Route::get( '{city_id}' , 'AdminCitiesController@city' );
});

Route::group( ['prefix'=>'items' ],function(){
    Route::get( '', 'AdminItemsController@index' );
    Route::get( 'create' , 'AdminItemsController@addItem' );
    Route::get( '{item_id}' , 'AdminItemsController@item' );
});

Route::group( ['prefix'=>'locations' ],function(){
    Route::get( '', 'AdminLocationsController@index' );
    Route::get( 'create' , 'AdminLocationsController@addLocation' );
    Route::get( '{location_id}' , 'AdminLocationsController@location' );
});


Route::get( 'settings', 'AdminSettingsController@index' );

Route::group(['prefix' => 'reports'], function () {
    Route::get('', 'AdminReportController@index');
    Route::get('/{report}', 'AdminReportController@show');
});