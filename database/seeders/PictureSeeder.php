<?php

namespace Database\Seeders;

use App\Models\Picture;
use Illuminate\Database\Seeder;

class PictureSeeder extends Seeder
{
    /**
     * Curated verified local editorial imagery paths.
     *
     * @return array<int, array{name: string, link: string, dimensions: string}>
     */
    public static function localAssets(): array
    {
        return [
            ['name' => 'Cloud Architecture Blueprint', 'link' => 'xivig-admin/img/blog-1-PTDVKJDt.webp', 'dimensions' => '800x500'],
            ['name' => 'Distributed Telemetry Dashboard', 'link' => 'xivig-admin/img/blog-2-DbDLCISq.webp', 'dimensions' => '800x500'],
            ['name' => 'Modern Workspace Engineering', 'link' => 'xivig-admin/img/blog-3-BCh7ZU5G.webp', 'dimensions' => '800x500'],
            ['name' => 'Microservices Control Plane', 'link' => 'xivig-admin/img/card-1-DVsHfMAh.webp', 'dimensions' => '800x400'],
            ['name' => 'Enterprise Platform Visual', 'link' => 'xivig-admin/img/card-2-BChm1jqb.webp', 'dimensions' => '800x400'],
            ['name' => 'Design Tokens System', 'link' => 'xivig-admin/img/card-3-GvyRdp0l.webp', 'dimensions' => '800x400'],
            ['name' => 'System Topology Graph', 'link' => 'xivig-admin/img/card-horizontal-1-B59MnoQO.webp', 'dimensions' => '800x800'],
            ['name' => 'Edge Compute Fabric', 'link' => 'xivig-admin/img/card-horizontal-2-SY-zgtGt.webp', 'dimensions' => '769x799'],
            ['name' => 'Editorial Flagship Banner', 'link' => 'xivig-admin/img/slide-hero-1-DvTkwF07.webp', 'dimensions' => '1600x600'],
            ['name' => 'Neural Mesh Overview', 'link' => 'xivig-admin/img/slide-hero-2-CrXQlEOl.webp', 'dimensions' => '1600x600'],
            ['name' => 'Zero Trust Pipeline', 'link' => 'xivig-admin/img/slide-hero-3-Ckz1jIOd.webp', 'dimensions' => '1600x600'],
            ['name' => 'Visual Photography Plate A', 'link' => 'dist-front/images/architecture.jpg', 'dimensions' => '1200x800'],
            ['name' => 'AI Systems Research Studio', 'link' => 'dist-front/images/ai-research.jpg', 'dimensions' => '1200x800'],
            ['name' => 'Generative Inference Flow', 'link' => 'dist-front/images/ai-art.jpg', 'dimensions' => '800x608'],
            ['name' => 'Enterprise Executive Strategy', 'link' => 'dist-front/images/business-1.jpg', 'dimensions' => '1200x800'],
            ['name' => 'SaaS Telemetry Boardroom', 'link' => 'dist-front/images/business-2.jpg', 'dimensions' => '1200x800'],
            ['name' => 'Capital Markets Briefing', 'link' => 'dist-front/images/business-3.jpg', 'dimensions' => '1200x800'],
            ['name' => 'Modern Product Studio', 'link' => 'dist-front/images/hero-image.jpg', 'dimensions' => '1920x1280'],
            ['name' => 'High-Res Darkroom Plate 1', 'link' => 'dist-front/images/highres-1.jpg', 'dimensions' => '1920x1080'],
            ['name' => 'High-Res Darkroom Plate 2', 'link' => 'dist-front/images/highres-2.jpg', 'dimensions' => '1920x1080'],
            ['name' => 'High-Res Darkroom Plate 3', 'link' => 'dist-front/images/highres-3.jpg', 'dimensions' => '1920x1080'],
            ['name' => 'High-Res Darkroom Plate 4', 'link' => 'dist-front/images/highres-4.jpg', 'dimensions' => '1920x1080'],
            ['name' => 'High-Res Darkroom Plate 5', 'link' => 'dist-front/images/highres-5.jpg', 'dimensions' => '1920x1080'],
            ['name' => 'High-Res Darkroom Plate 6', 'link' => 'dist-front/images/highres-6.jpg', 'dimensions' => '1920x1080'],
            ['name' => 'High-Res Darkroom Plate 7', 'link' => 'dist-front/images/highres-7.jpg', 'dimensions' => '1920x1080'],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::localAssets() as $asset) {
            Picture::firstOrCreate(
                ['link' => $asset['link']],
                [
                    'name' => $asset['name'],
                    'title' => $asset['name'],
                    'alt_text' => $asset['name'],
                    'dimensions' => $asset['dimensions'],
                    'mime_type' => str_ends_with($asset['link'], '.webp') ? 'image/webp' : 'image/jpeg',
                ]
            );
        }
    }
}
