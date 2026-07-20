<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Service;

use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\WishedPriceOutOfBounds;

class WishedPriceInputValidator implements WishedPriceInputValidatorInterface
{
    public function validatePrice(float $price): void
    {
        if ($price <= 0) {
            throw WishedPriceOutOfBounds::byValue($price);
        }
    }
}
