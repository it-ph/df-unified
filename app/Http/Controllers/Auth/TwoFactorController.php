<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorCode;

class TwoFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'twofactor']);
    }

    public function index()
    {
        return view('auth.twoFactor');
    }

    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'integer|required',
        ]);

        // add two factor code expires

        $user = auth()->user();
        if($request->input('two_factor_code') == $user->two_factor_code)
        {
            $user->resetTwoFactorCode();

            return redirect()->route('home');
        }

        return redirect()->back()->withErrors(['two_factor_code' => 'The two factor code you have entered does not match!']);
    }

    public function resend()
    {
        $user = auth()->user();
        $user->generateTwoFactorCode();
        if($this->is_connected())
        {
            $user->notify(new TwoFactorCode());
        }

        return redirect()->back()->withErrors(['two_factor_code' => 'The two factor code has been sent again']);
    }

    //Check if connected to Internet
    function is_connected()
    {
        $connected = @fsockopen("www.google.com", 80);
        //website, port  (try 80 or 443)
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }
}
