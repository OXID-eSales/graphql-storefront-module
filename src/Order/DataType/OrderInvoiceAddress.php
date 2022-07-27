<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use OxidEsales\Eshop\Application\Model\Order as EshopOrderModel;
use OxidEsales\GraphQL\Storefront\Address\DataType\AbstractAddress;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class OrderInvoiceAddress extends AbstractAddress
{
    private EshopOrderModel $order;

    public function __construct(EshopOrderModel $order)
    {
        $this->order = $order;
        parent::__construct('oxbill');
    }

    public function getEshopModel(): EshopOrderModel
    {
        return $this->order;
    }

    /**
     * @Field()
     */
    public function email(): string
    {
        return $this->getFieldValue('email');
    }

    /**
     * @Field()
     */
    public function vatID(): string
    {
        return $this->getFieldValue('ustid');
    }

    public static function getModelClass(): string
    {
        return EshopOrderModel::class;
    }
}
