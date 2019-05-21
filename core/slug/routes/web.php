<?php

Route::group(['namespace' => 'Botble\Slug\Http\Controllers', 'middleware' => 'web'], function () {
    Route::group(['prefix' => config('core.base.general.admin_dir'), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'slug'], function () {
            Route::post('/create', [
                'as' => 'slug.create',
                'uses' => 'SlugController@postCreate',
                'permission' => false,
            ]);
        });
    });
});
