<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class HomepageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            ['name' => 'Hero Section',         'slug' => 'hero',           'is_published' => true, 'sort_order' => 1],
            ['name' => 'Sticky Banner',        'slug' => 'sticky-banner',  'is_published' => true, 'sort_order' => 2],
            ['name' => 'Brand Logos',          'slug' => 'brand-marquee',  'is_published' => true, 'sort_order' => 3],
            ['name' => 'Services',             'slug' => 'services',       'is_published' => true, 'sort_order' => 4],
            ['name' => 'CenoBOTS',             'slug' => 'cenobots',       'is_published' => true, 'sort_order' => 5],
            ['name' => 'Printers',             'slug' => 'printers',       'is_published' => true, 'sort_order' => 6],
            ['name' => 'Client Logos',         'slug' => 'clients',        'is_published' => true, 'sort_order' => 7],
            ['name' => 'Garmin Showcase',      'slug' => 'garmin',         'is_published' => true, 'sort_order' => 8],
            ['name' => 'Call to Action',       'slug' => 'cta',            'is_published' => true, 'sort_order' => 9],
        ];

        foreach ($sections as $section) {
            HomepageSection::updateOrCreate(
                ['slug' => $section['slug']],
                $section
            );
        }
    }
}
