<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\Picture;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class GalleryAndSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Initial Site Settings from Screenshot
        $settings = [
            // Site Maintenance
            'site_maintenance' => ['value' => '0', 'group' => 'maintenance'],

            // External Integrations
            'google_analytics_id' => ['value' => 'G-XXXXXXXXXX', 'group' => 'integrations'],
            'openai_api_key' => ['value' => 'sk-proj-••••••••••••••••••••', 'group' => 'integrations'],
            'mailchimp_api_key' => ['value' => '••••••••••••••••••••-us1', 'group' => 'integrations'],
            'mailchimp_list_id' => ['value' => 'XXXXXXXXXX', 'group' => 'integrations'],

            // Editorial Configuration & Branding
            'gazette_title' => ['value' => 'XIVIG Maison Gazette', 'group' => 'editorial'],
            'gazette_tagline' => ['value' => 'The Haute Couture & Horology Journal', 'group' => 'editorial'],
            'editorial_synopsis' => ['value' => 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics.', 'group' => 'editorial'],
            'editorial_email' => ['value' => 'gazette@xivig.com', 'group' => 'editorial'],
            'stories_per_page' => ['value' => '9', 'group' => 'editorial'],
            'comments_moderation' => ['value' => 'Auto-Approve', 'group' => 'editorial'],
            'system_timezone' => ['value' => 'Europe/Paris', 'group' => 'editorial'],
        ];

        foreach ($settings as $key => $data) {
            Setting::updateOrCreate(['key' => $key], $data);
        }

        // 2. Initial Sample Gallery from Screenshot
        $sampleGallery = Gallery::updateOrCreate(
            ['slug' => 'abc-lpeiY'],
            [
                'title' => 'abc',
                'description' => 'Featured seasonal collection and high-resolution luxury editorial assets.',
                'status' => 'active',
                'created_at' => Carbon::create(2026, 11, 5, 14, 30),
                'updated_at' => Carbon::create(2026, 11, 5, 14, 30),
            ]
        );

        // Fetch pictures from Pictures table or seed 19 items
        $pictures = Picture::all();
        $sampleImages = [];

        if ($pictures->isNotEmpty()) {
            foreach ($pictures as $p) {
                $sampleImages[] = $p->link;
            }
        } else {
            $sampleImages = [
                'xivig-admin/img/1-DZLDOIIi.jpg',
                'xivig-admin/img/2-BNc2f0YF.jpg',
                'xivig-admin/img/3-DE3WfE1M.jpg',
                'xivig-admin/img/4-DTxV8-r7.jpg',
            ];
        }

        // Ensure 19 assets exist for gallery "abc" as in screenshot
        if ($sampleGallery->items()->count() === 0) {
            for ($i = 1; $i <= 19; $i++) {
                $img = $sampleImages[($i - 1) % count($sampleImages)];
                GalleryItem::create([
                    'gallery_id' => $sampleGallery->id,
                    'image_path' => $img,
                    'caption' => 'Curated editorial asset #' . $i,
                    'order' => $i,
                ]);
            }
        }

        // Set cover image
        if (!$sampleGallery->cover_image && $sampleGallery->items()->exists()) {
            $sampleGallery->update([
                'cover_image' => $sampleGallery->items()->first()->image_path,
            ]);
        }
    }
}
