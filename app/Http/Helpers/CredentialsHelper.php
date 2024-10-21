<?php

namespace App\Http\Helpers;

use App\Models\User;

class CredentialsHelper {
    private $credentials;

    /** Getter and Setter */
    public function setCredentials()
    {
        $user = User::with([
            'theroles:id,user_id,name',
            'theclient:id,name'
        ])->findOrFail(auth()->user()->id);
        $roles = [];
        foreach ($user->theroles as $role) {
            array_push($roles, $role->name);
        }
        asort($roles);
        $theroles = '';
        foreach ($roles as $role) {
            $theroles .= !next($roles) ? $role : $role .' | ';
        }

        $userdata = [
                'id'        => $user->id,
                'full_name' => $user->full_name,
                'email'  => strtolower($user->email),
                'roles'     => $roles,
                'client'     => $user->theclient ? ucfirst($user->theclient->name) : '',
                'theroles' => ucwords($theroles)
        ];
        $this->credentials = $userdata;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function get_set_credentials()
    {
        $this->setCredentials();
        $user = $this->getCredentials();

        return $user;
    }

    public function get_developers()
    {
        $developers = User::query()
            ->whereHas('theroles', function ($query) {
                $query->where('name', 'designer');
            })
            // ->developers()
            // ->isactive()
            ->select('id','first_name','last_name','email','client_id','last_login_at','status')
            ->orderBy('email','asc')
            ->get();

        return $developers;
    }

    public function get_developers_client($client_id)
    {
        $developers = User::query()
            ->whereHas('theroles', function ($query) {
                $query->where('name', 'designer');
            })
            ->where('client_id', $client_id)
            ->isactive()
            ->select('id','first_name','last_name','email','client_id','last_login_at','status')
            ->orderBy('email','asc')
            ->get();

        return $developers;
    }

    public function get_devs()
    {
        $developers = User::query()
            ->whereHas('theroles', function ($query) {
                $query->where('name', 'designer');
            })
            ->developers()
            ->select('id','first_name','last_name','email','client_id','status')
            ->orderBy('email','asc')
            ->get();

        return $developers;
    }

    public function get_auditors()
    {
        $auditors = User::query()
            ->whereHas('theroles', function ($query) {
                $query->where('name', 'proofreader');
            })
            // ->auditors()
            // ->isactive()
            ->select('id','first_name','last_name','email','client_id','last_login_at','status')
            ->orderBy('email','asc')
            ->get();

        return $auditors;
    }

    public function get_qcs()
    {
        $auditors = User::query()
            ->whereHas('theroles', function ($query) {
                $query->where('name', 'proofreader');
            })
            ->auditors()
            ->select('id','first_name','last_name','email','client_id','last_login_at','status')
            ->orderBy('email','asc')
            ->get();

        return $auditors;
    }

    public function get_auditors_client($client_id)
    {
        $auditors = User::query()
            ->whereHas('theroles', function ($query) {
                $query->where('name', 'proofreader');
            })
            ->where('client_id', $client_id)
            ->isactive()
            ->select('id','first_name','last_name','email','client_id','last_login_at','status')
            ->orderBy('email','asc')
            ->get();

        return $auditors;
    }
}
