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
        User::factory()->create([
            'name' => 'Admin POMS',
            'email' => 'admin@poms.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create Owner User
        User::factory()->create([
            'name' => 'Owner POMS',
            'email' => 'owner@poms.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        $this->call([
            DummyDataSeeder::class,
        ]);
    }
}
