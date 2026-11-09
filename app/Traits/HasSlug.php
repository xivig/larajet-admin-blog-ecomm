<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the HasSlug trait.
     */
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            $sourceField = property_exists($model, 'slugSource') ? $model->slugSource : 'title';

            if (empty($model->slug) && !empty($model->{$sourceField})) {
                $slug = Str::slug($model->{$sourceField});
                $originalSlug = $slug;
                $count = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$originalSlug}-" . ($count++);
                }

                $model->slug = $slug;
            }
        });
    }
}
