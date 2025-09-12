<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
            'email_verified_at' => Carbon::now(),
        ]);

        // Create HR user
        User::create([
            'name' => 'HR',
            'email' => 'hr@admin.com',
            'password' => Hash::make('password'),
            'role' => UserRole::HR->value,
            'status' => UserStatus::ACTIVE->value,
            'email_verified_at' => Carbon::now(),
        ]);
    }
}
