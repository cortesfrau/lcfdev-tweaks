<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Enums;

enum TweakCategory: string
{
    case Cleanup = 'cleanup';
    case Performance = 'performance';
    case Security = 'security';

    public function label(): string
    {
        return match ($this) {
            self::Cleanup => __('Cleanup', 'lcfdev-tweaks'),
            self::Performance => __('Performance', 'lcfdev-tweaks'),
            self::Security => __('Security', 'lcfdev-tweaks'),
        };
    }
}
