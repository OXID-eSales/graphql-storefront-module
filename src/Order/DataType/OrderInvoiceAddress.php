<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use OxidEsales\Eshop\Application\Model\Order as EshopOrderModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use OxidEsales\GraphQL\Storefront\Address\DataType\AddressInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class OrderInvoiceAddress implements AddressInterface, ShopModelAwareInterface
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
        return (string)$this->order->getRawFieldData('oxbillsal');
    }

    /**
     * @Field()
     */
    public function email(): string
    {
        return (string)$this->order->getRawFieldData('oxbillemail');
    }

    /**
     * @Field()
     */
    public function firstName(): string
    {
        return (string)$this->order->getRawFieldData('oxbillfname');
    }

    /**
     * @Field()
     */
    public function lastName(): string
    {
        return (string)$this->order->getRawFieldData('oxbilllname');
    }

    /**
     * @Field()
     */
    public function company(): string
    {
        return (string)$this->order->getRawFieldData('oxbillcompany');
    }

    /**
     * @Field()
     */
    public function additionalInfo(): string
    {
        return (string)$this->order->getRawFieldData('oxbilladdinfo');
    }

    /**
     * @Field()
     */
    public function street(): string
    {
        return (string)$this->order->getRawFieldData('oxbillstreet');
    }

    /**
     * @Field()
     */
    public function streetNumber(): string
    {
        return (string)$this->order->getRawFieldData('oxbillstreetnr');
    }

    /**
     * @Field()
     */
    public function zipCode(): string
    {
        return (string)$this->order->getRawFieldData('oxbillzip');
    }

    /**
     * @Field()
     */
    public function city(): string
    {
        return (string)$this->order->getRawFieldData('oxbillcity');
    }

    /**
     * @Field()
     */
    public function vatID(): string
    {
        return (string)$this->order->getRawFieldData('oxbillustid');
    }

    /**
     * @Field()
     */
    public function phone(): string
    {
        return (string)$this->order->getRawFieldData('oxbillfon');
    }

    /**
     * @Field()
     */
    public function fax(): string
    {
        return (string)$this->order->getRawFieldData('oxbillfax');
    }

    public function countryId(): ID
    {
        return new ID(
            $this->order->getRawFieldData('oxbillcountryid')
        );
    }

    public function stateId(): ID
    {
        return new ID(
            $this->order->getRawFieldData('oxbillstateid')
        );
    }

    public static function getModelClass(): string
    {
        return EshopOrderModel::class;
    }
}
