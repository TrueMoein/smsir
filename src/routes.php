<?php
Route::group(['namespace' => 'phplusir\smsir\Controllers', 'middleware' => config('smsir.middlewares')], function () {
    Route::get(config('smsir.route'), 'SmsirController@index')->name('sms-admin');
    Route::get(config('smsir.route') . '/{log}/delete', 'SmsirController@delete')->name('deleteLog');
});
