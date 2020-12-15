<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\Service;

use OxidEsales\GraphQL\Storefront\Order\DataType\Order as OrderDataType;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderCost;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderDelivery;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderDeliveryAddress;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderFile;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderInvoiceAddress;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderItem;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderPayment;
use OxidEsales\GraphQL\Storefront\Order\Infrastructure\Order as OrderInfrastructure;
use OxidEsales\GraphQL\Storefront\Voucher\DataType\Voucher;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=OrderDataType::class)
 */
final class OrderRelations
{
    /** @var OrderInfrastructure */
    private $orderInfrastructure;

    public function __construct(
        OrderInfrastructure $orderInfrastructure
    ) {
        $this->orderInfrastructure = $orderInfrastructure;
    }

    /**
     * @Field()
     */
    public function invoiceAddress(OrderDataType $order): OrderInvoiceAddress
    {
        return $this->orderInfrastructure->invoiceAddress($order);
    }

    /**
     * @Field()
     */
    public function deliveryAddress(OrderDataType $order): ?OrderDeliveryAddress
    {
        return $this->orderInfrastructure->deliveryAddress($order);
    }

    /**
     * @Field()
     */
    public function cost(OrderDataType $order): OrderCost
    {
        return new OrderCost($order->getEshopModel());
    }

    /**
     * @Field()
     */
    public function delivery(OrderDataType $order): OrderDelivery
    {
        return $this->orderInfrastructure->delivery($order);
    }

    /**
     * @Field
     *
     * @return Voucher[]
     */
    public function vouchers(OrderDataType $order): array
    {
        return $this->orderInfrastructure->getOrderVouchers($order);
    }

    /**
     * @Field
     *
     * @return OrderItem[]
     */
    public function getItems(OrderDataType $order): array
    {
        return $this->orderInfrastructure->getOrderItems($order);
    }

    /**
     * @Field()
     */
    public function getPayment(OrderDataType $order): ?OrderPayment
    {
        return $this->orderInfrastructure->getOrderPayment($order);
    }

    /**
     * @Field
     *
     * @return OrderFile[]
     */
    public function getFiles(OrderDataType $order): array
    {
        return $this->orderInfrastructure->getOrderFiles($order);
    }
}
