<?php

declare(strict_types=1);

namespace LcfDev\Tweaks\Tweaks;

use LcfDev\Tweaks\Contracts\Tweak;
use LcfDev\Tweaks\Tweaks\Performance\DashiconsDisabler;
use LcfDev\Tweaks\Tweaks\Performance\EmojiDisabler;
use LcfDev\Tweaks\Tweaks\Security\GenericLoginErrors;
use LcfDev\Tweaks\Tweaks\Security\UserEnumerationBlocker;
use LcfDev\Tweaks\Tweaks\Security\XmlRpcDisabler;

final class Catalog
{
    /**
     * @return list<Tweak>
     */
    public function all(): array
    {
        return [
            new EmojiDisabler(),
            new DashiconsDisabler(),
            new XmlRpcDisabler(),
            new UserEnumerationBlocker(),
            new GenericLoginErrors(),
        ];
    }
}
