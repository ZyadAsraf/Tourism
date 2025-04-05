<?php

namespace Database\Seeders;

use App\Models\TicketType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['Title'=>'normal','Description'=> 'Desc' , 'DiscountAmount'=>0],
            ['Title'=>'Egyptian','Description'=> 'Desc' , 'DiscountAmount'=>2],
            ['Title'=>'elderly','Description'=> 'Desc' , 'DiscountAmount'=>5],
            ['Title'=>'student','Description'=> 'Desc' , 'DiscountAmount'=>10],

        ];
        foreach ($types as $type) {
            TicketType::create($type);
        }
    }
}