<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;

final class BasketItemAmountLimitedStock extends Error
{
    public static function onStockLimitReached() : self
    {
        return new self('Stock limit has been reached, article can not be added to basket');
    }

    public static function limitedAvailability(float $amount): self
    {
        return new self(sprintf('Availability of this item is limited to %d', $amount));
    }
}
