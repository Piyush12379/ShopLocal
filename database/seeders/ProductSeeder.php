<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // First we need a vendor user to own these products
        // Let's create a demo shopkeeper
        $vendorId = DB::table('users')->insertGetId([
            'name'        => 'Mitti Arts',
            'email'       => 'mitti@shoplocal.com',
            'password'    => bcrypt('password123'),
            'role'        => 'shopkeeper',
            'is_approved' => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Get category IDs
        $decor    = DB::table('categories')->where('slug', 'home-decor')->value('id');
        $textile  = DB::table('categories')->where('slug', 'textiles')->value('id');
        $gift     = DB::table('categories')->where('slug', 'gift-sets')->value('id');
        $wellness = DB::table('categories')->where('slug', 'wellness')->value('id');
        $kitchen  = DB::table('categories')->where('slug', 'kitchen')->value('id');
        $jewel    = DB::table('categories')->where('slug', 'jewellery')->value('id');

        $products = [
            // Home Decor
            [
                'name'        => 'Terracotta Planter Set',
                'description' => 'Handcrafted terracotta planters perfect for indoor plants. Set of 3 sizes.',
                'price'       => 849,
                'old_price'   => 1099,
                'stock'       => 23,
                'emoji'       => '🏺',
                'category_id' => $decor,
            ],
            [
                'name'        => 'Copper Wind Chimes',
                'description' => 'Handmade copper wind chimes that produce a soothing melodious sound.',
                'price'       => 449,
                'old_price'   => null,
                'stock'       => 7,
                'emoji'       => '🔔',
                'category_id' => $decor,
            ],
            [
                'name'        => 'Ceramic Candle Holder',
                'description' => 'Hand-painted ceramic candle holder with geometric patterns.',
                'price'       => 349,
                'old_price'   => null,
                'stock'       => 15,
                'emoji'       => '🕯️',
                'category_id' => $decor,
            ],
            // Textiles
            [
                'name'        => 'Block Print Cushion Cover',
                'description' => 'Hand block-printed cotton cushion covers in traditional Rajasthani patterns.',
                'price'       => 599,
                'old_price'   => null,
                'stock'       => 30,
                'emoji'       => '🪑',
                'category_id' => $textile,
            ],
            [
                'name'        => 'Hand-Woven Table Runner',
                'description' => 'Pure cotton hand-woven table runner with natural dyes.',
                'price'       => 699,
                'old_price'   => 899,
                'stock'       => 12,
                'emoji'       => '🧶',
                'category_id' => $textile,
            ],
            // Gift Sets
            [
                'name'        => 'Festival Gift Hamper',
                'description' => 'Curated gift hamper with artisan products — perfect for Diwali and festivals.',
                'price'       => 1299,
                'old_price'   => null,
                'stock'       => 8,
                'emoji'       => '🎁',
                'category_id' => $gift,
            ],
            // Wellness
            [
                'name'        => 'Soy Wax Candle Set',
                'description' => 'Pure soy wax candles with essential oils. Pack of 4 scents.',
                'price'       => 549,
                'old_price'   => null,
                'stock'       => 20,
                'emoji'       => '🌿',
                'category_id' => $wellness,
            ],
            [
                'name'        => 'Herbal Bath Salt',
                'description' => 'Natural herbal bath salts with lavender and rose petals.',
                'price'       => 399,
                'old_price'   => 499,
                'stock'       => 25,
                'emoji'       => '🛁',
                'category_id' => $wellness,
            ],
            // Kitchen
            [
                'name'        => 'Ceramic Mug Set',
                'description' => 'Hand-thrown ceramic mugs with earthy glazes. Set of 2.',
                'price'       => 699,
                'old_price'   => null,
                'stock'       => 0,
                'emoji'       => '☕',
                'category_id' => $kitchen,
            ],
            // Jewellery
            [
                'name'        => 'Silver Jhumka Earrings',
                'description' => 'Traditional silver jhumka earrings with intricate filigree work.',
                'price'       => 1199,
                'old_price'   => 1499,
                'stock'       => 10,
                'emoji'       => '💍',
                'category_id' => $jewel,
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'vendor_id'   => $vendorId,
                'category_id' => $product['category_id'],
                'name'        => $product['name'],
                'description' => $product['description'],
                'price'       => $product['price'],
                'old_price'   => $product['old_price'],
                'stock'       => $product['stock'],
                'emoji'       => $product['emoji'],
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        echo "Products seeded!\n";
    }
}