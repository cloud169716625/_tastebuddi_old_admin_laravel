<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['cors'])->namespace('API\V1')->prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        // anyone can access
        Route::post('register', 'ApiAuthController@register');
        Route::post('login', 'ApiAuthController@login');
        Route::post('email/check', 'ApiAuthController@checkEmail');
        Route::post('verify', 'ApiAuthController@verify');
        Route::post('verification/resend', 'ApiAuthController@resendVerificationCode');

        // must be authenticated user
        Route::middleware(['jwt'])->group(function () {
            Route::delete('delete-my-account', 'ApiAuthController@deleteAccount');
            Route::get('user', 'ApiAuthController@getAuthUser');
            Route::get('logout', 'ApiAuthController@logout');

            // Password Verification
            Route::post('/account/password-verification', 'ApiAuthController@passwordVerification');
        });
    });

    Route::prefix('password')->group(function () {
        // anyone can access
        Route::post('email', 'ApiPasswordResetsController@sendResetCodeEmail');
        Route::post('reset', 'ApiPasswordResetsController@resetPassword');
    });

    Route::prefix('user')->group(function () {
        Route::middleware(['jwt'])->group(function () {
            Route::get('', 'ApiUsersController@show');
            Route::put('', 'ApiUsersController@update');
            Route::put('/{user_id}', 'ApiUsersController@update');
            Route::post('photo', 'ApiUsersController@uploadProfilePhoto');
            Route::delete('photo', 'ApiUsersController@deleteProfilePhoto');
            Route::get('/{user_id}/watchlist', 'ApiUserWatchedItemsController@show');
            Route::post('/{user_id}/watchlist', 'ApiUserWatchedItemsController@store');
            Route::delete('/{user_id}/watchlist', 'ApiUserWatchedItemsController@delete');
        });
    });

    Route::prefix('device')->middleware([ 'jwt' ])->group(function () {
        Route::post('register', 'ApiDevicesController@store');
        Route::delete('unregister/{device_id}', 'ApiDevicesController@destroy');
        Route::get('test', 'ApiDevicesController@test');
    });

    /*    Route::prefix('notification')->middleware([ 'jwt' ])->group(function () {
            Route::post( 'test', 'ApiPushNotificationsController@test');
        });

        // chat
        Route::prefix('chat')->middleware([ 'jwt' , 'chat' ])->group(function () {
            Route::post( 'send', 'ApiChatController@send' );
            Route::get( 'channels', 'ApiChatController@channels' );
            Route::get( 'history', 'ApiChatController@historyByChannel' );
            Route::get( 'unread', 'ApiChatController@unread' );
            Route::post( 'reset_unread', 'ApiChatController@resetUnread' );
        });


        Route::prefix( 'connections' )->middleware([ 'jwt' , 'chat' ])->group(function () {
            Route::post( 'connect', 'ApiConnectionsController@connect' );
            Route::post( 'status/change', 'ApiConnectionsController@changeStatus' );
            Route::get( 'active', 'ApiConnectionsController@activeConnections' );
            Route::get( 'pending', 'ApiConnectionsController@pendingConnections' );
        }); */

    Route::prefix('countries')->middleware([])->group(function () {
        Route::get('', 'ApiCountriesController@getCountries');
        Route::get('{country_id}/cities', 'ApiCitiesController@getCities');
        Route::get('{country_id}/locations/{keyword}', 'ApiCountriesController@getCountryLocations');
        Route::post('user/{user_id}', 'ApiCountriesController@saveCountry');
        Route::get('user/{user_id}', 'ApiCountriesController@getSavedCountries');
        Route::delete('user/{user_id}', 'ApiCountriesController@deleteSavedCountry');
    });

    Route::prefix('items')->group(function () {
        Route::get('', 'ApiItemsController@index');
        Route::get('{item_id}', 'ApiItemsController@getItem');
        Route::get('user/{user_id}', 'ApiItemsController@getContributedItems');
    });

    Route::prefix('items')->middleware(['jwt'])->group(function () {
        Route::post('', 'ApiItemsController@submitNewItem');
    });

    Route::prefix('currencies')->middleware([])->group(function () {
        Route::get('', 'ApiCountriesController@getCurrencies');
        Route::get('rates', 'ApiCountriesController@getRates');
    });

    Route::prefix('cities')->middleware([])->group(function () {
        Route::get('{city_id}/items', 'ApiCitiesController@getItems');
        Route::get('{city_id}/items/{item_id}', 'ApiCitiesController@getItemDetails');
        Route::get('{city_id}/locations/{keyword}', 'ApiCitiesController@getCityLocations');
        Route::get('{city_id}/items/{item_id}/recommendations', 'ApiRecommendationsController@getItemRecommendations');
    });

    Route::prefix('cities')->middleware(['jwt'])->group(function () {
        Route::put('{city_id}/items/{item_id}', 'ApiCitiesController@updateItemDetails');
    });


    Route::prefix('offers')->middleware([])->group(function () {
        Route::get('', 'ApiOffersController@getOffers');
        Route::get('{exclusive_offer_id}', 'ApiOffersController@getOfferDetails');
    });

    Route::prefix('locations')->middleware([])->group(function () {
        Route::get('{place_id}/details', 'ApiRecommendationsController@getPlaceDetails');
        Route::get('{location_id}/recommendations', 'ApiRecommendationsController@getPlaceRecommendations');
        Route::get('{location}/businesses', 'ApiBusinessController@getLocationBusinesses');
        Route::get('{location}/business-details', 'ApiBusinessController@getBusinessDetails');
    });

    Route::prefix('search')->middleware([])->group(function () {
        Route::get('country/{country_id}', 'ApiCountriesController@search');
        Route::get('country/{country_id}/{keyword}', 'ApiCountriesController@search');
        Route::get('dashboard/', 'ApiDashboardSearchController@search');
        Route::get('dashboard/{keyword}', 'ApiDashboardSearchController@search');
    });

    Route::prefix('report')->middleware(['jwt'])->group(function () {
        Route::get('/categories', 'ApiReportCategoriesController@index');
        Route::post('/', 'ApiReportController');
    });

    // Settings
    Route::group(['prefix' => 'settings'], function () {
        Route::get('pages/{pageType}', 'ApiPageSettingsController@show');
        Route::get('pages', 'ApiPageSettingsController@index');
    });
});
