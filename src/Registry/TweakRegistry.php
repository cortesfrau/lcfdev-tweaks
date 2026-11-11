<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Registry;

use InvalidArgumentException;
use LcfDev\Tweaks\Contracts\Tweak;
use LcfDev\Tweaks\Enums\TweakCategory;

final class TweakRegistry
{
    /** 
     * @var array<string, Tweak> $tweaks
     */
    private array $tweaks = [];

    public function add(Tweak $tweak): void
    {
        $id = $tweak->id();

        if (isset($this->tweaks[$id])) {
            throw new InvalidArgumentException(
                sprintf('Tweak "%s" is already registered.', $id)
            );
        }

        $this->tweaks[$id] = $tweak;
    }

    /**
     * @return array<string, Tweak>
     */
    public function all(): array
    {
        return $this->tweaks;
    }

    /**
     * @return list<array{category: TweakCategory, tweaks: non-empty-list<Tweak>}>
     */
    public function byCategory(): array
    {
        $groups = [];

        foreach (TweakCategory::cases() as $category) {
            $tweaks = array_values(array_filter(
                $this->tweaks,
                static fn (Tweak $tweak): bool => $tweak->category() === $category
            ));

            if ($tweaks !== []) {
                $groups[] = ['category' => $category, 'tweaks' => $tweaks];
            }
        }

        return $groups;
    }
}
