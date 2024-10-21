<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Dcblogdev\MsGraph\Models\MsGraphToken;

class NewMicrosoft365SignInListener
{
    public function handle($event)
    {
        $tokenId = $event->token['token_id'];
        $token = MsGraphToken::find($tokenId)->first();

        $user = User::query()
            ->where('email',$event->token['info']['mail'])
            ->orwhere('email',$event->token['info']['userPrincipalName'])
            ->first();

        // check if user is already exists
        if($user)
        {
            // update MsGraphToken
            MsGraphToken::findOrfail($tokenId)->update([
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            Auth::login($user);
        }
        else
        {
            // delete MsGraphToken if not registered
            MsGraphToken::findOrfail($tokenId)->delete();
            abort(redirect('unauthorized'));
        }
    }
}
