<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Scope query to apply key-value filters dynamically.
     */
    public function scopeFilter(Builder $query, Request $request, array $allowedFilters = []): Builder
    {
        foreach ($allowedFilters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        return $query;
    }
}
