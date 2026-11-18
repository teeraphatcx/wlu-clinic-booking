<?php

namespace Database\Factories;

use App\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Content>
 */
class ContentFactory extends Factory
{
    protected $model = Content::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake('th_TH')->realText(50);

        return [
            'type' => fake()->randomElement(['news', 'announcement', 'banner', 'faq']),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('####'),
            'content' => fake('th_TH')->paragraphs(3, true),
            'excerpt' => fake('th_TH')->sentence(),
            'image' => null,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
            'published_at' => now(),
            'expired_at' => null,
        ];
    }
}
