<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facades\App\Http\Helpers\CredentialsHelper;

class Role
{
    public function thecredentials()
    {
        return CredentialsHelper::get_set_credentials();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = $this->thecredentials();
        foreach($roles as $role) {
            if(in_array($role ,$user['roles'])) {
                return $next($request);
            }
        }

        return redirect('404');
    }
}
