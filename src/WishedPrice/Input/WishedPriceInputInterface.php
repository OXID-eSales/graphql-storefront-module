<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Input;

use TheCodingMachine\GraphQLite\Types\ID;

interface WishedPriceInputInterface
{
    public function getProductId(): ID;

    public function getCurrencyName(): string;

    public function getPrice(): float;
}
