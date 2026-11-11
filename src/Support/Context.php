<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Support;

use LcfDev\Tweaks\Enums\Scope;

final class Context
{
    public function matches(Scope $scope): bool
    {
        return match ($scope) {
            Scope::Everywhere => true,
            Scope::Frontend => $this->isFrontendPage(),
            Scope::GuestFrontend => $this->isFrontendPage() && $this->isGuest(),
        };
    }

    public function isGuest(): bool
    {
        return !is_user_logged_in();
    }

    public function isFrontendPage(): bool
    {
        return !is_admin()
            && !wp_doing_ajax()
            && !wp_doing_cron()
            && !(defined('XMLRPC_REQUEST') && XMLRPC_REQUEST)
            && !(defined('WP_CLI') && WP_CLI)
            && !$this->isRest();
    }

    private function isRest(): bool
    {
        if (isset($_GET['rest_route'])) {
            return true;
        }

        $path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $home = rtrim((string) parse_url(home_url(), PHP_URL_PATH), '/');
        $prefix = trim(rest_get_url_prefix(), '/');

        foreach ([$home . '/' . $prefix, $home . '/index.php/' . $prefix] as $base) {
            if ($path === $base || str_starts_with($path, $base . '/')) {
                return true;
            }
        }

        return false;
    }
}
