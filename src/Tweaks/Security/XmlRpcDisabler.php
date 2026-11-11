<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Tweaks\Security;

use LcfDev\Tweaks\Contracts\Tweak;
use LcfDev\Tweaks\Enums\Scope;
use LcfDev\Tweaks\Enums\TweakCategory;
use LcfDev\Tweaks\Support\Context;

final class XmlRpcDisabler implements Tweak
{
    public function id(): string
    {
        return 'xmlrpc-disabler';
    }

    public function label(): string
    {
        return __('Disable XML-RPC', 'lcfdev-tweaks');
    }

    public function category(): TweakCategory
    {
        return TweakCategory::Security;
    }

    public function description(): string
    {
        return __('Removes every XML-RPC method, pingbacks included, and the X-Pingback header. Jetpack and some older apps need XML-RPC.', 'lcfdev-tweaks');
    }

    /**
     * @return non-empty-list<Scope>
     */
    public function scopes(): array
    {
        return [Scope::Everywhere];
    }

    public function register(Context $context): void
    {
        add_filter('xmlrpc_enabled', '__return_false');
        add_filter('xmlrpc_methods', '__return_empty_array', PHP_INT_MAX);
        add_filter('wp_headers', [$this, 'removePingbackHeader']);
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    public function removePingbackHeader(array $headers): array
    {
        unset($headers['X-Pingback']);

        return $headers;
    }
}
