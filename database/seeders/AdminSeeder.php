<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin already exists
        $adminExists = User::where('email', 'admin@sms.com')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@sms.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);

            echo "✅ Admin user created successfully!\n";
        } else {
            echo "ℹ️  Admin user already exists, skipping...\n";
        }
    }
}
