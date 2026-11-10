<?php

namespace App\Traits;

use App\Models\SEO;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeoMeta
{
    /**
     * Get the SEO metadata for this model.
     */
    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SEO::class, 'seoable');
    }

    /**
     * Update or create SEO metadata.
     */
    public function saveSeoMeta(array $attributes): SEO
    {
        return $this->seoMeta()->updateOrCreate([], $attributes);
    }
}
