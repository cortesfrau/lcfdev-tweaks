<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Tweaks\Performance;

use LcfDev\Tweaks\Contracts\Tweak;
use LcfDev\Tweaks\Enums\Scope;
use LcfDev\Tweaks\Enums\TweakCategory;
use LcfDev\Tweaks\Support\Context;

final class EmojiDisabler implements Tweak
{
    public function id(): string
    {
        return 'emoji-disabler';
    }

    public function label(): string
    {
        return __('Disable Emojis', 'lcfdev-tweaks');
    }

    public function category(): TweakCategory
    {
        return TweakCategory::Performance;
    }

    public function description(): string
    {
        return __('Stops loading the emoji script and styles on the frontend and keeps emojis as plain text in feeds.', 'lcfdev-tweaks');
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
        // Frontend
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_enqueue_scripts', 'wp_enqueue_emoji_styles');

        // Embeds (/embed/)
        remove_action('embed_head', 'print_emoji_detection_script');
        remove_action('enqueue_embed_scripts', 'wp_enqueue_emoji_styles');

        // Feeds
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');

        add_filter('emoji_svg_url', '__return_false');
    }
}
