<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure;

use OxidEsales\Eshop\Core\Price as EshopPriceModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice;

final class PriceFactory
{
    public function createPrice(WishedPrice $wishedPrice): Price
    {
        /** @var EshopPriceModel $price */
        $price = oxNew(EshopPriceModel::class);
        $price->setPrice((float) $wishedPrice->getEshopModel()->getRawFieldData('oxprice'));

        return new Price($price);
    }
}
