<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create default admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@pancaindra.com'],
            [
                'name' => 'Administrator',
                'role' => 'admin',
                'password' => Hash::make('admin123'), // Change this password after first login
                'has_imported' => false,
                'has_viewed_details' => false,
            ]
        );
    }
}
