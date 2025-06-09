<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserType extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = [
            ['user_role' => 'user'],
            ['user_role' => 'admin'],
        ];

        foreach ($userTypes as $userType) {
            \DB::table('roles')->insert($userType);
        }
 
    }
}
