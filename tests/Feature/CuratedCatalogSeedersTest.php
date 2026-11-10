<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use Database\Seeders\CategorySeeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\TagSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CuratedCatalogSeedersTest extends TestCase
{
    use DatabaseTransactions;

    public function test_curated_catalog_has_10_meaningful_categories_and_50_realistic_posts(): void
    {
        $this->assertSame(10, Category::count());
        $this->assertSame(10, count(CategorySeeder::catalog()));
        $this->assertSame(25, Tag::count());
        $this->assertSame(25, count(TagSeeder::catalog()));
        $this->assertSame(50, Post::count());
        $this->assertSame(50, Post::published()->count());
        $this->assertSame(50, count(PostSeeder::articles()));
        $this->assertGreaterThan(0, Comment::count());

        foreach (Category::withCount(['posts' => fn ($q) => $q->published()])->get() as $cat) {
            $this->assertSame(5, $cat->posts_count, "Expected 5 published posts in {$cat->name}");
        }

        // Verify key category archive, taxonomy & TOC post detail routes resolve cleanly
        $this->get('/blog/category/technology')->assertStatus(200)->assertSee('Laravel 13');
        $this->get('/blog/tags')->assertStatus(200)->assertSee('#Kubernetes');
        $this->get('/blog-list')->assertStatus(200)->assertSee('Modular Monoliths Over Premature Microservices');

        $firstPost = Post::published()->firstOrFail();
        $this->get(route('blog.single-magazine', $firstPost->slug))
            ->assertStatus(200)
            ->assertSee('Architectural Context');
        $this->get(route('single-post', $firstPost->slug))
            ->assertStatus(200)
            ->assertSee('Architectural Context');
        $this->get(route('blog.format.image', $firstPost->slug))
            ->assertStatus(200)
            ->assertSee('Architectural Context');
        $this->get(route('blog.format.link', $firstPost->slug))
            ->assertStatus(200);
    }
}
