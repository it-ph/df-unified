<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dcblogdev\MsGraph\Facades\MsGraph;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function connect()
    {
        return MsGraph::connect();
    }

    public function logout()
    {
        return MsGraph::disconnect('/');
    }
}
