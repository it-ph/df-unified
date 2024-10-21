<?php

namespace App\Http\Controllers\Auth;

use Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorCode;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Dcblogdev\MsGraph\Models\MsGraphToken;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // public function username()
    // {
    //     return 'username';
    // }

    protected function authenticated(Request $request, $user)
    {
        $user->update([
            'last_login_at' => Carbon::now()
        ]);

        $user->generateTwoFactorCode();
        $user->notify(new TwoFactorCode());
    }

    // public function logout()
    // {
    //     $is_logged_in = MsGraphToken::query()
    //         ->where('user_id', Auth::user()->id)
    //         ->delete();

    //     if($is_logged_in)
    //     {
    //         Auth::logout();
    //         Session()->flush();

    //         return redirect('login');
    //     }
    // }
}
