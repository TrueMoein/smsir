<?php


Route::group(['namespace'=>'phplusir\smsir\Controllers'], function (){
	Route::get('sms-admin','SmsirController@index');
	Route::get('sms-admin/create','SmsirController@create');
	Route::get('sms-admin/send','SmsirController@send');
	Route::get('sms-admin/add','SmsirController@addToCustomer');
	Route::get('sms-admin/sendcc','SmsirController@sendCC');
	Route::get('sms-admin/verf','SmsirController@verf');
});