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
final class OrderInvoiceAddress implements ShopModelAwareInterface
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
        return (string) $this->order->getFieldData('oxbillsal');
    }

    /**
     * @Field()
     */
    public function email(): string
    {
        return (string) $this->order->getFieldData('oxbillemail');
    }

    /**
     * @Field()
     */
    public function firstName(): string
    {
        return (string) $this->order->getFieldData('oxbillfname');
    }

    /**
     * @Field()
     */
    public function lastName(): string
    {
        return (string) $this->order->getFieldData('oxbilllname');
    }

    /**
     * @Field()
     */
    public function company(): string
    {
        return (string) $this->order->getFieldData('oxbillcompany');
    }

    /**
     * @Field()
     */
    public function additionalInfo(): string
    {
        return (string) $this->order->getFieldData('oxbilladdinfo');
    }

    /**
     * @Field()
     */
    public function street(): string
    {
        return (string) $this->order->getFieldData('oxbillstreet');
    }

    /**
     * @Field()
     */
    public function streetNumber(): string
    {
        return (string) $this->order->getFieldData('oxbillstreetnr');
    }

    /**
     * @Field()
     */
    public function zipCode(): string
    {
        return (string) $this->order->getFieldData('oxbillzip');
    }

    /**
     * @Field()
     */
    public function city(): string
    {
        return (string) $this->order->getFieldData('oxbillcity');
    }

    /**
     * @Field()
     */
    public function vatID(): string
    {
        return (string) $this->order->getFieldData('oxbillustid');
    }

    /**
     * @Field()
     */
    public function phone(): string
    {
        return (string) $this->order->getFieldData('oxbillfon');
    }

    /**
     * @Field()
     */
    public function fax(): string
    {
        return (string) $this->order->getFieldData('oxbillfax');
    }

    public function countryId(): ID
    {
        return new ID(
            $this->order->getFieldData('oxbillcountryid')
        );
    }

    public function stateId(): ID
    {
        return new ID(
            $this->order->getFieldData('oxbillstateid')
        );
    }

    public static function getModelClass(): string
    {
        return EshopOrderModel::class;
    }
}
