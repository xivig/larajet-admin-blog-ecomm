<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super-admin';
    case Admin = 'admin';
    case Editor = 'editor';
    case Author = 'author';
    case Subscriber = 'subscriber';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Editor => 'Editor',
            self::Author => 'Author',
            self::Subscriber => 'Subscriber',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::SuperAdmin => 'bg-danger-subtle text-danger border border-danger-subtle',
            self::Admin => 'bg-primary-subtle text-primary border border-primary-subtle',
            self::Editor => 'bg-success-subtle text-success border border-success-subtle',
            self::Author => 'bg-info-subtle text-info border border-info-subtle',
            self::Subscriber => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        };
    }
}
