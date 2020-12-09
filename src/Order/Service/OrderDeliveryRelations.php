<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\Service;

use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryProvider;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderDelivery;
use OxidEsales\GraphQL\Storefront\Order\Infrastructure\OrderDelivery as OrderDeliveryInfrastructure;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=OrderDelivery::class)
 */
final class OrderDeliveryRelations
{
    /** @var OrderDeliveryInfrastructure */
    private $orderDeliveryInfrastructure;

    public function __construct(OrderDeliveryInfrastructure $orderDeliveryInfrastructure)
    {
        $this->orderDeliveryInfrastructure = $orderDeliveryInfrastructure;
    }

    /**
     * @Field()
     */
    public function getProvider(OrderDelivery $orderDelivery): DeliveryProvider
    {
        return $this->orderDeliveryInfrastructure->getDeliveryProvider($orderDelivery);
    }
}
