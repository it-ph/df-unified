<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use App\Notifications\AccountCreated;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToModel, WithHeadingRow,SkipsEmptyRows
{
    private $has_error = array();
    private $row_number = 1;

    public function getRoles()
    {
        $user = User::with('theroles:id,user_id,name')->findOrFail(auth()->user()->id);
        $roles = [];
        foreach ($user->theroles as $role) {
            array_push($roles, $role->name);
        }

        return $roles;
    }

    public function model(array $row)
    {
        $ctr_error = 0;
        array_push($this->has_error,"Something went wrong, Please check all entries that you have encoded.");

        $this->row_number += 1;

        // Define required columns and their labels
        $requiredFields = [
            'first_name' => 'A',
            'last_name' => 'B',
            'email_address' => 'C',
            'roles' => 'D',
            'client' => 'E'
        ];

        // Check if the current user is not admin
        $roles = $this->getRoles();
        $isNotAdmin = !in_array('admin', $roles);
        if ($isNotAdmin) {
            unset($requiredFields['client']);
        }

        foreach ($requiredFields as $field => $column) {
            if (empty($row[$field])) {
                $ctr_error += 1;
                $this->has_error[] = " Check Cell $column".$this->row_number.", $field is required.";
            }
        }

        if ($isNotAdmin)
        {
            $client_id = auth()->user()->client_id;
            $user = User::where([
                'client_id' => $client_id,
                'email' => $row['email_address']
            ])->first();
        }

        if(!$isNotAdmin && !empty($row['client']))
        {
            $client = Client::where('name',$row['client'])->first();
            if (empty($client)) {
                $ctr_error += 1;
                array_push($this->has_error, " Check Cell E".$this->row_number.", Client: ".$row['client']." does not exist.");
            }
            else
            {
                // Find the existing user or prepare for creation
                $client_id = $client->id;
                $user = User::where([
                    'client_id' => $client_id,
                    'email' => $row['email_address']
                ])->first();
            }
        }

        // Define allowed roles
        $allowedRoles = ['manager', 'team lead', 'proofreader', 'designer'];

        // Split the string into an array
        $rolesArray = explode(',', $row['roles']);

        // Convert all array values to lowercase
        $rolesArray = array_map('strtolower', $rolesArray);

        // Trim spaces from each element
        $rolesArray = array_map('trim', $rolesArray);

        // Remove duplicate values
        $rolesArray = array_unique($rolesArray);

        // Filter out any roles that are not in the allowed list
        $rolesArray = array_filter($rolesArray, function($role) use ($allowedRoles) {
            return in_array($role, $allowedRoles);
        });

        $rolesArray = array_values($rolesArray);
        if(!empty($row['roles']))
        {
            if (empty($rolesArray)) {
                $ctr_error += 1;
                array_push($this->has_error, " Check Cell D".$this->row_number.", "."Roles: ".$row['roles']." is invalid.");
            }
        }

        if($ctr_error <= 0)
        {
            // Generate password if the user is not found
            // $password = $this->generatePassword();
            $password = "password";
            $hashedPassword = Hash::make($password);

            // Create or update user
            $user = User::updateOrCreate(
                [
                    'client_id' => $client_id,
                    'email' => $row['email_address']
                ],
                [
                    'username' => strtolower($row['first_name']) . '.' . strtolower($row['last_name']),
                    'first_name' => ucfirst($row['first_name']),
                    'last_name' => ucfirst($row['last_name']),
                    'password' => $user ? $user->password : $hashedPassword, // Use existing password if user exists
                ]
            );

            // Delete existing roles and create new ones
            Role::where('user_id', $user->id)->delete();

            foreach ($rolesArray as $roleName) {
                Role::create([
                    'user_id' => $user->id,
                    'name' => $roleName,
                ]);
            }

            // Check if the user was newly created
            $isNewUser = $user->wasRecentlyCreated; // Check if the user was just created
            if ($isNewUser)
            {
                // Send notification only for new users
                if($this->is_connected())
                {
                    $user->notify(new AccountCreated($password));
                }
            }
        }
    }

    public function getErrors()
    {
        return $this->has_error;
    }

    public function generatePassword($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
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
