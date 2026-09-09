<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(3, true),
            'meta_title' => $title,
            'meta_description' => fake()->text(150),
            'layout' => 'pages.show',
            'published' => true,
            'position' => fake()->numberBetween(1, 100),
        ];
    }
}
