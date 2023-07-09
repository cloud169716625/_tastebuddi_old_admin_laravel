<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'users' ], function () {
    Route::get('', 'AjaxUsersController@getUsers');
});

Route::group(['prefix'=>'user' ], function () {
    Route::get('', 'AjaxUsersController@getUser');
    Route::post('', 'AjaxUsersController@saveUser');
    Route::delete('', 'AjaxUsersController@deleteUser');
    Route::get('recommendations', 'AjaxUsersController@getUserRecommendations');
    Route::post('photo/upload', 'AjaxUsersController@uploadProfilePhoto');
    Route::post('/{userId}/activate', 'AjaxUsersController@activate');
    Route::post('/{userId}/disable', 'AjaxUsersController@disable');
    Route::post('/{userId}/enable', 'AjaxUsersController@enable');
    Route::post('/{userId}/recommendations/{recommendation}/deactivate', 'AjaxUsersController@deactivateRecommendation');
});

Route::group(['prefix'=>'countries' ], function () {
    Route::get('', 'AjaxCountriesController@getCountries');
});

Route::group(['prefix'=>'country' ], function () {
    Route::get('', 'AjaxCountriesController@getCountry');
    Route::post('', 'AjaxCountriesController@saveCountry');
    Route::delete('', 'AjaxCountriesController@deleteCountry');
    Route::post('background_photo/upload', 'AjaxCountriesController@uploadBackgroundPhoto')->middleware(['upload']);
    Route::post('flag_photo/upload', 'AjaxCountriesController@uploadFlagPhoto')->middleware(['upload']);
});


Route::group(['prefix'=>'categories' ], function () {
    Route::get('', 'AjaxCategoriesController@getCategories');
});

Route::group(['prefix'=>'category' ], function () {
    Route::get('', 'AjaxCategoriesController@getCategory');
    Route::post('', 'AjaxCategoriesController@saveCategory');
    Route::delete('', 'AjaxCategoriesController@deleteCategory');
    Route::get('categories', 'AjaxCategoriesController@getCategory');
    Route::post('/{category}/order', 'AjaxCategoriesController@order');
});

Route::group(['prefix'=>'cities' ], function () {
    Route::get('', 'AjaxCitiesController@getCities');

    Route::get('{id}/items', 'AjaxCitiesController@getItemsByCity');
});

Route::group(['prefix'=>'city' ], function () {
    Route::get('', 'AjaxCitiesController@getCity');
    Route::post('', 'AjaxCitiesController@saveCity');
    Route::delete('', 'AjaxCitiesController@deleteCity');
    Route::get('countries', 'AjaxCitiesController@getCountries');
});

Route::group(['prefix'=>'items' ], function () {
    Route::get('', 'AjaxItemsController@getItems');
});

Route::group(['prefix'=>'item' ], function () {
    Route::get('', 'AjaxItemsController@getItem');
    Route::post('', 'AjaxItemsController@saveItem');
    Route::put('', 'AjaxItemsController@updateItem');
    Route::delete('', 'AjaxItemsController@deleteItem');
    Route::get('items', 'AjaxItemsController@getItems');
    Route::get('categories', 'AjaxItemsController@getCategories');
    Route::get('cities', 'AjaxItemsController@getCities');
    Route::post('item_photo/upload', 'AjaxItemsController@uploadItemPhoto');
    Route::get('details', 'AjaxItemsController@getItemDetails');
    Route::post('details', 'AjaxItemsController@addItemDetails');
    Route::delete('details', 'AjaxItemsController@deleteDetail');
    Route::get('locations', 'AjaxItemsController@getLocations');
});

Route::group(['prefix' => 'details'], function () {
    Route::put('/{itemDetail}', 'AjaxItemDetailsController@update');
});

Route::group(['prefix'=>'locations' ], function () {
    Route::get('all', 'AjaxLocationsController@getAllLocations');
    Route::get('', 'AjaxLocationsController@getLocations');
    Route::post('{location}/unverify-verify', 'AjaxLocationsController@verifyUnverify');
    Route::get('{location}/item-details', 'AjaxLocationsController@getLocationItemDetails');
    Route::post('{location}/create-item', 'AjaxLocationsController@createItem');
});


Route::group(['prefix'=>'verified-businesses' ], function () {
    Route::post('{location}/tag-item', 'AjaxVerifiedBusinessController@tagItem');
    Route::get('{location}/tagged-items', 'AjaxVerifiedBusinessController@getTaggedItems');
    Route::get('{location}/tagged-items', 'AjaxVerifiedBusinessController@getTaggedItems');
    Route::delete('{location}/untagged-item/{verifiedBusinessItemId}', 'AjaxVerifiedBusinessController@unTaggedItem');
    Route::post('{location}/create-item', 'AjaxVerifiedBusinessController@createItem');
});

Route::group(['prefix'=>'location' ], function () {
    Route::get('', 'AjaxLocationsController@getLocation');
    Route::post('', 'AjaxLocationsController@saveLocation');
    Route::delete('', 'AjaxLocationsController@deleteLocation');
});

Route::group(['prefix'=>'settings' ], function () {
    Route::get('/{setting}', 'AjaxSettingsController@show');
    Route::get('', 'AjaxSettingsController@getSettings');
    Route::post('', 'AjaxSettingsController@saveSettings');
    Route::post('setupsubdomains', 'AjaxSettingsController@setupSubDomains');
    Route::put('', 'AjaxSettingsController@update');
});

Route::group(['prefix'=>'notifications' ], function () {
    Route::post('send', 'NotificationsController@send');
});

Route::group(['prefix' => 'reports'], function () {
    Route::get('/categories', 'AjaxReportCategoryController');

    Route::get('/', 'AjaxReportController@index');
    Route::get('/{report}', 'AjaxReportController@show');
    Route::delete('/{report}', 'AjaxReportController@destroy');
    Route::delete('/{report}/disable', 'AjaxReportController@destroyReportable');
});

