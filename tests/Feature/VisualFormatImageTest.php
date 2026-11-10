<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VisualFormatImageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_default_visual_format_image_page_renders_elite_experience(): void
    {
        $response = $this->get(route('blog.format.image'));

        $response->assertStatus(200);
        $response->assertDontSee('Elite Design Collective', false);
        $response->assertDontSee("alert('URL copied!')", false);

        foreach (['telemetry', 'editions', 'author', 'comments', 'related'] as $section) {
            $response->assertSee('data-vf-section="' . $section . '"', false);
        }

        $response->assertSee('id="vfLightbox"', false);
        $response->assertSee('vf-hero-breadcrumbs', false);
        $response->assertSee('id="vfZenToggle"', false);
        $response->assertSee('id="vfProgressBar"', false);
        $response->assertSee('id="vfToast"', false);

        $viewData = $response->getOriginalContent()->getData();
        $this->assertArrayHasKey('visualTelemetry', $viewData);
        $this->assertNotEmpty($viewData['visualTelemetry']['dimensions']);
        $this->assertNotEmpty($viewData['visualTelemetry']['formatted_size']);
        $this->assertGreaterThanOrEqual(2, $viewData['visualFrames']->count());
        $this->assertNotEmpty($viewData['visualSwitcherPosts']);
        $this->assertNotEmpty($viewData['relatedPosts']);

        $this->assertLessThanOrEqual(10, $viewData['sidebarCategories']->count());
        $this->assertLessThanOrEqual(25, $viewData['sidebarTags']->count());
    }

    public function test_specific_slug_visual_format_image_increments_views_and_links_cleanly(): void
    {
        $post = Post::published()->whereNotNull('image_id')->latest('published_at')->firstOrFail();
        $initialViews = (int) $post->views_count;

        $response = $this->get(route('blog.format.image', $post->slug));

        $response->assertStatus(200);
        $response->assertSee($post->title);

        $post->refresh();
        $this->assertSame($initialViews + 1, (int) $post->views_count);
    }
}
