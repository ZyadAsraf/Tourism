<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Governorate;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            ['Name' => 'Cairo'],
            ['Name' => 'Alexandria'],
            ['Name' => 'Giza'],
            ['Name' => 'Port Said'],
            ['Name' => 'Suez'],
            ['Name' => 'Luxor'],
            ['Name' => 'Aswan'],
            ['Name' => 'Red Sea'],
            ['Name' => 'Dakahlia'],
            ['Name' => 'Beheira'],
            ['Name' => 'Sharqia'],
            ['Name' => 'Minya'],
            ['Name' => 'Qalyubia'],
            ['Name' => 'Sohag'],
            ['Name' => 'Fayoum'],
            ['Name' => 'Assiut'],
            ['Name' => 'Ismailia'],
            ['Name' => 'Gharbia'],
            ['Name' => 'Beni Suef'],
            ['Name' => 'Menoufia'],
            ['Name' => 'Qena'],
            ['Name' => 'Aswan'],
            ['Name' => 'Damietta'],
            ['Name' => 'New Valley'],
            ['Name' => 'Matrouh'],
            ['Name' => 'North Sinai'],
            ['Name' => 'South Sinai'],
        ];

        foreach ($governorates as $governorate) {
            Governorate::create($governorate);
        }
    }
}