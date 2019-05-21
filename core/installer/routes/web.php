<?php

Route::group([
    'prefix'     => 'install',
    'as'         => 'installers.',
    'namespace'  => 'Botble\Installer\Http\Controllers',
    'middleware' => ['web'],
], function () {

    Route::group(['middleware' => 'install'], function () {
        Route::get('/', [
            'as'   => 'welcome',
            'uses' => 'InstallController@getWelcome',
        ]);

        Route::get('environment', [
            'as'   => 'environment',
            'uses' => 'InstallController@getEnvironment',
        ]);

        Route::post('environment/save', [
            'as'   => 'environment.save',
            'uses' => 'InstallController@postSaveEnvironment',
        ]);

        Route::get('requirements', [
            'as'   => 'requirements',
            'uses' => 'InstallController@getRequirements',
        ]);
    });

    Route::get('account', [
        'as'   => 'create_account',
        'uses' => 'InstallController@getCreateAccount',
    ]);

    Route::post('account/save', [
        'as'   => 'account.save',
        'uses' => 'InstallController@postSaveAccount',
    ]);

    Route::get('final', [
        'as'   => 'final',
        'uses' => 'InstallController@getFinish',
    ]);
});
