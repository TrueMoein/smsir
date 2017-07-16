Official Laravel Package for Sms.ir
===================

<a style="text-align:center" href="https://www.sms.ir/%D8%AE%D8%AF%D9%85%D8%A7%D8%AA/%D9%88%D8%A8-%D8%B3%D8%B1%D9%88%DB%8C%D8%B3/%D8%A7%D8%B1%D8%B3%D8%A7%D9%84-%D9%BE%DB%8C%D8%A7%D9%85%DA%A9-laravel/">آموزش نصب و استفاده از این پکیج به زبان فارسی</a>

Hi, if you have an account in sms.ir, you can use this package for laravel

----------


How to install:
-------------

    composer require phplusir/smsir
    php artisan vendor:publish

> **Setup:**

add this line to your app.php providers:
phplusir\smsir\SmsirServiceProvider::class,

and add this line to your app.php aliases:
'Smsir' => phplusir\smsir\SmsirFacade::class,


> After publish the package files you must open smsir.php in config folder and set the api-key, secret-key and your sms line number.
> 

> **Like this:**

	'api-key' => env('SMSIR-API-KEY','Your sms.ir api key'),
	'secret-key' => env('SMSIR-SECRET-KEY','Your sms.ir secret key'),
	'line-number' => env('SMSIR-LINE-NUMBER','Your sms.ir line number'
> 
> Note:

you can set the keys and line number in your .env file

> **like this:**

> SMSIR-API-KEY=your api-key

> SMSIR-SECRET-KEY=your secret-key

> SMSIR-LINE-NUMBER=1000465******



Methods:
-------------

> Coming Soon

