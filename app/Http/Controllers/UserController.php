<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTraits;
use App\Services\UsersServices;
use Illuminate\Support\Facades\Hash;
use App\Notifications\AccountCreated;
use App\Http\Requests\UserStoreRequest;
use Facades\App\Http\Helpers\CredentialsHelper;
use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Controllers\GlobalVariableController;

class UserController extends GlobalVariableController
{
    use ResponseTraits;

    public function __construct()
    {
        parent::__construct();
        $this->service = new UsersServices();
    }

    public function thecredentials()
    {
        return CredentialsHelper::get_set_credentials();
    }

    public function thedevelopers($client_id)
    {
        return CredentialsHelper::get_developers_client($client_id);
    }

    public function theauditors($client_id)
    {
        return CredentialsHelper::get_auditors_client($client_id);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->successResponse('Users loaded successfully!');
        try
        {
            $result["data"] =  $this->service->load();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function store(UserStoreRequest $request)
    {
        $user = $this->thecredentials();
        $result = $this->successResponse('User created successfully!');
        try{
            if($request->edit_id === null)
            {
                // $password = $this->generatePassword();
                $password = "password";
                $request['password'] = Hash::make($password);
                $request['username'] = $request->first_name . '.' . $request->last_name;
                $request['client_id'] = in_array('admin', $user['roles']) ? $request->client_id : auth()->user()->client_id;

                $data = [
                    'username'  => strtolower($request->username),
                    'first_name'     => $request->first_name,
                    'last_name'     => $request->last_name,
                    'email'     => $request->email,
                    'client_id' => $request->client_id,
                    'supervisor_id' => $request->supervisor_id,
                    'password'  => $request->password,
                    'status'    => $request->status
                ];

                $user = User::create($data);

                if($this->is_connected())
                {
                    $user->notify(new AccountCreated($password));
                }

                // delete then create new roles
                Role::where('user_id',$user->id)->delete();
                if ($user) {
                    $role = [];
                    for ($i = 0; $i < count($request->roles); $i++) {
                        $role = [
                            'user_id' => $user->id,
                            'name' => $request->roles[$i],
                        ];
                        Role::create($role);
                    }
                }
            }
            else
            {
                // delete then create new roles
                Role::where('user_id',$request->edit_id)->delete();

                $role = [];
                for ($i = 0; $i < count($request->roles); $i++) {
                    $role = [
                        'user_id' => $request->edit_id,
                        'name'    => $request->roles[$i],
                    ];
                    Role::create($role);
                }

                $request['username'] = $request->first_name . '.' . $request->last_name;
                $request['client_id'] = in_array('admin', $user['roles']) ? $request->client_id : auth()->user()->client_id;
                $result = $this->update(
                    [
                    'username'  => strtolower($request->username),
                    'first_name'     => $request->first_name,
                    'last_name'     => $request->last_name,
                    'email'     => $request->email,
                    'client_id' => $request->client_id,
                    'supervisor_id' => $request->supervisor_id,
                    'status'    => $request->status
                ], $request->edit_id);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function show($id)
    {
        $result = $this->successResponse('User retrieved successfully!');
        try {
            $result["data"] = User::with('theroles:user_id,name')->findOrfail($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function update($data, $id)
    {
        $result = $this->successResponse('User updated successfully!');
        try {
            // $password = $this->generatePassword();
            $password = "password";
            $request['password'] = Hash::make($password);
            $request['username'] = $data['first_name'] . '.' . $data['last_name'];

            $data = [
                'username'  => strtolower($request['username']),
                'first_name'     =>  $data['first_name'],
                'last_name'     =>  $data['last_name'],
                'email'     => $data['email'],
                'client_id' => $data['client_id'],
                'supervisor_id' => $data['supervisor_id'],
                'status'    => $data['status'],
            ];

            $user = User::findOrFail($id);
            $user->update($data);

            // send username and password; if status from deactivated to active
            if($user->wasChanged('status') && $user->status == 'active')
            {
                $user->update(['password' => $request['password']]);
                if($this->is_connected())
                {
                    $user->notify(new AccountCreated($password));
                }
            }

        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrfail($id);
        $result = $this->successResponse('User deactivated successfully!');
        try {
            $user->update(['status' => 'deactivated']);
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function activate($id)
    {
        $user = User::findOrfail($id);
        $result = $this->successResponse('User activated successfully!');
        try {
            $user->update(['status' => 'activate']);
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    private function restore($email)
    {
        $result = $this->successResponse('User Successfully Restored');
        try {
            User::where('email',$email)->restore();
        } catch (\Throwable $th) {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    public function getDevs($client_id)
    {
        $developers = $this->thedevelopers($client_id);

        $devs = [];
        foreach($developers as $developer) {
            if(!$developer->hasActiveJob($developer->id))
            {
                $devs[] = [
                    'id' => $developer->id,
                    'full_name' => $developer->full_name
                ];
            }
        }

        return $devs;
    }

    public function getAuditors($client_id)
    {
        $auditors = $this->theauditors($client_id);

        $qcs = [];
        foreach($auditors as $auditors) {
            $qcs[] = [
                'id' => $auditors->id,
                'full_name' => $auditors->full_name
            ];
        }

        return $qcs;
    }

    public function generatePassword($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
    }

    public function changePassword(UserChangePasswordRequest $request)
    {
        $result = $this->successResponse('Password changed successfully!');
        try {
            User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);

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
