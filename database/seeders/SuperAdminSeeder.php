<?php


namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;
class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('super_admins')->insert([
            'id' => 1,
            'FirstName' => 'Super',
            'LastName' => 'Admin',
            'PhoneNumber'=> '123',
            'Email' => 'superadmin@starter-kit.com',
            'email_verified_at' => now(),
            'password' => Hash::make('superadmin'),
            'created_at' => now(),
            'updated_at' => now(),
            'BirthDate' => '1990-01-01'
        ]);


    }
}
