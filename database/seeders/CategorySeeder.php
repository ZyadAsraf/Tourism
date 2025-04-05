<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'Name' => 'Historical',
                'Description' => 'Ancient and historical sites',
                'Img' => 'history.jpg',
            ],
            [
                'Name' => 'Natural',
                'Description' => 'Nature spots like parks and beaches',
                'Img' => 'nature.jpg',
            ],
            [
                'Name' => 'Entertainment',
                'Description' => 'Theme parks and fun places',
                'Img' => 'fun.jpg',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['Name' => $category['Name']], // Prevent duplicates
                $category
            );
        }
    }
}
