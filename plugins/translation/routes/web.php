<?php

Route::group(['namespace' => 'Botble\Translation\Http\Controllers', 'middleware' => 'web'], function () {
    Route::group(['prefix' => config('core.base.general.admin_dir'), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'system/translations'], function () {
            Route::get('view/{groupKey?}', [
                'as' => 'translations.group.view',
                'uses' => 'TranslationController@getView',
                'permission' => 'translations.edit',
            ])->where('groupKey', '.*');

            Route::get('/{groupKey?}', [
                'as' => 'translations.list',
                'uses' => 'TranslationController@getIndex',
                'permission' => 'translations.edit',
            ])->where('groupKey', '.*');

            Route::post('edit/{groupKey}', [
                'as' => 'translations.group.edit',
                'uses' => 'TranslationController@postEdit',
                'permission' => 'translations.edit',
            ])->where('groupKey', '.*');

            Route::post('add/{groupKey}', [
                'as' => 'translations.group.add',
                'uses' => 'TranslationController@postAdd',
                'permission' => 'translations.create',
            ])->where('groupKey', '.*');

            Route::post('/delete/{groupKey}/{translationKey}', [
                'as' => 'translations.group.delete',
                'uses' => 'TranslationController@postDelete',
                'permission' => 'translations.delete',
            ])->where('groupKey', '.*');

            Route::post('/publish/{groupKey}', [
                'as' => 'translations.group.publish',
                'uses' => 'TranslationController@postPublish',
                'permission' => 'translations.edit',
            ])->where('groupKey', '.*');

            Route::post('/import', [
                'as' => 'translations.import',
                'uses' => 'TranslationController@postImport',
                'permission' => 'translations.edit',
            ]);

            Route::post('/find', [
                'as' => 'translations.find',
                'uses' => 'TranslationController@postFind',
                'permission' => 'translations.create',
            ]);
        });
    });
});
