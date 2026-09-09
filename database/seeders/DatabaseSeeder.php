<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles must exist first: users.user_role has an FK to roles.id.
        $this->call(RolesSeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            AdminUserSeeder::class,
            NavigationSeeder::class,
            SubNavigationSeeder::class,
            PageSeeder::class,
            HomepageSectionSeeder::class,
        ]);
    }
}
