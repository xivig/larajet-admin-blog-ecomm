<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository
    ) {
    }

    /**
     * Create a new blog post with automatic slug generation and status handling.
     */
    public function createPost(array $data, ?int $authorId = null): Post
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);
        }

        if ($authorId) {
            $data['user_id'] = $authorId;
        }

        if (!empty($data['status']) && empty($data['published_at'])) {
            $data['published_at'] = Carbon::now();
        }

        return $this->postRepository->create($data);
    }

    /**
     * Update an existing post.
     */
    public function updatePost(int $id, array $data): bool
    {
        if (!empty($data['title']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $this->postRepository->update($id, $data);
    }

    /**
     * Delete a post.
     */
    public function deletePost(int $id): bool
    {
        return $this->postRepository->delete($id);
    }
}
