<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $navigations = [
            [
                'id' => 1,
                'name' => 'Почетнa',
                'slug' => '/',
                'created_at' => '2025-06-09 21:02:21',
                'updated_at' => '2025-10-02 19:25:45',
            ],
            [
                'id' => 2,
                'name' => 'За Нас',
                'slug' => 'za-nas',
                'created_at' => '2025-06-10 11:57:48',
                'updated_at' => '2025-10-02 19:20:04',
            ],
            [
                'id' => 3,
                'name' => 'Услуги',
                'slug' => 'uslugi',
                'created_at' => '2025-06-10 12:34:18',
                'updated_at' => '2025-10-02 18:56:28',
            ],
            [
                'id' => 4,
                'name' => 'Портфолио',
                'slug' => 'portfolio',
                'created_at' => '2025-06-10 12:47:13',
                'updated_at' => '2025-10-02 19:10:47',
            ],
            [
                'id' => 5,
                'name' => 'Контакт',
                'slug' => 'kontakt',
                'created_at' => '2025-06-10 12:47:53',
                'updated_at' => '2025-10-02 19:12:49',
            ],
        ];

        DB::table('navigations')->insert($navigations);
    }
}
