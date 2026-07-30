<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure;

use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice as WishedPriceDataType;
use TheCodingMachine\GraphQLite\Types\ID;

interface WishedPriceFactoryInterface
{
    public function createWishedPrice(
        string $userId,
        string $userName,
        ID $productId,
        string $currencyName,
        float $price
    ): WishedPriceDataType;
}
