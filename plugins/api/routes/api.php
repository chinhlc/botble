<?php

Route::group([
    'prefix' => 'api/v1',
    'namespace' => 'Botble\ACL\Http\Controllers\API',
    'middleware' => ['api'],
], function () {

    Route::post('login', 'AuthController@login');

    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('logout', 'AuthController@logout');
    });

});