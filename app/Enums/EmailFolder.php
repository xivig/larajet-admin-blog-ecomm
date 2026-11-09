<?php

namespace App\Enums;

enum EmailFolder: string
{
    case Inbox = 'inbox';
    case Starred = 'starred';
    case Sent = 'sent';
    case Draft = 'draft';
    case Spam = 'spam';
    case Trash = 'trash';

    public function icon(): string
    {
        return match ($this) {
            self::Inbox => 'bi-inbox',
            self::Starred => 'bi-star',
            self::Sent => 'bi-send',
            self::Draft => 'bi-file-earmark',
            self::Spam => 'bi-exclamation-octagon',
            self::Trash => 'bi-trash',
        };
    }
}
