<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Infrastructure;

use OxidEsales\Eshop\Application\Model\Basket as EshopBasketModel;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketProductBruttoSum;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketProductVats;

final class BasketProduct
{
    /**
     * @return BasketProductVats[]
     */
    public function getVats(BasketProductBruttoSum $basketProductGross): array
    {
        /** @var EshopBasketModel $basket */
        $basket = $basketProductGross->getEshopModel();

        $productVats = [];
        $vats = $basket->getProductVats(false);

        foreach ($vats as $vatRate => $vatPrice) {
            $productVats[] = new BasketProductVats((float)$vatRate, (float)$vatPrice);
        }

        return $productVats;
    }
}
