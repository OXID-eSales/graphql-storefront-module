<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Service;

use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\WishedPriceOutOfBounds;

interface WishedPriceInputValidatorInterface
{
    /**
     * @throws WishedPriceOutOfBounds
     */
    public function validatePrice(float $price): void;
}
