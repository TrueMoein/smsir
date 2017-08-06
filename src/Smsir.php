<?php

namespace phplusir\smsir;
use GuzzleHttp\Client;

class Smsir
{
	/**
	 * this method use in every request to get the token at first.
	 *
	 * @return mixed - the Token for use api
	 */
	public static function getToken()
	{
		$client     = new Client();
		$body       = ['UserApiKey'=>config('smsir.api-key'),'SecretKey'=>config('smsir.secret-key'),'System'=>'laravel_v_1_4'];
		$result     = $client->post('http://ws.sms.ir/api/Token',['json'=>$body]);
		return json_decode($result->getBody(),true)['TokenKey'];
	}

	/**
	 * this method return your credit in sms.ir
	 *
	 * @return mixed - credit
	 */
	public static function credit()
	{
		$client     = new Client();
		$result     = $client->get('http://ws.sms.ir/api/credit',['headers'=>['x-sms-ir-secure-token'=>self::getToken()]]);
		return json_decode($result->getBody(),true)['Credit'];
	}


	/**
	 * Simple send message with sms.ir account and line number
	 *
	 * @param array $messages = Messages - Count must be equal with $numbers
	 * @param array $numbers  = Numbers - must be equal with $messages
	 * @param null $sendDateTime = dont fill it if you want to send message now
	 *
	 * @return mixed
	 */
	public static function send(array $messages,array $numbers,$sendDateTime = null)
	{
		$client     = new Client();
		if($sendDateTime === null) {
			$body = ['Messages'=>$messages,'MobileNumbers'=>$numbers,'LineNumber'=>config('smsir.line-number')];
		} else {
			$body = ['Messages'=>$messages,'MobileNumbers'=>$numbers,'LineNumber'=>config('smsir.line-number'),'SendDateTime'=>$sendDateTime];
		}
		$result     = $client->post('http://ws.sms.ir/api/MessageSend',['json'=>$body,'headers'=>['x-sms-ir-secure-token'=>self::getToken()]]);
		if(config('smsir.db-log')) {
			$res = json_decode($result->getBody()->getContents(),true);
			foreach ( array_combine( $messages, $numbers ) as $message => $number ) {
				SmsirLogs::create( [
					'response' => $res['Message'],
					'message'  => $message,
					'status'   => $res['IsSuccessful'],
					'from'     => config('smsir.line-number'),
					'to'       => $number,
				] );
			}
		}
		return $result->getBody()->getContents();
	}


	/**
	 * add a person to the customer club contacts
	 *
	 * @param $prefix               = mr, dr, dear...
	 * @param $firstName            = first name of this contact
	 * @param $lastName             = last name of this contact
	 * @param $mobile               = contact mobile number
	 * @param string $birthDay      = birthday of contact, not require
	 * @param string $categotyId    = which category id of your customer club to join this contact?
	 *
	 * @return \Psr\Http\Message\ResponseInterface = $result as json
	 */
	public static function addToCustomerClub($prefix,$firstName,$lastName,$mobile,$birthDay = '',$categotyId = '')
	{
		$client     = new Client();
		$body = ['Prefix'=>$prefix,'FirstName'=>$firstName,'LastName'=>$lastName,'Mobile'=>$mobile,'BirthDay'=>$birthDay,'CategoryId'=>$categotyId];
		$result     = $client->post('http://ws.sms.ir/api/CustomerClubContact',['json'=>$body,'headers'=>['x-sms-ir-secure-token'=>self::getToken()]]);
		$res = json_decode($result->getBody()->getContents(),true);
		if(config('smsir.db-log')){
			SmsirLogs::create([
				'response'  => $res['Message'],
				'message'   => "افزودن $firstName $lastName به مخاطبین باشگاه ",
				'status'    => $res['IsSuccessful'],
				'from'      => 'باشگاه مشتریان',
				'to'        => $mobile,
			]);
		}
		return $result->getBody()->getContents();
	}

	/**
	 * @param array $messages
	 * @param array $numbers
	 * @param null $sendDateTime
	 * @param bool $canContinueInCaseOfError
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public static function sendToCustomerClub(array $messages,array $numbers,$sendDateTime = null,$canContinueInCaseOfError = true)
	{
		$client = new Client();
		if($sendDateTime !== null) {
			$body   = ['Messages'=>$messages,'MobileNumbers'=>$numbers,'SendDateTime'=>$sendDateTime,'CanContinueInCaseOfError'=>$canContinueInCaseOfError];
		} else {
			$body   = ['Messages'=>$messages,'MobileNumbers'=>$numbers,'CanContinueInCaseOfError'=>$canContinueInCaseOfError];
		}
		$result = $client->post('http://ws.sms.ir/api/CustomerClub/Send',['json'=>$body,'headers'=>['x-sms-ir-secure-token'=>self::getToken()]]);
		if(config('smsir.db-log')){
			$res = json_decode($result->getBody()->getContents(),true);
			foreach (array_combine($messages, $numbers) as $message => $number) {
				SmsirLogs::create([
					'response'  => $res['Message'],
					'message'   => $message,
					'status'    => $res['IsSuccessful'],
					'from'      => 'باشگاه مشتریان',
					'to'        => $number,
				]);
			}
		}
		return $result->getBody()->getContents();
	}

	/**
	 * @param $prefix
	 * @param $firstName
	 * @param $lastName
	 * @param $mobile
	 * @param $message
	 * @param string $birthDay
	 * @param string $categotyId
	 *
	 * @return mixed
	 */
	public static function addContactAndSend($prefix,$firstName,$lastName,$mobile,$message,$birthDay = '',$categotyId = '')
	{
		$client = new Client();
		$body   = ['Prefix'=>$prefix,'FirstName'=>$firstName,'LastName'=>$lastName,'Mobile'=>$mobile,'BirthDay'=>$birthDay,'CategoryId'=>$categotyId,'MessageText'=>$message];
		$result = $client->post('http://ws.sms.ir/api/CustomerClub/AddContactAndSend',['json'=>[$body],'headers'=>['x-sms-ir-secure-token'=>self::getToken()]]);
		if(config('smsir.db-log')){
			$res = json_decode($result->getBody()->getContents(),true);
			SmsirLogs::create([
				'response'  => $res['Message'],
				'message'   => $message,
				'status'    => $res['IsSuccessful'],
				'from'      => 'باشگاه مشتریان',
				'to'        => $mobile,
			]);
		}
		return $result->getBody()->getContents();
	}


	/**
	 * @param $code
	 * @param $number
	 *
	 * @return mixed
	 */
	public static function sendVerification($code,$number)
	{
		$client = new Client();
		$body   = ['Code'=>$code,'MobileNumber'=>$number];
		$result = $client->post('http://ws.sms.ir/api/VerificationCode',['json'=>$body,'headers'=>['x-sms-ir-secure-token'=>self::getToken()]]);
		if(config('smsir.db-log')){
			$res = json_decode($result->getBody()->getContents(),true);
			SmsirLogs::create([
				'response'  => $res['Message'],
				'message'   => $code,
				'status'    => $res['IsSuccessful'],
				'from'      => 'باشگاه مشتریان',
				'to'        => $number,
			]);
		}
		return $result->getBody()->getContents();
	}
}