<?php

Route::group([
    'namespace' => 'Botble\Member\Http\Controllers',
    'prefix' => config('core.base.general.admin_dir'),
    'middleware' => ['web', 'auth'],
], function () {
    Route::group(['prefix' => 'members'], function () {
        Route::get('/', [
            'as' => 'member.list',
            'uses' => 'MemberController@getList',
        ]);

        Route::get('/create', [
            'as' => 'member.create',
            'uses' => 'MemberController@getCreate',
        ]);

        Route::post('/create', [
            'as' => 'member.create',
            'uses' => 'MemberController@postCreate',
        ]);

        Route::get('/edit/{id}', [
            'as' => 'member.edit',
            'uses' => 'MemberController@getEdit',
        ]);

        Route::post('/edit/{id}', [
            'as' => 'member.edit',
            'uses' => 'MemberController@postEdit',
        ]);

        Route::get('/delete/{id}', [
            'as' => 'member.delete',
            'uses' => 'MemberController@getDelete',
        ]);

        Route::post('/delete-many', [
            'as' => 'member.delete.many',
            'uses' => 'MemberController@postDeleteMany',
            'permission' => 'member.delete',
        ]);
    });
});

Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

    Route::group([
        'namespace' => 'Botble\Member\Http\Controllers',
        'middleware' => ['web', 'member.guest'],
        'as' => 'public.member.',
    ], function () {
        Route::get('/login', 'LoginController@showLoginForm')->name('login');
        Route::post('/login', 'LoginController@login')->name('login.post');

        Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('/register', 'RegisterController@register')->name('register.post');

        Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.request');
        Route::post('/password/reset', 'ResetPasswordController@reset')->name('password.email');
        Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.update');

        Route::get('/register/confirm/resend', 'RegisterController@resendConfirmation')->name('resend_confirmation');
        Route::get('/register/confirm/{email}', 'RegisterController@confirm')->name('confirm');
    });

    Route::group([
        'namespace' => 'Botble\Member\Http\Controllers',
        'middleware' => ['web', 'member'],
        'prefix' => 'account',
        'as' => 'public.member.',
    ], function () {
        Route::get('/logout', 'LoginController@logout')->name('logout');

        Route::get('/overview', [
            'as' => 'overview',
            'uses' => 'PublicController@getOverview',
        ]);

        Route::get('/edit', [
            'as' => 'edit',
            'uses' => 'PublicController@getEditAccount',
        ]);

        Route::post('/edit', [
            'as' => 'edit',
            'uses' => 'PublicController@postEditAccount',
        ]);

        Route::get('/password', [
            'as' => 'password',
            'uses' => 'PublicController@getChangePassword',
        ]);

        Route::post('/change-password', [
            'as' => 'post.change-password',
            'uses' => 'PublicController@postChangePassword',
        ]);

        Route::get('/avatar', [
            'as' => 'avatar',
            'uses' => 'PublicController@getChangeProfileImage',
        ]);

        Route::post('/change-avatar', [
            'as' => 'change-avatar',
            'uses' => 'PublicController@postChangeProfileImage',
        ]);
    });

});
