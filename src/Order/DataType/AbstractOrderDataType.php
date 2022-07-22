<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use OxidEsales\Eshop\Application\Model\Order;

abstract class AbstractOrderDataType
{
    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getEshopModel(): Order
    {
        return $this->order;
    }

    public static function getModelClass(): string
    {
        return Order::class;
    }
}
