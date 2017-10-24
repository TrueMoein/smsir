<?php

namespace phplusir\smsir;
use GuzzleHttp\Client;

class Smsir
{
	/**
	 * This method used for log the messages to the database if db-log set to true.
	 *
	 * @param $result
	 * @param $messages
	 * @param $numbers
	 * @internal param bool $addToCustomerClub | set to true if you want to log another message instead main message
	 */
	public static function DBlog($result, $messages, $numbers) {

		if(config('smsir.db-log')) {

			$res = json_decode($result->getBody()->getContents(),true);

			if(count($messages) === 1) {
				foreach ( $numbers as $number ) {
					SmsirLogs::create( [
						'response' => $res['Message'],
						'message'  => $messages[0],
						'status'   => $res['IsSuccessful'],
						'from'     => config('smsir.line-number'),
						'to'       => $number,
					]);
				}
			} else {
				foreach ( array_combine( $messages, $numbers ) as $message => $number ) {
					SmsirLogs::create( [
						'response' => $res['Message'],
						'message'  => $message,
						'status'   => $res['IsSuccessful'],
						'from'     => config('smsir.line-number'),
						'to'       => $number,
					]);
				}
			}
			return var_dump($res);
		}
	}

	/**
	 * this method use in every request to get the token at first.
	 *
	 * @return mixed - the Token for use api
	 */
	public static function getToken()
	{
		$client     = new Client();
		$body       = ['UserApiKey'=>config('smsir.api-key'),'SecretKey'=>config('smsir.secret-key'),'System'=>'laravel_v_1_4'];
		$result     = $client->post('http://restfulsms.com/api/Token',['json'=>$body,'connect_timeout'=>30]);
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
		$result     = $client->get('http://restfulsms.com/api/credit',['headers'=>['x-sms-ir-secure-token'=>self::getToken()],'connect_timeout'=>30]);
		return json_decode($result->getBody(),true)['Credit'];
	}

	/**
	 * Simple send message with sms.ir account and line number
	 *
	 * @param $messages = Messages - Count must be equal with $numbers
	 * @param $numbers  = Numbers - must be equal with $messages
	 * @param null $sendDateTime = dont fill it if you want to send message now
	 *
	 * @return mixed
	 */
	public static function send($messages,$numbers,$sendDateTime = null)
	{
		$client     = new Client();
		$messages = (array)$messages;
		$numbers = (array)$numbers;
		if($sendDateTime === null) {
			$body   = ['Messages'=>$messages,'MobileNumbers'=>$numbers,'LineNumber'=>config('smsir.line-number')];
		} else {
			$body   = ['Messages'=>$messages,'MobileNumbers'=>$numbers,'LineNumber'=>config('smsir.line-number'),'SendDateTime'=>$sendDateTime];
		}
		$result     = $client->post('http://restfulsms.com/api/MessageSend',['json'=>$body,'headers'=>['x-sms-ir-secure-token'=>self::getToken()],'connect_timeout'=>30]);

		self::DBlog($result,$messages,$numbers);

		return json_decode($result->getBody(),true);
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
		$body       = ['Prefix'=>$prefix,'FirstName'=>$firstName,'LastName'=>$lastName,'Mobile'=>$mobile,'BirthDay'=>$birthDay,'CategoryId'=>$categotyId];
		$result     = $client->post('http://restfulsms.com/api/CustomerClubContact',['json'=>$body,'headers'=>['x-sms-ir-secure-token'=>self::getToken()],'connect_timeout'=>30]);
		$res        = json_decode($result->getBody()->getContents(),true);

		self::DBlog($res,"افزودن $firstName $lastName به مخاطبین باشگاه ",$mobile);

		return json_decode($result->getBody(),true);
	}

	/**
	 * @param $messages
	 * @param $numbers
	 * @param null $sendDateTime
	 * @param bool $canContinueInCaseOfError
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public static function sendToCustomerClub($messages,$numbers,$sendDateTime = null,$canContinueInCaseOfError = true)
	{
		$client     = new Client();
		$messages = (array)$messages;
		$numbers = (array)$numbers;
		if($sendDateTime !== null) {
			$body   = ['Messages'=>$messages,'MobileNumbers'=>$numbers,'SendDateTime'=>$sendDateTime,'CanContinueInCaseOfError'=>$canContinueInCaseOfError];
		} else {
			$body   = ['Messages'=>$messages,'MobileNumbers'=>$numbers,'CanContinueInCaseOfError'=>$canContinueInCaseOfError];
		}
		$result     = $client->post('http://restfulsms.com/api/CustomerClub/Send',['json'=>$body,'headers'=>['x-sms-ir-secure-token'=>self::getToken()],'connect_timeout'=>30]);

		self::DBlog($result,$messages,$numbers);

		return json_decode($result->getBody(),true);

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
		$result = $client->post('http://restfulsms.com/api/CustomerClub/AddContactAndSend',['json'=>[$body],'headers'=>['x-sms-ir-secure-token'=>self::getToken()],'connect_timeout'=>30]);

		self::DBlog($result,$message,$mobile);

		return json_decode($result->getBody(),true);
	}

	/**
	 * @param $code
	 * @param $number
	 *
	 * @param bool $log
	 *
	 * @return mixed
	 */
	public static function sendVerification($code,$number,$log = false)
	{
		$client = new Client();
		$body   = ['Code'=>$code,'MobileNumber'=>$number];
		$result = $client->post('http://restfulsms.com/api/VerificationCode',['json'=>$body,'headers'=>['x-sms-ir-secure-token'=>self::getToken()],'connect_timeout'=>30]);
		if($log) {
			self::DBlog($result,$code,$number);
		}
		return json_decode($result->getBody(),true);
	}

	/**
	 * @param $perPage
	 * @param $pageNumber
	 * @param $formDate
	 * @param $toDate
	 *
	 * @return mixed
	 */
	public static function getReceivedMessages($perPage,$pageNumber,$formDate,$toDate)
	{
		$client = new Client();
		$result = $client->get("http://restfulsms.com/api/ReceiveMessage?Shamsi_FromDate={$formDate}&Shamsi_ToDate={$toDate}&RowsPerPage={$perPage}&RequestedPageNumber={$pageNumber}",['headers'=>['x-sms-ir-secure-token'=>self::getToken()],'connect_timeout'=>30]);

		return json_decode($result->getBody()->getContents())->Messages;
	}

	/**
	 * @param $perPage
	 * @param $pageNumber
	 * @param $formDate
	 * @param $toDate
	 *
	 * @return mixed
	 */
	public static function getSentMessages($perPage,$pageNumber,$formDate,$toDate)
	{
		$client = new Client();
		$result = $client->get("http://restfulsms.com/api/MessageSend?Shamsi_FromDate={$formDate}&Shamsi_ToDate={$toDate}&RowsPerPage={$perPage}&RequestedPageNumber={$pageNumber}",['headers'=>['x-sms-ir-secure-token'=>self::getToken()],'connect_timeout'=>30]);

		return json_decode($result->getBody()->getContents())->Messages;
	}
}