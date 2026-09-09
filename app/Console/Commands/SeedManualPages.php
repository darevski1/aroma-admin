<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;

class SeedManualPages extends Command
{
    protected $signature = 'pages:seed-manual';
    protected $description = 'Seed Page records for all manually created Blade pages in resources/views/pages';

    /**
     * Map of manual pages: slug => config.
     */
    protected array $pages = [
        'about' => [
            'title'      => 'За Нас',
            'layout'     => 'pages.about.index',
            'route_name' => 'za-nas',
            'position'   => 1,
        ],
        'uslugi' => [
            'title'      => 'Услуги',
            'layout'     => 'pages.uslugi.index',
            'route_name' => 'uslugi',
            'position'   => 2,
        ],
        'cennobots' => [
            'title'      => 'ЦеноБотс',
            'layout'     => 'pages.cennobots.index',
            'route_name' => 'cennobots',
            'position'   => 3,
        ],
        'computer-network' => [
            'title'      => 'Мрежни Инсталации',
            'layout'     => 'pages.computer_network.index',
            'route_name' => 'mrezni-instalacii-i-odrzuvanje-na-mrezi',
            'position'   => 4,
        ],
        'computer-world' => [
            'title'      => 'Одржување Компјутери',
            'layout'     => 'pages.computerWorld.index',
            'route_name' => 'odrzuvanje-na-kompjuterski-sistemi',
            'position'   => 5,
        ],
        'access-control' => [
            'title'      => 'Видео Надзор',
            'layout'     => 'pages.accessControl.index',
            'route_name' => 'video-nadzor-i-kontrola-na-pristap',
            'position'   => 6,
        ],
        'printer-world' => [
            'title'      => 'Принтери',
            'layout'     => 'pages.printerWorld.index',
            'route_name' => 'servis-i-prodazba-na-printeri',
            'position'   => 7,
        ],
        'portfolio' => [
            'title'      => 'Портфолио',
            'layout'     => 'pages.portfolio.index',
            'route_name' => 'portfolio',
            'position'   => 8,
        ],
        'kontakt' => [
            'title'      => 'Контакт',
            'layout'     => 'pages.kontakt.index',
            'route_name' => 'kontakt',
            'position'   => 9,
        ],
    ];

    public function handle(): int
    {
        $created = 0;
        $updated = 0;

        foreach ($this->pages as $slug => $config) {
            $page = Page::where('slug', $slug)->first();

            if ($page) {
                // Update layout/route_name if missing (don't overwrite existing data)
                $dirty = false;
                if (empty($page->layout) || $page->layout === 'pages.show') {
                    $page->layout = $config['layout'];
                    $dirty = true;
                }
                if (empty($page->route_name)) {
                    $page->route_name = $config['route_name'];
                    $dirty = true;
                }
                if ($page->position === 0) {
                    $page->position = $config['position'];
                    $dirty = true;
                }
                if ($dirty) {
                    $page->save();
                    $updated++;
                    $this->info("  Updated: {$slug} (ID: {$page->id})");
                } else {
                    $this->line("  Exists:  {$slug} (ID: {$page->id}) – no changes needed");
                }
            } else {
                Page::create([
                    'title'      => $config['title'],
                    'slug'       => $slug,
                    'layout'     => $config['layout'],
                    'route_name' => $config['route_name'],
                    'published'  => true,
                    'position'   => $config['position'],
                ]);
                $created++;
                $this->info("  Created: {$slug}");
            }
        }

        $this->newLine();
        $this->info("Done. Created: {$created}, Updated: {$updated}, Total pages: " . Page::count());

        return Command::SUCCESS;
    }
}
