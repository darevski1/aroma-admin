<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds. Run after PageSeeder so items can be linked to pages.
     */
    public function run(): void
    {
        $services = [
            ['id' => 6, 'title' => 'Мрежни инсталации и одржување на мрежи', 'slug' => 'mrezni-instalacii-i-odrzuvanje-na-mrezi'],
            ['id' => 7, 'title' => 'Одржување на компјутерски системи', 'slug' => 'odrzuvanje-na-kompjuterski-sistemi'],
            ['id' => 8, 'title' => 'Видео надзор и контрола на пристап', 'slug' => 'video-nadzor-i-kontrola-na-pristap'],
            ['id' => 9, 'title' => 'Сервис и продажба на принтери', 'slug' => 'servis-i-prodazba-na-printeri'],
        ];

        $items = [
            ['id' => 1, 'parent_id' => null, 'title' => 'Почетнa', 'slug' => '/'],
            ['id' => 2, 'parent_id' => null, 'title' => 'За Нас', 'slug' => 'za-nas'],
            ['id' => 3, 'parent_id' => null, 'title' => 'Услуги', 'slug' => 'uslugi'],
            ['id' => 4, 'parent_id' => null, 'title' => 'Портфолио', 'slug' => 'portfolio'],
            ['id' => 5, 'parent_id' => null, 'title' => 'Контакт', 'slug' => 'kontakt'],
        ];
        foreach ($services as $service) {
            $items[] = $service + ['parent_id' => 3];
        }

        $position = [];
        $rows = [];
        foreach ($items as $item) {
            $key = $item['parent_id'] ?? 0;
            $position[$key] = ($position[$key] ?? -1) + 1;

            $rows[] = [
                'id' => $item['id'],
                'parent_id' => $item['parent_id'],
                'title' => $item['title'],
                'slug' => $item['slug'],
                'page_id' => Page::where('route_name', $item['slug'])->value('id'),
                'published' => true,
                'position' => $position[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('navigations')->insert($rows);
    }
}
