<?php

namespace phplusir\smsir\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use phplusir\smsir\models\SmsirLogs;
use phplusir\smsir\Smsir;


class SmsirController extends Controller
{

    // the main index page for administrators
    public function index()
    {
        $credit = Smsir::credit();
        $smsir_logs = SmsirLogs::orderBy('id', 'DESC')->paginate(config('smsir.in-page'));
        return view('smsir::index', compact('credit', 'smsir_logs'));
    }

    // administrators can delete single log
    public function delete(SmsirLogs $log)
    {
        $log->delete();
        // return the user back to sms-admin after delete the log
        return back();
    }
}
