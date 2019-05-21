<?php

Route::group(['namespace' => 'Botble\Api\Http\Controllers', 'middleware' => 'web'], function () {

    Route::group(['prefix' => config('core.base.general.admin_dir'), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'api'], function () {
            Route::get('/clients', [
                'as' => 'api.clients',
                'uses' => 'ApiClientController@getClients',
                'permission' => 'api.clients',
            ]);
            Route::get('/clients/create', [
                'as' => 'api.clients.create',
                'uses' => 'ApiClientController@getCreateClient',
                'permission' => 'api.clients',
            ]);
            Route::post('/clients/create', [
                'as' => 'api.clients.create.post',
                'uses' => 'ApiClientController@postCreateClient',
                'permission' => 'api.clients',
            ]);
            Route::get('/clients/edit/{id}', [
                'as' => 'api.clients.edit',
                'uses' => 'ApiClientController@getEditClient',
                'permission' => 'api.clients',
            ]);
            Route::post('/clients/edit/{id}', [
                'as' => 'api.clients.edit.post',
                'uses' => 'ApiClientController@postEditClient',
                'permission' => 'api.clients',
            ]);
            Route::get('/clients/delete/{id}', [
                'as' => 'api.clients.delete',
                'uses' => 'ApiClientController@getDeleteClient',
                'permission' => 'api.clients',
            ]);
            Route::delete('/clients/edit/{id}', [
                'as' => 'api.clients.delete.post',
                'uses' => 'ApiClientController@postDeleteClient',
                'permission' => 'api.clients',
            ]);
        });
    });
    
});