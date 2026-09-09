<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Register all existing public pages in the pages table.
     * These map to the hardcoded Blade views in resources/views/pages/*.
     */
    public function run(): void
    {
        // Each entry maps to a manually-created Blade view in resources/views/pages/*.
        // Slugs must be single-segment (no slashes) to work with the /pages/{slug} route.
        // The layout column points to the Blade view path registered in PageTemplateConfig.
        $pages = [
            [
                'title'            => 'За Нас',
                'slug'             => 'about',
                'content'          => null,
                'meta_title'       => 'За Нас - ITPlus',
                'meta_description' => 'Запознајте го ITPlus, тим за професионални ИТ решенија, инфраструктура, сервис и поддршка за компании и приватни клиенти.',
                'featured_image'   => null,
                'layout'           => 'pages.about.index',
                'route_name'       => 'za-nas',
                'published'        => true,
                'position'         => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'Услуги',
                'slug'             => 'uslugi',
                'content'          => null,
                'meta_title'       => 'Услуги - ITPlus',
                'meta_description' => 'Професионални ИТ услуги – мрежи, одржување, видео надзор, принтери.',
                'featured_image'   => null,
                'layout'           => 'pages.uslugi.index',
                'route_name'       => 'uslugi',
                'published'        => true,
                'position'         => 2,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'Мрежни инсталации и одржување на мрежи',
                'slug'             => 'computer-network',
                'content'          => null,
                'meta_title'       => 'Мрежни инсталации - ITPlus',
                'meta_description' => 'Комплетни решенија за LAN/Wi-Fi мрежи, конфигурација на рутери и стабилна мрежна инфраструктура.',
                'featured_image'   => null,
                'layout'           => 'pages.computer_network.index',
                'route_name'       => 'mrezni-instalacii-i-odrzuvanje-na-mrezi',
                'published'        => true,
                'position'         => 3,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'Одржување на компјутерски системи',
                'slug'             => 'computer-maintenance',
                'content'          => null,
                'meta_title'       => 'Одржување на компјутерски системи - ITPlus',
                'meta_description' => 'Редовно сервисирање, оптимизација и техничка поддршка за компјутери, сервери и деловни ИТ системи.',
                'featured_image'   => null,
                'layout'           => 'pages.computerWorld.index',
                'route_name'       => 'odrzuvanje-na-kompjuterski-sistemi',
                'published'        => true,
                'position'         => 4,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'Видео надзор и контрола на пристап',
                'slug'             => 'access-control',
                'content'          => null,
                'meta_title'       => 'Видео надзор - ITPlus',
                'meta_description' => 'Инсталација на CCTV системи, IP камери и безбедносни решенија за професионална заштита.',
                'featured_image'   => null,
                'layout'           => 'pages.accessControl.index',
                'route_name'       => 'video-nadzor-i-kontrola-na-pristap',
                'published'        => true,
                'position'         => 5,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'Сервис и продажба на принтери',
                'slug'             => 'printer-world',
                'content'          => null,
                'meta_title'       => 'Сервис и продажба на принтери - ITPlus',
                'meta_description' => 'Продажба, сервисирање и одржување на ласерски и inkjet принтери, тонери и резервни делови.',
                'featured_image'   => null,
                'layout'           => 'pages.printerWorld.index',
                'route_name'       => 'servis-i-prodazba-na-printeri',
                'published'        => true,
                'position'         => 6,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'ЦеноБотс',
                'slug'             => 'cenobots',
                'content'          => null,
                'meta_title'       => 'ЦеноБотс - ITPlus',
                'meta_description' => 'AI-автономни роботи за чистење. Паметно, ефикасно и еколошко решение за секој комерцијален простор.',
                'featured_image'   => null,
                'layout'           => 'pages.cennobots.index',
                'route_name'       => 'cennobots',
                'published'        => true,
                'position'         => 7,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'Портфолио',
                'slug'             => 'portfolio',
                'content'          => null,
                'meta_title'       => 'Портфолио - ITPlus',
                'meta_description' => 'Проекти што ја прават технологијата сигурна, брза и видлива.',
                'featured_image'   => null,
                'layout'           => 'pages.portfolio.index',
                'route_name'       => 'portfolio',
                'published'        => true,
                'position'         => 8,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'title'            => 'Контакт',
                'slug'             => 'kontakt',
                'content'          => null,
                'meta_title'       => 'Контакт - ITPlus',
                'meta_description' => 'Контактирајте го ITPlus за понуда, техничка поддршка, ИТ услуги.',
                'featured_image'   => null,
                'layout'           => 'pages.kontakt.index',
                'route_name'       => 'kontakt',
                'published'        => true,
                'position'         => 9,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ];

        foreach ($pages as $page) {
            DB::table('pages')->updateOrInsert(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
