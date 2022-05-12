<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Exception;

use OxidEsales\GraphQL\Base\Exception\OutOfBounds;

use function sprintf;

final class RatingOutOfBounds extends OutOfBounds
{
    public static function byWrongValue(int $rating): self
    {
        return new self(sprintf('Rating must be between 1 and 5, was %s', $rating));
    }
}
