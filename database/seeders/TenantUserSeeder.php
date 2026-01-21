<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TenantUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('tenant')->table('users')->insert([
            // EMPLOYEES
            [
                'name' => 'Suman Das',
                'email' => 'suman.das@codeclouds.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'job_title' => 'QA Engineer',
                'profile_photo_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Karan Malhotra',
                'email' => 'karan.malhotra@codeclouds.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'job_title' => 'DevOps Engineer',
                'profile_photo_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ananya Gupta',
                'email' => 'ananya.gupta@codeclouds.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'job_title' => 'UI/UX Designer',
                'profile_photo_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
