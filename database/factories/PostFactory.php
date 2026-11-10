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
        $isPublished = fake()->boolean(85);
        $content = fake()->paragraphs(4, true);

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'category_id' => Category::inRandomOrder()->value('id') ?? Category::factory(),
            'tag_id' => Tag::inRandomOrder()->value('id') ?? Tag::factory(),
            'image_id' => Picture::inRandomOrder()->value('id') ?? Picture::factory(),
            'author_id' => Author::inRandomOrder()->value('id') ?? Author::factory(),
            'title' => fake()->sentence(8),
            'slug' => fake()->unique()->slug(4),
            'content' => $content,
            'excerpt' => fake()->sentence(15),
            'status' => $isPublished,
            'post_status' => $isPublished ? 'published' : 'draft',
            'published_at' => $isPublished ? fake()->dateTimeBetween('-30 days', 'now') : null,
            'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($content)) / 200)),
            'views_count' => $isPublished ? fake()->numberBetween(18, 1450) : 0,
            'is_featured' => $isPublished ? fake()->boolean(25) : false,
        ];
    }
}
