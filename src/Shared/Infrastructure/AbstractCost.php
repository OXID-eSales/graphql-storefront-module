<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Infrastructure;

use OxidEsales\Eshop\Core\Price as EshopPriceModel;

abstract class AbstractCost
{
    public function getNetPriceObject(float $netSum): EshopPriceModel
    {
        /** @var EshopPriceModel $price */
        $price = oxNew(EshopPriceModel::class);
        $price->setNettoPriceMode();
        $price->setPrice($netSum);

        return $price;
    }
}
