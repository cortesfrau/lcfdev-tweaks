<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Tweaks\Security;

use LcfDev\Tweaks\Contracts\Tweak;
use LcfDev\Tweaks\Enums\Scope;
use LcfDev\Tweaks\Enums\TweakCategory;
use LcfDev\Tweaks\Support\Context;
use WP_Error;
use WP_REST_Request;

final class UserEnumerationBlocker implements Tweak
{
    private Context $context;

    public function id(): string
    {
        return 'user-enumeration-blocker';
    }

    public function label(): string
    {
        return __('Block user enumeration', 'lcfdev-tweaks');
    }

    public function category(): TweakCategory
    {
        return TweakCategory::Security;
    }

    public function description(): string
    {
        return __('Hides usernames from visitors who are not logged in: ?author=N returns a 404, the REST users endpoint requires login, the users sitemap is removed and the oEmbed author link points to the home page. Author archives are not affected.', 'lcfdev-tweaks');
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
        $this->context = $context;

        add_action('template_redirect', [$this, 'blockAuthorQuery'], 0);
        add_filter('rest_request_before_callbacks', [$this, 'blockUsersEndpoint'], 10, 3);
        add_filter('wp_sitemaps_add_provider', [$this, 'removeUsersSitemap'], 10, 2);
        add_filter('oembed_response_data', [$this, 'removeAuthorUrl']);
    }

    public function blockAuthorQuery(): void
    {
        if (!isset($_GET['author']) || !$this->context->isGuest()) {
            return;
        }

        global $wp_query;

        $wp_query->set_404();
        status_header(404);
        nocache_headers();
    }

    public function blockUsersEndpoint(mixed $response, array $handler, WP_REST_Request $request): mixed
    {
        if (!str_starts_with($request->get_route(), '/wp/v2/users') || !$this->context->isGuest()) {
            return $response;
        }

        return new WP_Error(
            'rest_user_cannot_view',
            __('Sorry, you are not allowed to list users.', 'lcfdev-tweaks'),
            ['status' => rest_authorization_required_code()]
        );
    }

    public function removeUsersSitemap(mixed $provider, string $name): mixed
    {
        return $name === 'users' ? false : $provider;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function removeAuthorUrl(array $data): array
    {
        if (isset($data['author_url'])) {
            $data['author_url'] = home_url('/');
        }

        return $data;
    }
}
