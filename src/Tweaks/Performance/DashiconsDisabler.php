<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Tweaks\Performance;

use LcfDev\Tweaks\Contracts\Tweak;
use LcfDev\Tweaks\Enums\Scope;
use LcfDev\Tweaks\Enums\TweakCategory;
use LcfDev\Tweaks\Support\Context;

final class DashiconsDisabler implements Tweak
{
    public function id(): string
    {
        return 'dashicons-disabler';
    }

    public function label(): string
    {
        return __('Disable Dashicons', 'lcfdev-tweaks');
    }

    public function category(): TweakCategory
    {
        return TweakCategory::Performance;
    }

    public function description(): string
    {
        return __('Stops loading the Dashicons stylesheet on the frontend unless something depends on it, such as the admin bar.', 'lcfdev-tweaks');
    }

    /**
     * @return non-empty-list<Scope>
     */
    public function scopes(): array
    {
        return [Scope::GuestFrontend, Scope::Frontend];
    }

    public function register(Context $context): void
    {
        add_action('wp_enqueue_scripts', [$this, 'dequeue'], PHP_INT_MAX);
        add_action('wp_print_footer_scripts', [$this, 'dequeue'], 1);
    }

    public function dequeue(): void
    {
        wp_dequeue_style('dashicons');
    }
}
