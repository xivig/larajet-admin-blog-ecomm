<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use App\Models\Picture;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'category_id' => Category::inRandomOrder()->value('id') ?? Category::factory(),
            'tag_id' => Tag::inRandomOrder()->value('id') ?? Tag::factory(),
            'image_id' => Picture::inRandomOrder()->value('id') ?? Picture::factory(),
            'author_id' => Author::inRandomOrder()->value('id') ?? Author::factory(),
            'title' => fake()->sentence(8),
            'slug' => fake()->unique()->slug(4),
            'content' => fake()->paragraphs(4, true),
            'excerpt' => fake()->sentence(15),
            'status' => fake()->boolean(85),
        ];
    }
}
