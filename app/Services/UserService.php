<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function addUser($data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'username'   => $data['username'],
            'username'   => $data['username'],
            'email'      => $data['email'],
            'password'   => $data['password'],
        ]);
    }
}
