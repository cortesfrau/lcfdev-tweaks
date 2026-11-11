<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Admin;

use LcfDev\Tweaks\Contracts\Tweak;
use LcfDev\Tweaks\Registry\TweakRegistry;
use LcfDev\Tweaks\Settings\SettingsRepository;

final class SettingsPage
{
    private const PAGE_SLUG = 'lcfdev-tweaks';
    private const OPTION_GROUP = 'lcfdev_tweaks';
    private const CAPABILITY = 'activate_plugins';

    public function __construct(
        private readonly TweakRegistry $registry,
        private readonly SettingsRepository $settings
    ) {}

    public function register(): void
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_filter('option_page_capability_' . self::OPTION_GROUP, [$this, 'capability']);
    }

    public function capability(): string
    {
        return self::CAPABILITY;
    }

    public function registerMenu(): void
    {
        add_options_page(
            __('Tweaks', 'lcfdev-tweaks'),
            __('Tweaks', 'lcfdev-tweaks'),
            self::CAPABILITY,
            self::PAGE_SLUG,
            [$this, 'render']
        );
    }

    public function registerSettings(): void
    {
        register_setting(
            self::OPTION_GROUP,
            SettingsRepository::OPTION_NAME,
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'sanitize'],
                'default'           => [],
            ]
        );

        foreach ($this->registry->byCategory() as ['category' => $category, 'tweaks' => $tweaks]) {
            add_settings_section(
                $category->value,
                $category->label(),
                '__return_false',
                self::PAGE_SLUG
            );

            foreach ($tweaks as $tweak) {
                add_settings_field(
                    $tweak->id(),
                    esc_html($tweak->label()),
                    [$this, 'renderField'],
                    self::PAGE_SLUG,
                    $category->value,
                    [
                        'label_for' => $tweak->id(),
                        'tweak' => $tweak,
                    ]
                );
            }
        }
    }

    /**
     * @param array{label_for: string, tweak: Tweak} $args
     */
    public function renderField(array $args): void
    {
        $tweak = $args['tweak'];
        $id = $tweak->id();
        $scopes = $tweak->scopes();
        $current = $this->settings->scopeFor($tweak);
        $description = $tweak->description();
        $describedBy = $description !== '' ? ' aria-describedby="' . esc_attr($id) . '-description"' : '';

        if (count($scopes) === 1) {
            printf(
                '<input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="%3$s" %4$s%5$s>',
                esc_attr($id),
                esc_attr(SettingsRepository::OPTION_NAME),
                esc_attr($scopes[0]->value),
                checked($current !== null, true, false),
                $describedBy
            );
        } else {
            printf(
                '<select id="%1$s" name="%2$s[%1$s]"%3$s>',
                esc_attr($id),
                esc_attr(SettingsRepository::OPTION_NAME),
                $describedBy
            );

            printf(
                '<option value="" %1$s>%2$s</option>',
                selected($current?->value ?? '', '', false),
                esc_html__('Off', 'lcfdev-tweaks')
            );

            foreach ($scopes as $scope) {
                printf(
                    '<option value="%1$s" %2$s>%3$s</option>',
                    esc_attr($scope->value),
                    selected($current?->value ?? '', $scope->value, false),
                    esc_html($scope->label())
                );
            }

            echo '</select>';
        }

        if ($description !== '') {
            printf(
                '<p class="description" id="%1$s-description">%2$s</p>',
                esc_attr($id),
                esc_html($description)
            );
        }
    }

    /**
     * @return array<string, string|null>
     */
    public function sanitize(mixed $input): array
    {
        return $this->settings->normalize(is_array($input) ? $input : [], $this->registry->all());
    }

    public function render(): void
    {
        if (!current_user_can(self::CAPABILITY)) {
            return;
        }

?>
        <div class="wrap">
            <h1><?php esc_html_e('Tweaks', 'lcfdev-tweaks'); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields(self::OPTION_GROUP);
                do_settings_sections(self::PAGE_SLUG);
                submit_button();
                ?>
            </form>
        </div>
<?php
    }
}
