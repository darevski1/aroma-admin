<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Seed roles with fixed IDs: 1 = user, 2 = admin.
     */
    public function run(): void
    {
        // Reset the table so IDs always start at 1,
        // even when re-seeding without migrate:fresh.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('roles')->insert([
            ['id' => 1, 'user_role' => 'user'],
            ['id' => 2, 'user_role' => 'admin'],
        ]);
    }
}
