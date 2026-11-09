<?php

namespace App\Exceptions;

class UnauthorizedCmsActionException extends CmsException
{
    public static function forAction(string $action): self
    {
        return new self("You do not possess the required permissions to execute the action: '{$action}'.");
    }
}
