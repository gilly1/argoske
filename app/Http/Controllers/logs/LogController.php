<?php

namespace App\Http\Controllers\logs;

use App\Model\logs\log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function logs($subdomain)
    {
        $messages = log::orderBy('created_at','DESC')->limit(50)->get();
        return view('logs/log')->with('messages',$messages)->with('subdomain',$subdomain);
    }
    public function userLogs ($subdomain)
    {
        $messages = log::where('user_id',auth()->user()->id)->orderBy('created_at','DESC')->limit(50)->get();
        return view('notification/notification')->with('type','userLogs')->with('messages',[])->with('userLogs',$messages)->with('subdomain',$subdomain);
    }
}
