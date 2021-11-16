<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use OxidEsales\Eshop\Application\Model\Order as EshopOrderModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class OrderCost implements DataType
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
    public function getTotal(): float
    {
        return (float) $this->order->getRawFieldData('oxtotalordersum');
    }

    /**
     * @Field()
     */
    public function getVoucher(): float
    {
        return (float) $this->order->getRawFieldData('oxvoucherdiscount');
    }

    /**
     * @Field()
     */
    public function getDiscount(): float
    {
        return (float) $this->order->getRawFieldData('oxdiscount');
    }

    public static function getModelClass(): string
    {
        return EshopOrderModel::class;
    }
}
