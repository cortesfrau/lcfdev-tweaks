<?php

declare(strict_types=1);

namespace LcfDev\WpTweaks;

final class Plugin
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function run(): void
    {
        // Each tweak registers its own hooks here, e.g. (new Tweaks\Emojis())->register();
    }
}
