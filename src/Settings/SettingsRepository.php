<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Settings;

use LcfDev\Tweaks\Contracts\Tweak;
use LcfDev\Tweaks\Enums\Scope;

final class SettingsRepository
{
    public const OPTION_NAME = 'lcfdev_tweaks_settings';

    public function scopeFor(Tweak $tweak): ?Scope
    {
        return $this->resolve($tweak, $this->stored()[$tweak->id()] ?? null);
    }

    /**
     * @param array<array-key, mixed> $input
     * @param iterable<Tweak> $tweaks
     * @return array<string, string|null>
     */
    public function normalize(array $input, iterable $tweaks): array
    {
        $settings = [];

        foreach ($tweaks as $tweak) {
            $settings[$tweak->id()] = $this->resolve($tweak, $input[$tweak->id()] ?? null)?->value;
        }

        return $settings;
    }

    /**
     * @return array<array-key, mixed>
     */
    private function stored(): array
    {
        $settings = get_option(self::OPTION_NAME, []);

        return is_array($settings) ? $settings : [];
    }

    private function resolve(Tweak $tweak, mixed $value): ?Scope
    {
        $scope = is_string($value) ? Scope::tryFrom($value) : null;

        return in_array($scope, $tweak->scopes(), true) ? $scope : null;
    }
}
