<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Default administrator account.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'darko@darko.mk'],
            [
                'name'      => 'Darko',
                'password'  => Hash::make('password'),
                'user_role' => 2, // admin
                'active'    => true,
            ]
        );
    }
}
