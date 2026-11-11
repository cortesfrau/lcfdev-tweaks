<?php

declare(strict_types=1);

namespace LcfDev\Tweaks;

use LcfDev\Tweaks\Admin\SettingsPage;
use LcfDev\Tweaks\Registry\TweakRegistry;
use LcfDev\Tweaks\Settings\SettingsRepository;
use LcfDev\Tweaks\Support\Context;
use LcfDev\Tweaks\Tweaks\Catalog;

final class Plugin
{
    public function __construct(
        private readonly TweakRegistry $tweakRegistry,
        private readonly SettingsRepository $settings,
        private readonly Context $context
    ) {}

    public static function create(): self
    {
        $registry = new TweakRegistry();

        foreach ((new Catalog())->all() as $tweak) {
            $registry->add($tweak);
        }

        return new self($registry, new SettingsRepository(), new Context());
    }

    public function run(): void
    {
        if (is_admin()) {
            (new SettingsPage($this->tweakRegistry, $this->settings))->register();
        }

        add_action('init', [$this, 'loadTextdomain'], 0);
        add_action('init', [$this, 'registerTweaks'], 0);
    }

    public function loadTextdomain(): void
    {
        load_plugin_textdomain(
            'lcfdev-tweaks',
            false,
            basename(LCFDEV_TWEAKS_PATH) . '/languages'
        );
    }

    public function registerTweaks(): void
    {
        foreach ($this->tweakRegistry->all() as $tweak) {
            $scope = $this->settings->scopeFor($tweak);

            if ($scope === null || !$this->context->matches($scope)) {
                continue;
            }

            $tweak->register($this->context);
        }
    }
}
