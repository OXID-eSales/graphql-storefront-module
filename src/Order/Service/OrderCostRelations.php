<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\Service;

use OxidEsales\GraphQL\Storefront\Currency\DataType\Currency;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderCost;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderProductBruttoSum;
use OxidEsales\GraphQL\Storefront\Order\Infrastructure\OrderCost as OrderCostInfrastructure;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=OrderCost::class)
 */
final class OrderCostRelations
{
    /** @var OrderCostInfrastructure */
    private $orderCostInfrastructure;

    public function __construct(OrderCostInfrastructure $orderCostInfrastructure)
    {
        $this->orderCostInfrastructure = $orderCostInfrastructure;
    }

    /**
     * @Field()
     */
    public function getDelivery(OrderCost $orderCost): Price
    {
        return new Price(
            $this->orderCostInfrastructure->getDeliveryCost($orderCost),
            $this->orderCostInfrastructure->getOrderCurrencyObject($orderCost)
        );
    }

    /**
     * @Field()
     */
    public function getPayment(OrderCost $orderCost): Price
    {
        return new Price(
            $this->orderCostInfrastructure->getPaymentCost($orderCost),
            $this->orderCostInfrastructure->getOrderCurrencyObject($orderCost)
        );
    }

    /**
     * @Field()
     */
    public function getProductNet(OrderCost $orderCost): Price
    {
        return new Price(
            $this->orderCostInfrastructure->getProductNetSum($orderCost),
            $this->orderCostInfrastructure->getOrderCurrencyObject($orderCost)
        );
    }

    /**
     * @Field()
     */
    public function getProductGross(OrderCost $orderCost): OrderProductBruttoSum
    {
        return $this->orderCostInfrastructure->getProductGross($orderCost);
    }

    /**
     * @Field()
     */
    public function getCurrency(OrderCost $orderCost): Currency
    {
        return new Currency($this->orderCostInfrastructure->getOrderCurrencyObject($orderCost));
    }
}
