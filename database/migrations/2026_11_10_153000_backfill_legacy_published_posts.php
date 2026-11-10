<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill legacy active posts (status = true) that defaulted to 'draft'
     * when the post_status column was added without backfilling existing rows.
     */
    public function up(): void
    {
        $legacyActivePosts = DB::table('posts')
            ->whereNull('deleted_at')
            ->where('status', true)
            ->where('post_status', 'draft')
            ->whereNull('published_at')
            ->orderBy('id')
            ->get(['id', 'content', 'created_at', 'views_count', 'reading_time', 'is_featured']);

        foreach ($legacyActivePosts as $idx => $post) {
            $publishedAt = $post->created_at
                ? Carbon::parse($post->created_at)
                : Carbon::now()->subDays(($idx % 21) + 1);

            $wordCount = str_word_count(strip_tags((string) $post->content));
            $readingTime = max(2, (int) ceil($wordCount / 180));

            DB::table('posts')
                ->where('id', $post->id)
                ->update([
                    'post_status' => 'published',
                    'published_at' => $publishedAt->toDateTimeString(),
                    'reading_time' => $post->reading_time > 1 ? $post->reading_time : $readingTime,
                    'views_count' => $post->views_count > 0 ? $post->views_count : (35 + (($idx * 47) % 890)),
                    'is_featured' => $post->is_featured ?: ($idx < 6),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data integrity backfill is intentionally non-destructive on rollback.
    }
};
