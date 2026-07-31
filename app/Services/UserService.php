<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new user in the system.
     *
     * @param  array<string, mixed>  $data
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    /**
     * Update an existing user.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateUser(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user->fresh();
    }
}
