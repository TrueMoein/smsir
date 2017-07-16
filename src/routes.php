<?php
Route::group(['namespace'=>'phplusir\smsir\Controllers','middleware'=>config('smsir.middleware')], function (){
	Route::get('sms-admin','SmsirController@index')->name('sms-admin');
	Route::get('sms-admin/{log}/delete','SmsirController@delete')->name('deleteLog');
});