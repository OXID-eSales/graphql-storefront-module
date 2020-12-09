<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Exception;

use OxidEsales\GraphQL\Base\Exception\OutOfBounds;

final class ReviewInputInvalid extends OutOfBounds
{
    public static function byWrongValue(): self
    {
        return new self('Review input cannot have both empty text and rating value.');
    }
}
