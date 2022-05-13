<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Exception;

use OxidEsales\GraphQL\Base\Exception\OutOfBounds;

use function sprintf;

final class WishedPriceOutOfBounds extends OutOfBounds
{
    public static function byValue(float $value): self
    {
        return new self(sprintf('Wished price must be positive, was: %d', $value));
    }
}
