<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use DateTimeInterface;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class OrderDelivery extends AbstractOrderDataType
{
    /**
     * @Field()
     */
    public function getTrackingNumber(): string
    {
        return (string)$this->order->getTrackCode();
    }

    /**
     * @Field()
     */
    public function getTrackingURL(): string
    {
        return (string)$this->order->getShipmentTrackingUrl();
    }

    /**
     * @Field()
     */
    public function getDispatched(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string)$this->order->getRawFieldData('oxsenddate')
        );
    }
}
