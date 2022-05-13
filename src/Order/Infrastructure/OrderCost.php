<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\Infrastructure;

use OxidEsales\Eshop\Core\Price as EshopPriceModel;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderCost as OrderCostDataType;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderProductBruttoSum;
use stdClass;

final class OrderCost
{
    public function getOrderCurrencyObject(OrderCostDataType $orderCost): stdClass
    {
        /** @var stdClass $currencyObject */
        $currencyObject = $orderCost->getEshopModel()->getOrderCurrency();
        $currencyObject->rate = (float)$orderCost->getEshopModel()->getRawFieldData('oxcurrate');

        return $currencyObject;
    }

    public function getDeliveryCost(OrderCostDataType $orderCost): EshopPriceModel
    {
        return $orderCost->getEshopModel()->getOrderDeliveryPrice();
    }

    public function getPaymentCost(OrderCostDataType $orderCost): EshopPriceModel
    {
        return $orderCost->getEshopModel()->getOrderPaymentPrice();
    }

    public function getProductNetSum(OrderCostDataType $orderCost): EshopPriceModel
    {
        $netSum = (float)$orderCost->getEshopModel()->getRawFieldData('oxtotalnetsum');

        /** @var EshopPriceModel $price */
        $price = oxNew(EshopPriceModel::class);
        $price->setNettoPriceMode();
        $price->setPrice($netSum);

        return $price;
    }

    public function getProductGross(OrderCostDataType $orderCost): OrderProductBruttoSum
    {
        return new OrderProductBruttoSum($orderCost->getEshopModel());
    }
}
