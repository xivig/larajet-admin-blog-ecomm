<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function all(): Collection
    {
        return Post::with(['category', 'user'])->latest()->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['category', 'user'])->latest()->paginate($perPage);
    }

    public function findById(int $id): ?Post
    {
        return Post::with(['category', 'user', 'tag', 'comments'])->find($id);
    }

    public function findBySlug(string $slug): ?Post
    {
        return Post::with(['category', 'user', 'tag', 'comments'])->where('slug', $slug)->first();
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $post = Post::find($id);
        return $post ? $post->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $post = Post::find($id);
        return $post ? (bool) $post->delete() : false;
    }

    public function getPublished(int $limit = 10): Collection
    {
        return Post::where('status', true)->latest()->take($limit)->get();
    }
}
