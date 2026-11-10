<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Category;
use App\Models\Picture;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class MagazinePortalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_magazine_portal_renders_all_sections_without_hardcoded_placeholders(): void
    {
        $response = $this->get(route('blog.magazine'));

        $response->assertStatus(200);
        $response->assertDontSee('dist-front/images/image-4.jpg', false);
        $response->assertDontSee('dist-front/images/image-5.jpg', false);

        $sections = [
            'hero',
            'popular',
            'recent',
            'block-posts',
            'matrix-visuals',
            'spotlight',
            'column-posts',
            'deep-dive',
            'business',
            'technology',
            'videos',
            'editors-picks',
            'channels',
        ];

        foreach ($sections as $section) {
            $response->assertSee('data-mag-section="' . $section . '"', false);
        }

        // Verify view data completeness and sidebar caps
        $viewData = $response->getOriginalContent()->getData();
        $this->assertGreaterThanOrEqual(4, $viewData['blockPosts']->count());
        $this->assertGreaterThanOrEqual(4, $viewData['popularStories']->count());
        $this->assertGreaterThanOrEqual(4, $viewData['recentStories']->count());
        $this->assertGreaterThanOrEqual(4, $viewData['matrixPosts']->count());
        $this->assertGreaterThanOrEqual(4, $viewData['visualBriefs']->count());
        $this->assertNotNull($viewData['spotlightMain']);
        $this->assertGreaterThanOrEqual(2, $viewData['spotlightSub']->count());
        $this->assertGreaterThanOrEqual(3, $viewData['columnLeft']->count());
        $this->assertGreaterThanOrEqual(3, $viewData['columnRight']->count());
        $this->assertNotNull($viewData['gridHero']);
        $this->assertGreaterThanOrEqual(4, $viewData['gridStack']->count());
        $this->assertNotNull($viewData['businessHero']);
        $this->assertGreaterThanOrEqual(6, $viewData['businessPosts']->count());
        $this->assertGreaterThanOrEqual(6, $viewData['techPosts']->count());
        $this->assertGreaterThanOrEqual(3, $viewData['videoPosts']->count());
        $this->assertGreaterThanOrEqual(4, $viewData['editorsPicks']->count());

        $this->assertLessThanOrEqual(10, $viewData['sidebarCategories']->count());
        $this->assertLessThanOrEqual(25, $viewData['sidebarTags']->count());
    }

    public function test_magazine_portal_resilient_when_few_published_posts_exist(): void
    {
        // Hide existing posts inside transaction to verify circular fallback for small datasets
        Post::query()->update(['status' => false, 'post_status' => 'draft']);

        $user = User::first() ?? User::factory()->create();
        $category = Category::first() ?? Category::create(['name' => 'Technology', 'status' => true]);
        $tag = Tag::first() ?? Tag::create(['name' => 'Architecture']);
        $picture = Picture::first() ?? Picture::create([
            'name' => 'sample',
            'link' => 'xivig-admin/img/blog-1-PTDVKJDt.webp',
        ]);
        $author = Author::first() ?? Author::create(['user_id' => $user->id, 'nickname' => 'ed-' . Str::random(5)]);

        foreach (range(1, 3) as $idx) {
            Post::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'tag_id' => $tag->id,
                'image_id' => $picture->id,
                'author_id' => $author->id,
                'title' => "Fallback Resilience Dispatch #{$idx}",
                'slug' => 'fallback-resilience-dispatch-' . $idx . '-' . Str::random(4),
                'content' => 'Detailed technical overview across distributed cloud infrastructure and microservices.',
                'excerpt' => 'Compact resilience summary for magazine portal testing.',
                'status' => true,
                'post_status' => 'published',
                'published_at' => now()->subHours($idx),
                'is_featured' => $idx === 1,
                'is_sticky' => $idx === 2,
                'reading_time' => 4,
                'views_count' => 100 + $idx,
            ]);
        }

        $response = $this->get(route('blog.magazine'));
        $response->assertStatus(200);
        $response->assertSee('Fallback Resilience Dispatch #1');

        $viewData = $response->getOriginalContent()->getData();
        $this->assertNotEmpty($viewData['blockPosts']);
        $this->assertNotEmpty($viewData['matrixPosts']);
        $this->assertNotEmpty($viewData['gridStack']);
        $this->assertNotEmpty($viewData['businessPosts']);
        $this->assertNotEmpty($viewData['techPosts']);
        $this->assertNotEmpty($viewData['editorsPicks']);
    }
}
