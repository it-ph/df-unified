<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\RequestSLA;
use App\Models\RequestType;
use App\Models\RequestVolume;
use Illuminate\Support\Facades\View;

class GlobalVariableController extends Controller
{
    public $clients,$users,$request_slas,$request_types,$request_volumes,$tls,$oms;

    public function __construct()
    {
        $this->clients = Client::query()
            ->select('id','name')
            ->orderBy('name', 'ASC')
            ->get();

        $this->users = User::query()
            ->select('id','username','email')
            ->where('status','active')
            ->orderBy('username', 'ASC')
            ->get();

        $this->request_slas = RequestSLA::query()
            ->with([
                'therequesttype:id,name',
                'therequestvolume:id,name',
            ])
            ->select('id','request_type_id','request_volume_id','agreed_sla')
            ->where('status','active')
            ->get();

        $this->request_types = RequestType::query()
            ->select('id','name')
            ->where('status','active')
            ->orderBy('id','ASC')
            ->get();

        $this->request_volumes = RequestVolume::query()
            ->select('id','name')
            ->where('status','active')
            ->orderBy('id','ASC')
            ->get();

        View::share('clients', $this->clients);
        View::share('users', $this->users);
        View::share('request_slas', $this->request_slas);
        View::share('request_types', $this->request_types);
        View::share('request_volumes', $this->request_volumes);
    }
}
