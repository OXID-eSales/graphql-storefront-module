<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Infrastructure;

use OxidEsales\Eshop\Core\Price as EshopPriceModel;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketCost as BasketCostDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketProductBruttoSum;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price;
use stdClass;

final class BasketCost
{
    public function getBasketCurrencyObject(BasketCostDataType $basketCost): stdClass
    {
        return $basketCost->getEshopModel()->getBasketCurrency();
    }

    public function getProductNetSum(BasketCostDataType $basketCost): EshopPriceModel
    {
        $netSum = (float) $basketCost->getEshopModel()->getNettoSum();

        /** @var EshopPriceModel $price */
        $price  = oxNew(EshopPriceModel::class);
        $price->setNettoPriceMode();
        $price->setPrice($netSum);

        return $price;
    }

    public function getProductGross(BasketCostDataType $basketCost): BasketProductBruttoSum
    {
        return new BasketProductBruttoSum($basketCost->getEshopModel());
    }

    public function getDeliveryPrice(BasketCostDataType $basketCost): Price
    {
        /** @phpstan-ignore-next-line */
        $deliveryPrice = $basketCost->getEshopModel()->getBasketDeliveryCost();

        return new Price(
            $deliveryPrice,
            $this->getBasketCurrencyObject($basketCost)
        );
    }
}
