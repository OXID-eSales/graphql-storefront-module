<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use OxidEsales\Eshop\Application\Model\Order as EshopOrderModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class OrderDeliveryAddress implements ShopModelAwareInterface
{
    /** @var EshopOrderModel */
    private $order;

    public function __construct(EshopOrderModel $order)
    {
        $this->order = $order;
    }

    public function getEshopModel(): EshopOrderModel
    {
        return $this->order;
    }

    /**
     * @Field()
     */
    public function salutation(): string
    {
        return (string) $this->order->getRawFieldData('oxdelsal');
    }

    /**
     * @Field()
     */
    public function firstName(): string
    {
        return (string) $this->order->getRawFieldData('oxdelfname');
    }

    /**
     * @Field()
     */
    public function lastName(): string
    {
        return (string) $this->order->getRawFieldData('oxdellname');
    }

    /**
     * @Field()
     */
    public function company(): string
    {
        return (string) $this->order->getRawFieldData('oxdelcompany');
    }

    /**
     * @Field()
     */
    public function additionalInfo(): string
    {
        return (string) $this->order->getRawFieldData('oxdeladdinfo');
    }

    /**
     * @Field()
     */
    public function street(): string
    {
        return (string) $this->order->getRawFieldData('oxdelstreet');
    }

    /**
     * @Field()
     */
    public function streetNumber(): string
    {
        return (string) $this->order->getRawFieldData('oxdelstreetnr');
    }

    /**
     * @Field()
     */
    public function zipCode(): string
    {
        return (string) $this->order->getRawFieldData('oxdelzip');
    }

    /**
     * @Field()
     */
    public function city(): string
    {
        return (string) $this->order->getRawFieldData('oxdelcity');
    }

    /**
     * @Field()
     */
    public function phone(): string
    {
        return (string) $this->order->getRawFieldData('oxdelfon');
    }

    /**
     * @Field()
     */
    public function fax(): string
    {
        return (string) $this->order->getRawFieldData('oxdelfax');
    }

    public function countryId(): ID
    {
        return new ID(
            $this->order->getRawFieldData('oxdelcountryid')
        );
    }

    public function stateId(): ID
    {
        return new ID(
            $this->order->getRawFieldData('oxdelstateid')
        );
    }

    public static function getModelClass(): string
    {
        return EshopOrderModel::class;
    }
}
