<?php

namespace Database\Seeders;

use App\Models\Radmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin exists
        if (!Radmin::where('email', 'admin@resourease.com')->exists()) {
            Radmin::create([
                'name' => 'Admin',
                'email' => 'admin@resourease.com',
                'password' => Hash::make('password123'),
            ]);
            $this->command->info('Admin user created successfully.');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
} 