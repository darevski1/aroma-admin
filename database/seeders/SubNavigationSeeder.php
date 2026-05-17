<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class SubNavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subNavigations = [
            [
                'id' => 2,
                'sub_name' => 'Мрежни инсталации и одржување на мрежи',
                'slug' => 'mrezni-instalacii-i-odrzuvanje-na-mrezi',
                'published' => 1,
                'navigation_id' => 3,
                'created_at' => '2025-10-03 20:23:16',
                'updated_at' => '2026-05-15 16:23:31',
            ],
            [
                'id' => 3,
                'sub_name' => 'Одржување на компјутерски системи',
                'slug' => 'odrzuvanje-na-kompjuterski-sistemi',
                'published' => 1,
                'navigation_id' => 3,
                'created_at' => '2025-10-03 20:56:12',
                'updated_at' => '2026-05-15 16:20:22',
            ],
            [
                'id' => 4,
                'sub_name' => 'Видео надзор и контрола на пристап',
                'slug' => 'video-nadzor-i-kontrola-na-pristap',
                'published' => 1,
                'navigation_id' => 3,
                'created_at' => '2025-10-03 20:56:23',
                'updated_at' => '2026-05-15 16:19:58',
            ],
            [
                'id' => 5,
                'sub_name' => 'Сервис и продажба на принтери',
                'slug' => 'servis-i-prodazba-na-printeri',
                'published' => 1,
                'navigation_id' => 3,
                'created_at' => '2025-10-03 20:56:33',
                'updated_at' => '2026-05-15 16:19:37',
            ],
        ];

        DB::table('sub_navigations')->insert($subNavigations);
    }
}