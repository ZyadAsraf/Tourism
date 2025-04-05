<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sid = Str::uuid(); // Generate UUID

        DB::table('super_admins')->insert([
            'id' => $sid, // Insert UUID into the `id` column
            'FirstName' => 'Super',
            'LastName' => 'Admin',
            'PhoneNumber' => '123',
            'Email' => 'superadmins@starter-kit.com',
            'EmailVerifiedAt' => now(),
            'Password' => Hash::make('superadmin'),
            'created_at' => now(),
            'updated_at' => now(),
            'BirthDate' => '1990-01-01'
        ]);
    }
}
