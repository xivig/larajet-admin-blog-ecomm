<?php

namespace App\Repositories\Contracts;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Post;

    public function findBySlug(string $slug): ?Post;

    public function create(array $data): Post;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function getPublished(int $limit = 10): Collection;
}
