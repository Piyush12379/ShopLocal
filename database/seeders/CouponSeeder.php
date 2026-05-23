<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::create([
            'code'      => 'SAVE20',
            'type'      => 'percent',
            'value'     => 20,
            'min_order' => 500,
            'max_uses'  => 50,
            'is_active' => true,
        ]);

        Coupon::create([
            'code'      => 'FLAT100',
            'type'      => 'flat',
            'value'     => 100,
            'min_order' => 0,
            'max_uses'  => 100,
            'is_active' => true,
        ]);
    }
}