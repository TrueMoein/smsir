<?php

namespace phplusir\smsir\Controllers;

use App\Http\Controllers\Controller;
use phplusir\smsir\Smsir;

class SmsirController extends Controller
{
	public function index()
	{
		$credit = Smsir::credit();
		return view('smsir::index',compact('credit'));
	}

	public function send()
	{
		$numbers = ['09301240100'];
		Smsir::send(['تست ارسال علیزاده'],$numbers);
	}

	public function addToCustomer() {
		$send = Smsir::addContactAndSend('آقای','معین','علیزاده','09301240100','این تست برای بخش دوم');
		var_dump($send);
	}

	public function sendCC() {
		Smsir::sendToCustomerClub(['سلام'],['09301240100']);
	}

	public function verf() {
		$verf = Smsir::sendVerification('1290839','09301240100');
		var_dump($verf);
	}


}