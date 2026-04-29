<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'        => 'Admin',
            'email'       => 'admin@shoplocal.com',
            'password'    => Hash::make('password123'),
            'role'        => 'admin',
            'is_approved' => true,
        ]);

        echo "Admin created: admin@shoplocal.com / password123\n";
    }
}