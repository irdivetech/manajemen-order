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

    /**
     * Delete a user after transferring their orders to the super admin (ID 1).
     *
     * @param  User  $user
     * @return int The number of orders transferred
     */
    public function transferOrdersAndDelete(User $user): int
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
            $ordersCount = $user->orders()->count();
            
            // Ambil admin utama (admin yang paling pertama dibuat)
            $superAdmin = User::where('role', 'admin')->orderBy('id', 'asc')->first();
            $superAdminId = $superAdmin ? $superAdmin->id : User::first()->id;

            if ($ordersCount > 0) {
                // Pindahkan pesanan ke admin utama
                $user->orders()->update(['created_by' => $superAdminId]);
            }

            // Pindahkan juga riwayat tracking agar tidak terjadi foreign key constraint error
            if ($user->trackingHistories()->count() > 0) {
                $user->trackingHistories()->update(['updated_by' => $superAdminId]);
            }

            $user->delete();

            return $ordersCount;
        });
    }
}
