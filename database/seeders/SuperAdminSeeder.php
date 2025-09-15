<?php

namespace Database\Seeders;

use App\Models\Radmin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin if it doesn't exist
        $superAdmin = Radmin::where('email', 'superadmin@assetlab.com')->first();
        
        if (!$superAdmin) {
            Radmin::create([
                'name' => 'Super Administrator',
                'email' => 'superadmin@assetlab.com',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'is_super_admin' => true,
            ]);
            
            $this->command->info('Super admin created successfully!');
            $this->command->info('Email: superadmin@assetlab.com');
            $this->command->info('Password: password123');
        } else {
            $this->command->info('Super admin already exists.');
        }
        
        // Create additional test admins if they don't exist
        $equipmentAdmin = Radmin::where('email', 'equipment@assetlab.com')->first();
        if (!$equipmentAdmin) {
            Radmin::create([
                'name' => 'Equipment Administrator',
                'email' => 'equipment@resourease.com',
                'password' => Hash::make('password123'),
                'role' => 'equipment_admin',
                'is_super_admin' => false,
            ]);
            
            $this->command->info('Equipment admin created successfully!');
        }
        
        $maintenanceAdmin = Radmin::where('email', 'maintenance@resourease.com')->first();
        if (!$maintenanceAdmin) {
            Radmin::create([
                'name' => 'Maintenance Administrator',
                'email' => 'maintenance@resourease.com',
                'password' => Hash::make('password123'),
                'role' => 'maintenance_admin',
                'is_super_admin' => false,
            ]);
            
            $this->command->info('Maintenance admin created successfully!');
        }
    }
}
