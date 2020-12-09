<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class WishedPriceNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Wished price was not found by id: %s', $id));
    }
}
