<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('blog:populate-images', function () {
    $images = [
        'admin-dist/img/blog-1-PTDVKJDt.webp',
        'admin-dist/img/blog-2-DbDLCISq.webp',
        'admin-dist/img/blog-3-BCh7ZU5G.webp',
        'admin-dist/img/card-1-DVsHfMAh.webp',
        'admin-dist/img/card-2-BChm1jqb.webp',
        'admin-dist/img/card-3-GvyRdp0l.webp',
        'admin-dist/img/card-horizontal-1-B59MnoQO.webp',
        'admin-dist/img/card-horizontal-2-SY-zgtGt.webp',
        'admin-dist/img/slide-hero-1-DvTkwF07.webp',
        'admin-dist/img/slide-hero-2-CrXQlEOl.webp',
        'admin-dist/img/slide-hero-3-Ckz1jIOd.webp',
        'admin-dist/img/album-1-r1zfpAlg.webp',
        'admin-dist/img/album-2-Cnpqltfz.webp',
        'admin-dist/img/album-3-BvLZBV-Z.webp',
    ];

    // Ensure Picture records exist with these beautiful images
    $pictures = App\Models\Picture::all();
    if ($pictures->isEmpty()) {
        foreach ($images as $idx => $img) {
            App\Models\Picture::create([
                'name' => 'High-Performance Architecture ' . ($idx + 1),
                'link' => $img,
            ]);
        }
        $pictures = App\Models\Picture::all();
    } else {
        foreach ($pictures as $idx => $pic) {
            $img = $images[$idx % count($images)];
            $pic->update(['link' => $img]);
        }
    }

    // Now populate each post with one of these pictures
    $posts = App\Models\Post::all();
    $picCount = $pictures->count();
    foreach ($posts as $idx => $post) {
        $assignedPic = $pictures[$idx % $picCount];
        $post->update(['image_id' => $assignedPic->id]);
    }

    $this->info("Populated {$posts->count()} posts with high-fidelity images across {$picCount} pictures.");
})->purpose('Populate blog post images with elite assets');
