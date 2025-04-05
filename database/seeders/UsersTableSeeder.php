<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Superadmin user
        $userId = Str::uuid();
        DB::table('users')->insert([
            'id' => $userId,
            'UserName' => 'superadmin',
            'FirstName' => 'Super',
            'LastName' => 'Admin',
            'Phonenumber'=> '123',
            'Email' => 'superadmin@starter-kit.com',
            'EmailVerifiedAt' => now(),
            'Password' => Hash::make('superadmin'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);



        $roles = DB::table('roles')->whereNot('name', 'super_admin')->get();
        foreach ($roles as $role) {
            for ($i = 0; $i < 10; $i++) {
                $userId = Str::uuid();
                DB::table('users')->insert([
                    'id' => $userId,
                    'UserName' => $faker->unique()->userName,
                    'FirstName' => $faker->firstName,
                    'LastName' => $faker->lastName,
                    'Email' => $faker->unique()->safeEmail,
                    'EmailVerifiedAt' => now(),
                    'Password' => Hash::make('password'),
                    'PhoneNumber'=> '123',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('model_has_roles')->insert([
                    'role_id' => $role->id,
                    'model_type' => 'App\Models\User',
                    'model_id' => $userId,
                ]);
            }
        }
    }
}

