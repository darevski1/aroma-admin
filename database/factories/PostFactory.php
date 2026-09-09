<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->text(200),
            'content' => fake()->paragraphs(5, true),
            'published' => fake()->boolean(80),
            'meta_title' => $title,
            'meta_description' => fake()->text(150),
            'layout' => 'pages.show',
            'position' => fake()->numberBetween(1, 100),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['published' => true]);
    }
}
