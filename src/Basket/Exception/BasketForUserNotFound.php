<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class BasketForUserNotFound extends NotFound
{
    public function __construct(string $userId, string $title)
    {
        parent::__construct(sprintf('Basket "%s" not found for user "%s"', $title, $userId));
    }
}
