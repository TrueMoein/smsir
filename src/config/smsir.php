<?php



return [

	/* Important Settings */

	// SMS.ir Api Key
	'api-key' => env('SMSIR-API-KEY','Your sms.ir api key'),
	// SMS.ir Secret Key
	'secret-key' => env('SMSIR-SECRET-KEY','Your sms.ir secret key'),

	'line-number' => env('SMSIR-LINE-NUMBER','Your sms.ir line number'),

	'db-log' => true,
	/* Admin Panel */

	'title' => 'مدیریت پیامک ها'

];