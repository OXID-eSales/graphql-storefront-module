<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class BasketNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Basket was not found by id: %s', $id));
    }

    public static function byOwnerAndTitle(string $userId, string $title): self
    {
        return new self(sprintf('Basket "%s" not found for user "%s"', $title, $userId));
    }
}
