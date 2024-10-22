<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserControllerAPI extends Controller
{
    public function getRoles()
    {
        $user = User::with('theroles:id,user_id,name')->findOrFail(auth()->user()->id);
        $roles = [];
        foreach ($user->theroles as $role) {
            array_push($roles, $role->name);
        }

        return $roles;
    }

    public function getAllUsers (Request $request)
    {
        if($request->ajax())
        {
            $users = User::with([
                'theclient:id,name',
                'thesupervisor:id,first_name,last_name',
                'theroles:id,user_id,name'
            ])
            ->select('id','first_name','last_name','email','client_id','supervisor_id','status','last_login_at');

            $roles = $this->getRoles();

            // ADMIN
            if(in_array('admin',$roles))
            {
                $users = $users;
            }
            // MANAGER
            elseif(in_array('manager',$roles))
            {
                $users = $users->clientusers();
            }
            // TEAM LEAD
            else
            {
                $users = $users->supervisors();
            }

            return datatables($users)
                ->editColumn('full_name', (function($value){
                    return ucfirst($value->full_name);
                }))
                ->editColumn('email', (function($value){
                    return strtolower($value->email);
                }))
                ->editColumn('supervisor_id', (function($value){
                    return $value->thesupervisor ? $value->thesupervisor->full_name : '-';
                }))
                ->editColumn('theroles', (function($value){
                    $roles = [];
                    foreach ($value->theroles as $role) {
                        array_push($roles, $role->name);
                    }
                    asort($roles);
                    $theroles = '';
                    foreach ($roles as $role) {
                        $theroles .= '<span class="badge bg-primary badge-roles">'.ucwords($role).'</span> ';
                    }
                    return $theroles;
                }))
                ->editColumn('client_id', (function($value){
                    return $value->theclient ? $value->theclient->name : "";
                }))
                ->editColumn('last_login_at', (function($value){
                    return $value->last_login_at ? date('d-M-y h:i:s a', strtotime($value->last_login_at)) : '-';
                }))
                ->editColumn('status', (function($value){
                    return $value->status == 'active' ? '<span class="text-success"><strong>Active</strong></span>' : '<label class="text-danger"><strong>Inactive</strong></label>';
                }))
                ->addColumn('action', (function($value){
                        return '<button type="button" class="btn btn-warning btn-sm waves-effect waves-light" title="Edit User" onclick=USER.show('.$value->id.')><i class="fas fa-pencil-alt"></i></button>
                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" title="Deactivate User" onclick=USER.destroy('.$value->id.')><i class="fas fa-ban"></i></button>';
                }))
                ->rawColumns(
                [
                    'action',
                ])
                ->escapeColumns([])
                ->make(true);
        }
    }
}
