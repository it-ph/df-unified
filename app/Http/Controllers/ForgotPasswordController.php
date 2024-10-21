<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\ForgotPasswordEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\ForgotPasswordRequestKey;

class ForgotPasswordController extends Controller
{
    public function forgotPassword()
    {
        return view('pages.admin.users.forgot-password');
    }

    public function submitForgotPassword(Request $request)
    {
        $this->validate($request,
            [
                'email' => 'required'
            ]
        );

        $is_exist = User::where('email', $request['email'])->first();

        if($is_exist)
        {
            $to = $request['email'];

            if($this->is_connected())
            {
                $request_key = $this->generateRandomString();

                $forgotRequestKey = ForgotPasswordRequestKey::create(
                    [
                        'email' => $request['email'],
                        'request_key' => $request_key
                    ]
                );

                if($this->is_connected())
                {
                    Mail::to($to)->queue(new ForgotPasswordEmail($request_key));
                }
            }
            else
            {
                return redirect()->back()->withErrors('System Cannot connect to the Internet! Please check your Network.');
            }
        }
        else
        {
            return redirect()->back()->withErrors('Email Address not found!');
        }

        return redirect()->back()->with('with_success', "Please click the link sent to your Email!");
    }

    public function verifyForgotPassword($request_key)
    {
        $forgotRequestKey = ForgotPasswordRequestKey::where('request_key', $request_key)->first();
        $user = User::where('email', $forgotRequestKey->email)->first();

        $request['password'] = $this->generatePassword();

        $password = Hash::make($request['password']);
        $user->update(['password' => $password]);

        return redirect()->route('successful.verify.forgot.password', [
            'userId' => $user->id,
            'password' => $request['password'],
            'request_key' => $request_key]);
    }

    public function successfulForgotPassword($userId, $password, $request_key)
    {
        $user = User::where('id', $userId)->first();
        $password = $password;

        $forgotRequestKey = ForgotPasswordRequestKey::where('request_key', $request_key)->first();

        if($forgotRequestKey)
        {
            $forgotRequestKey->delete();
        }
        else
        {
            return "<h3>Unauthorized Request.</h3>";
        }

        return view('pages.admin.users.reset-password',compact('password'));
    }

    public function generatePassword($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
    }

    public function generateRandomString($length = 60)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
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
