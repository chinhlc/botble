<?php

Route::group([
    'middleware' => ['web', 'auth'],
    'prefix' => 'tables',
    'namespace' => 'Botble\Table\Http\Controllers',
    'permission' => false,
], function () {
    Route::get('/bulk-change/data', 'TableController@getDataForBulkChanges')->name('tables.bulk-change.data');
    Route::post('/bulk-change/save', 'TableController@postSaveBulkChange')->name('tables.bulk-change.save');
    Route::get('/get-filter-input', 'TableController@getFilterInput')->name('tables.get-filter-input');
});
