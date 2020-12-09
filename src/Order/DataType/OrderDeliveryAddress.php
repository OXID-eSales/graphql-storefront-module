<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use OxidEsales\Eshop\Application\Model\Order as EshopOrderModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class OrderDeliveryAddress
{
    /** @var EshopOrderModel */
    private $order;

    public function __construct(EshopOrderModel $order)
    {
        $this->order = $order;
    }

    /**
     * @Field()
     */
    public function salutation(): string
    {
        return (string) $this->order->getFieldData('oxdelsal');
    }

    /**
     * @Field()
     */
    public function firstName(): string
    {
        return (string) $this->order->getFieldData('oxdelfname');
    }

    /**
     * @Field()
     */
    public function lastName(): string
    {
        return (string) $this->order->getFieldData('oxdellname');
    }

    /**
     * @Field()
     */
    public function company(): string
    {
        return (string) $this->order->getFieldData('oxdelcompany');
    }

    /**
     * @Field()
     */
    public function additionalInfo(): string
    {
        return (string) $this->order->getFieldData('oxdeladdinfo');
    }

    /**
     * @Field()
     */
    public function street(): string
    {
        return (string) $this->order->getFieldData('oxdelstreet');
    }

    /**
     * @Field()
     */
    public function streetNumber(): string
    {
        return (string) $this->order->getFieldData('oxdelstreetnr');
    }

    /**
     * @Field()
     */
    public function zipCode(): string
    {
        return (string) $this->order->getFieldData('oxdelzip');
    }

    /**
     * @Field()
     */
    public function city(): string
    {
        return (string) $this->order->getFieldData('oxdelcity');
    }

    /**
     * @Field()
     */
    public function phone(): string
    {
        return (string) $this->order->getFieldData('oxdelfon');
    }

    /**
     * @Field()
     */
    public function fax(): string
    {
        return (string) $this->order->getFieldData('oxdelfax');
    }

    public function countryId(): ID
    {
        return new ID(
            $this->order->getFieldData('oxdelcountryid')
        );
    }

    public function stateId(): ID
    {
        return new ID(
            $this->order->getFieldData('oxdelstateid')
        );
    }
}
