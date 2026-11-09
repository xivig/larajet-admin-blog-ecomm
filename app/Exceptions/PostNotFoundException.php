<?php

namespace App\Exceptions;

class PostNotFoundException extends CmsException
{
    public static function forSlug(string $slug): self
    {
        return new self("Requested article with slug '{$slug}' could not be located or is not yet published.");
    }
}
