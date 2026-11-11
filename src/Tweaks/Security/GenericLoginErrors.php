<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Tweaks\Security;

use LcfDev\Tweaks\Contracts\Tweak;
use LcfDev\Tweaks\Enums\Scope;
use LcfDev\Tweaks\Enums\TweakCategory;
use LcfDev\Tweaks\Support\Context;
use WP_Error;
use WP_User;

final class GenericLoginErrors implements Tweak
{
    private const CREDENTIAL_ERRORS = ['invalid_username', 'invalid_email', 'incorrect_password'];

    public function id(): string
    {
        return 'generic-login-errors';
    }

    public function label(): string
    {
        return __('Generic login errors', 'lcfdev-tweaks');
    }

    public function category(): TweakCategory
    {
        return TweakCategory::Security;
    }

    public function description(): string
    {
        return __('The login form shows the same error whether the username exists or not, and the lost password form never says that an account does not exist.', 'lcfdev-tweaks');
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
        add_filter('wp_login_errors', [$this, 'genericLoginErrors']);
        add_action('lostpassword_post', [$this, 'hideUnknownAccount'], PHP_INT_MAX, 2);
    }

    public function genericLoginErrors(WP_Error $errors): WP_Error
    {
        if (array_intersect(self::CREDENTIAL_ERRORS, $errors->get_error_codes()) === []) {
            return $errors;
        }

        foreach (self::CREDENTIAL_ERRORS as $code) {
            $errors->remove($code);
        }

        $errors->add(
            'incorrect_password',
            __('<strong>Error:</strong> The username or password you entered is incorrect.', 'lcfdev-tweaks')
        );

        return $errors;
    }

    public function hideUnknownAccount(WP_Error $errors, WP_User|false $userData): void
    {
        if ($userData !== false || ($GLOBALS['pagenow'] ?? '') !== 'wp-login.php') {
            return;
        }

        if (array_diff($errors->get_error_codes(), ['invalid_email']) !== []) {
            return;
        }

        $redirectTo = !empty($_REQUEST['redirect_to'])
            ? wp_unslash($_REQUEST['redirect_to'])
            : 'wp-login.php?checkemail=confirm';

        wp_safe_redirect($redirectTo);
        exit;
    }
}
