<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@poms.com'],
            [
                'name' => 'Admin POMS',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Create Owner User
        User::firstOrCreate(
            ['email' => 'owner@poms.com'],
            [
                'name' => 'Owner POMS',
                'password' => Hash::make('password123'),
                'role' => 'owner',
            ]
        );

        $this->call([
            DummyDataSeeder::class,
        ]);
    }
}
