<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Contracts;

use LcfDev\Tweaks\Enums\Scope;
use LcfDev\Tweaks\Enums\TweakCategory;
use LcfDev\Tweaks\Support\Context;

interface Tweak
{
    public function id(): string;

    public function label(): string;

    public function category(): TweakCategory;

    public function description(): string;

    /**
     * @return non-empty-list<Scope> The first one is the default.
     */
    public function scopes(): array;

    public function register(Context $context): void;
}
