<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Exception;

use OxidEsales\GraphQL\Base\Exception\Exists;

final class BasketExists extends Exists
{
    public static function byTitle(string $title): self
    {
        return new self(sprintf("Basket '%s' already exists!", $title));
    }
}
