<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Home Decor',  'slug' => 'home-decor',  'emoji' => '🏠'],
            ['name' => 'Textiles',    'slug' => 'textiles',    'emoji' => '🧵'],
            ['name' => 'Gift Sets',   'slug' => 'gift-sets',   'emoji' => '🎁'],
            ['name' => 'Wellness',    'slug' => 'wellness',    'emoji' => '🌿'],
            ['name' => 'Jewellery',   'slug' => 'jewellery',   'emoji' => '💍'],
            ['name' => 'Kitchen',     'slug' => 'kitchen',     'emoji' => '🍳'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->insert([
                'name'       => $cat['name'],
                'slug'       => $cat['slug'],
                'emoji'      => $cat['emoji'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "Categories seeded!\n";
    }
}