<?php

namespace App\Listeners;

use App\Events\PostViewed;
use App\Services\AnalyticsService;

class RecordPostViewListener
{
    public function __construct(protected AnalyticsService $analytics)
    {
    }

    public function handle(PostViewed $event): void
    {
        $this->analytics->recordPostView($event->post, request());
    }
}
