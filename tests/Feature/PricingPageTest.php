<?php

namespace Tests\Feature;

use Tests\TestCase;

class PricingPageTest extends TestCase
{
    public function test_pricing_page_renders_tiers_and_light_mode_cta_contrast_rules(): void
    {
        $response = $this->get('/pricing');

        $response->assertStatus(200);
        $response->assertSee('Simple, Transparent Pricing');
        $response->assertSee('Starter');
        $response->assertSee('Professional');
        $response->assertSee('Enterprise');
        $response->assertSee('Tailored Deployments for Global Scale');
        $response->assertSee('text-lg-start', false);

        $compiledCss = file_get_contents(public_path('xivig-public/css/elite-design.css'));
        $this->assertNotFalse($compiledCss);
        $this->assertStringContainsString('pricing-cta-box h3', $compiledCss);
        $this->assertStringContainsString('pricing-card.featured', $compiledCss);
    }
}
