<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Enums;

enum Scope: string
{
    /** Every request, admin included. */
    case Everywhere = 'everywhere';

    /** Frontend requests, for any visitor. */
    case Frontend = 'frontend';

    /** Frontend requests from visitors who are not logged in. */
    case GuestFrontend = 'guest_frontend';

    public function label(): string
    {
        return match ($this) {
            self::Everywhere => __('Everywhere', 'lcfdev-tweaks'),
            self::Frontend => __('All visitors', 'lcfdev-tweaks'),
            self::GuestFrontend => __('Visitors who are not logged in', 'lcfdev-tweaks'),
        };
    }
}
