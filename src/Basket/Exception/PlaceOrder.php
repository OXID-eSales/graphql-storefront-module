<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;

final class PlaceOrder extends Error
{
    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

    public static function byBasketId(string $id, string $status): self
    {
        return new self(vsprintf('Place order for user basket id: %s has status %s', [$id, $status]));
    }

    public static function emptyBasket(string $id): self
    {
        return new self(sprintf('Order cannot be placed. Basket with id: %s is empty', $id));
    }

    public static function notAcceptedTOS(string $id): self
    {
        return new self(sprintf('Terms of service were not accepted for basket with id: %s', $id));
    }

    public static function productsNotOrdarable(): self
    {
        return new self('Some products are not orderable');
    }
}
